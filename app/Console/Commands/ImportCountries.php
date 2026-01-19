<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportCountries extends Command
{
    protected $signature = 'import:countries {--chunk=1000 : Number of records per chunk} {--force : Force truncate and re-import data}';

    protected $description = 'Import or update countries from JSON file into the database';

    public function handle()
    {
        $this->info('Starting import of countries...');

        // Đường dẫn tới file JSON
        $jsonFile = database_path('country.json');

        // Kiểm tra sự tồn tại của file
        if (!File::exists($jsonFile)) {
            $this->error('JSON file not found.');
            return 1;
        }

        // Đọc và parse dữ liệu từ file JSON
        $jsonData = json_decode(File::get($jsonFile), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON format in country.json.');
            return 1;
        }

        // Lấy dữ liệu countries từ JSON (giả sử là mảng trực tiếp)
        $countries = $jsonData ?? [];

        // Số lượng bản ghi mỗi lần chèn
        $chunkSize = $this->option('chunk');

        // Xóa dữ liệu cũ nếu có tùy chọn --force
        if ($this->option('force')) {
            if ($this->confirm('This will truncate the countries table. Are you sure?')) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                Country::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                $this->info('Table truncated successfully.');
            } else {
                $this->info('Import cancelled.');
                return 1;
            }
        }

        // Chèn dữ liệu countries
        $this->info('Importing countries...');
        $this->importCountries($countries, $chunkSize);

        $this->info('Import completed successfully!');
        return 0;
    }

    protected function importCountries($countries, $chunkSize)
    {
        // Chia nhỏ dữ liệu để chèn theo chunk
        $countryData = collect($countries)->map(function ($item) {
            return [
                'name' => $item['Name'],
                'code' => $item['Code'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->chunk($chunkSize);

        // Chèn dữ liệu theo chunk
        DB::transaction(function () use ($countryData) {
            foreach ($countryData as $chunk) {
                Country::upsert(
                    $chunk->toArray(),
                    ['code'], // Khóa duy nhất để kiểm tra (giả sử code là unique)
                    ['name', 'created_at', 'updated_at']
                );
                $this->info('Inserted/Updated ' . $chunk->count() . ' countries.');
            }
        });
    }
}