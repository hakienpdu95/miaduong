<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GenerateMigration extends Command
{
    protected $signature = 'migration:generate {--from=render_migration_file.json} {--force : Force delete existing migrations without confirmation}';
    protected $description = 'Generate migrations from JSON file in project root and clean old migrations';

    // Danh sách kiểu dữ liệu hợp lệ
    protected $validColumnTypes = [
        'increments', 'bigIncrements', 'tinyInteger', 'smallInteger', 'mediumInteger', 'integer', 'bigInteger',
        'unsignedTinyInteger', 'unsignedSmallInteger', 'unsignedMediumInteger', 'unsignedInteger', 'unsignedBigInteger',
        'float', 'double', 'decimal', 'string', 'char', 'binary', 'text', 'mediumText', 'longText',
        'date', 'dateTime', 'timestamp', 'time', 'year', 'boolean', 'enum', 'set', 'json'
    ];

    public function handle()
    {
        // Kiểm tra môi trường
        if (!app()->environment('local', 'staging') && !$this->option('force')) {
            $this->error('This command can only run in local or staging environment unless --force is used!');
            return 1;
        }

        // Đọc file JSON từ thư mục gốc dự án
        $jsonPath = base_path($this->option('from'));
        if (!File::exists($jsonPath)) {
            $this->error("File $jsonPath not found!");
            return 1;
        }

        $json = json_decode(File::get($jsonPath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Invalid JSON format in $jsonPath!");
            return 1;
        }

        // Validate JSON schema
        if (!$this->validateJson($json)) {
            $this->error("JSON schema validation failed!");
            return 1;
        }

        // Xóa các file migration cũ
        $migrationPath = database_path('migrations');
        $deletedFiles = $this->cleanMigrations($migrationPath);
        Log::info('Cleaned old migrations', [
            'user' => auth()->user()?->email ?? 'console',
            'deleted_files' => $deletedFiles,
            'time' => now()
        ]);
        $this->info("Deleted $deletedFiles old migration files.");

        // Sắp xếp bảng theo phụ thuộc
        $tables = $this->sortTablesByDependencies($json);
        $template = File::get(database_path('templates/create_base_table.php'));
        $timestamp = Carbon::now();
        $sqlMigrations = [];
        $tableNumber = 0;

        foreach ($tables as $tableData) {
            $tableNumber++;
            $tableInfo = explode('///', array_shift($tableData));
            $tableName = $tableInfo[0];
            $tableDesc = $tableInfo[3] ?: '';

            // Tạo class name từ tên bảng
            $className = 'Create' . Str::studly($tableName) . 'Table';

            // Sinh câu lệnh cột
            $fields = [];
            $indexes = [];
            $initialData = '';
            foreach ($tableData as $field) {
                $parts = explode('///', $field);
                $fieldName = $parts[0];
                $fieldType = $parts[1];
                $fieldParams = $parts[2] !== '__' ? $this->formatParams($fieldType, $parts[2]) : '';
                $fieldNullable = $parts[3] === '_NULL' ? '->nullable()' : '';
                $fieldDefault = $parts[4] !== '__' ? $this->formatDefault($fieldType, $parts[4]) : '';
                $fieldModifier = $parts[5] !== '__' ? $parts[5] : '';
                $fieldComment = $parts[6] !== '__' ? "->comment('{$parts[6]}')" : '';

                if ($fieldName === '__index') {
                    $indexType = $parts[1] === 'fulltext' ? 'fullText' : 'index'; // Hỗ trợ fulltext
                    $indexFields = explode(';', $parts[5]);
                    foreach ($indexFields as $index) {
                        if ($index) {
                            $columns = explode(',', $index);
                            if (count($columns) > 1) {
                                $indexes[] = "\$table->$indexType(['" . implode("','", $columns) . "']);";
                            } else {
                                $indexes[] = "\$table->$indexType('$index');";
                            }
                        }
                    }
                    continue;
                } elseif ($fieldName === '__primary') {
                    $fields[] = "\$table->primary(['" . str_replace(',', "','", $parts[5]) . "']);";
                    continue;
                } elseif ($fieldName === '__initial_data') {
                    // Tách các bản ghi bằng dấu ;
                    $records = explode(';', rtrim($parts[5], ';'));
                    $insertStatements = [];
                    foreach ($records as $record) {
                        if (empty($record)) continue;
                        $data = explode(',', $record);
                        $insertData = [];
                        foreach ($data as $pair) {
                            [$key, $value] = explode(':', $pair, 2);
                            if ($value === 'now') {
                                $value = 'Carbon::now()';
                            } elseif ($key === 'password') {
                                $value = "Hash::make('$value')";
                            } else {
                                $value = "'$value'";
                            }
                            $insertData[] = "'$key' => $value";
                        }
                        // Sử dụng cột 'name' để kiểm tra bản ghi tồn tại (cho bảng roles)
                        $uniqueKey = $tableName === 'roles' ? 'name' : 'email';
                        $uniqueValue = '';
                        foreach ($data as $pair) {
                            [$key, $value] = explode(':', $pair, 2);
                            if ($key === $uniqueKey) {
                                $uniqueValue = "'$value'";
                                break;
                            }
                        }
                        if ($uniqueValue) {
                            $insertStatements[] = "DB::table('$tableName')->updateOrInsert( ['$uniqueKey' => $uniqueValue], [" . implode(',', $insertData) . "] );";
                        }
                    }
                    $initialData = implode("\n        ", $insertStatements);
                    continue;
                }

                // Xử lý khóa ngoại dạng string hoặc unsignedBigInteger
                $params = $fieldParams ? "'$fieldName', $fieldParams" : "'$fieldName'";
                $fieldDefinition = "\$table->$fieldType($params)$fieldNullable$fieldDefault$fieldComment";
                
                // Nếu có modifier liên quan đến khóa ngoại
                if ($fieldModifier !== '__' && strpos($fieldModifier, 'constrained') !== false) {
                    preg_match("/references\('([^']+)'\)->constrained\('([^']+)'\)(.*)/", $fieldModifier, $matches);
                    if (isset($matches[1], $matches[2])) {
                        $referencedColumn = $matches[1];
                        $referencedTable = $matches[2];
                        $extraModifiers = $matches[3] ?? '';
                        $fields[] = $fieldDefinition . ";";
                        $fields[] = "\$table->foreign('$fieldName')->references('$referencedColumn')->on('$referencedTable')$extraModifiers;";
                    } else {
                        $fields[] = $fieldDefinition . $fieldModifier . ";";
                    }
                } else {
                    $fields[] = $fieldDefinition . $fieldModifier . ";";
                }
            }

            // Sinh file migration
            $fileName = $timestamp->format('Y_m_d_His') . sprintf('_%06d_create_%s_table.php', $tableNumber, $tableName);
            $fileContent = str_replace(
                ['__YEAR__', '__MONTH__', '__DAY__', '__HOUR__', '__MINUTE__', '__SECOND__', '__CLASS_NAME__', '__TABLE_NAME__', '__FIELDS__', '__INDEXES__', '__INITIAL_DATA__'],
                [
                    $timestamp->year,
                    str_pad($timestamp->month, 2, '0', STR_PAD_LEFT),
                    str_pad($timestamp->day, 2, '0', STR_PAD_LEFT),
                    str_pad($timestamp->hour, 2, '0', STR_PAD_LEFT),
                    str_pad($timestamp->minute, 2, '0', STR_PAD_LEFT),
                    str_pad($timestamp->second, 2, '0', STR_PAD_LEFT),
                    $className,
                    $tableName,
                    implode("\n            ", $fields),
                    $indexes ? "\n            " . implode("\n            ", $indexes) : '',
                    $initialData ? "\n\n        $initialData" : ''
                ],
                $template
            );

            File::put("$migrationPath/$fileName", $fileContent);
            $sqlMigrations[] = "('$fileName', 0)";
        }

        // Cập nhật bảng migrations
        $sqlFile = "$migrationPath/0000_sql_migrations_update.sql";
        File::put($sqlFile, "INSERT INTO `migrations` (`migration`, `batch`) VALUES " . implode(',', $sqlMigrations) . ";");

        $this->info("Generated $tableNumber migrations successfully!");
        return 0;
    }

    private function formatDefault($fieldType, $value)
    {
        // Nếu là enum hoặc set, xử lý danh sách giá trị
        if (in_array($fieldType, ['enum', 'set']) && preg_match("/^\[(.+)\]$/", $value, $matches)) {
            $values = array_map('trim', explode(',', $matches[1]));
            return "->default('{$values[0]}')";
        }
        // Nếu giá trị là số, boolean, hoặc NULL, giữ nguyên
        if (is_numeric($value) || in_array(strtolower($value), ['true', 'false', 'null'])) {
            return "->default($value)";
        }
        // Nếu là chuỗi, bao trong dấu nháy đơn
        return "->default($value)";
    }

    private function formatParams($fieldType, $params)
    {
        // Loại bỏ dấu ngoặc cho các kiểu thông thường
        $params = str_replace(['(', ')'], '', $params);
        // Nếu là enum hoặc set, xử lý danh sách giá trị
        if (in_array($fieldType, ['enum', 'set']) && preg_match("/^\[(.+)\]$/", $params, $matches)) {
            $values = array_map(fn($val) => "'".trim($val)."'", explode(',', $matches[1]));
            return "[" . implode(',', $values) . "]";
        }
        // Nếu là decimal, xử lý (precision,scale)
        if ($fieldType === 'decimal' && strpos($params, ',') !== false) {
            [$precision, $scale] = explode(',', $params);
            return "$precision, $scale";
        }
        if ($fieldType === 'char' && is_numeric($params)) {
            return $params; // Xử lý độ dài cho char
        }
        return $params;
    }

    private function cleanMigrations($migrationPath)
    {
        if (!File::exists($migrationPath)) {
            File::makeDirectory($migrationPath, 0755, true);
            return 0;
        }

        // Lấy danh sách file trong thư mục migrations
        $files = File::files($migrationPath);
        $deletedCount = 0;
        // Danh sách file được giữ lại
        $keepFiles = ['0000_sql_migrations_update.sql'];

        // Yêu cầu xác nhận trong môi trường non-local, trừ khi có --force
        if (!app()->environment('local') && !$this->option('force')) {
            if (!$this->confirm('This will delete all existing migration files. Continue?')) {
                $this->info('Migration generation cancelled.');
                exit(1);
            }
        }

        foreach ($files as $file) {
            $fileName = $file->getFilename();
            if (in_array($fileName, $keepFiles)) {
                continue;
            }
            File::delete($file->getPathname());
            $deletedCount++;
        }

        return $deletedCount;
    }

    private function validateJson($json)
    {
        $tableNames = array_map(fn($table) => explode('///', $table[0])[0], $json);

        // Kiểm tra trùng lặp tên bảng
        $uniqueTableNames = array_unique($tableNames);
        if (count($uniqueTableNames) !== count($tableNames)) {
            $this->error("Duplicate table names found in JSON!");
            return false;
        }

        foreach ($json as $table) {
            $tableInfo = explode('///', $table[0]);
            if (count($tableInfo) < 4 || !$tableInfo[0]) {
                $this->error('Invalid table header');
                return false;
            }

            foreach (array_slice($table, 1) as $field) {
                $parts = explode('///', $field);
                if (count($parts) < 7) {
                    $this->error("Invalid field definition: $field");
                    return false;
                }

                // Bỏ qua __initial_data, __primary, __index khi validate kiểu dữ liệu
                if (in_array($parts[0], ['__initial_data', '__primary', '__index'])) {
                    continue;
                }

                // Validate kiểu dữ liệu
                if (!in_array($parts[1], $this->validColumnTypes)) {
                    $this->error("Invalid column type: {$parts[1]} in field: $field");
                    return false;
                }

                // Validate tham số cho enum/set
                if (in_array($parts[1], ['enum', 'set']) && ($parts[2] === '__' || !preg_match("/^\[(.+)\]$/", $parts[2]))) {
                    $this->error("Enum/Set type requires a list of values in field: $field");
                    return false;
                }

                // Validate tham số cho decimal
                if ($parts[1] === 'decimal' && ($parts[2] === '__' || !preg_match("/^\d+,\d+$/", $parts[2]))) {
                    $this->error("Decimal type requires precision,scale in field: $field");
                    return false;
                }

                // Validate khóa ngoại cho unsignedBigInteger hoặc string
                if (in_array($parts[1], ['unsignedBigInteger', 'string', 'char']) && $parts[5] !== '__' && strpos($parts[5], 'constrained') !== false) {
                    preg_match("/constrained\('([^']+)'\)/", $parts[5], $matches);
                    if (isset($matches[1]) && !in_array($matches[1], $tableNames)) {
                        $this->error("Invalid foreign key: Table {$matches[1]} does not exist in field: $field");
                        return false;
                    }
                }
            }
        }

        return true;
    }

    private function sortTablesByDependencies($json)
    {
        $dependencies = [];
        $tables = [];

        foreach ($json as $table) {
            $tableInfo = explode('///', $table[0]);
            $tableName = $tableInfo[0];
            $deps = [];

            foreach (array_slice($table, 1) as $field) {
                $parts = explode('///', $field);
                if (in_array($parts[1], ['unsignedBigInteger', 'string', 'char']) && $parts[5] !== '__' && strpos($parts[5], 'constrained') !== false) {
                    preg_match("/constrained\('([^']+)'\)/", $parts[5], $matches);
                    if (isset($matches[1])) {
                        $deps[] = $matches[1];
                    }
                }
            }

            $dependencies[$tableName] = $deps;
            $tables[$tableName] = $table;
        }

        $sorted = [];
        $visited = [];

        $visit = function ($tableName) use (&$visit, &$sorted, &$visited, $dependencies, $tables) {
            if (isset($visited[$tableName])) {
                return;
            }
            $visited[$tableName] = true;
            foreach ($dependencies[$tableName] as $dep) {
                $visit($dep);
            }
            $sorted[] = $tables[$tableName];
        };

        foreach (array_keys($tables) as $tableName) {
            $visit($tableName);
        }

        return $sorted;
    }
}