<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class InsertUsersDemo extends Command
{
    protected $signature = 'users:insert-demo {count=700 : Number of users to insert} {--truncate : Truncate table before inserting}';
    protected $description = 'Insert random demo users into the users table';

    public function handle()
    {
        $count = (int) $this->argument('count');
        $truncate = $this->option('truncate');
        $this->info("Inserting {$count} demo users...");
        Log::info("Starting users:insert-demo", ['count' => $count, 'truncate' => $truncate]);

        // Khởi tạo Faker
        $faker = Faker::create();
        $ho = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Vũ', 'Đặng', 'Bùi', 'Đỗ'];
        $ten_dem = ['Văn', 'Thị', 'Minh', 'Ngọc', 'Hồng', 'Quang', 'Thu', 'Đức'];
        $ten = ['Anh', 'Bình', 'Cường', 'Duy', 'Hà', 'Hùng', 'Lan', 'Mai', 'Nam', 'Thảo'];

        try {
            // Tạm tắt ràng buộc
            DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
            DB::statement('SET UNIQUE_CHECKS = 0;');
            Log::info('Disabled FOREIGN_KEY_CHECKS and UNIQUE_CHECKS');

            // Xóa bảng nếu có tùy chọn --truncate
            if ($truncate) {
                DB::table('users')->truncate();
                $this->info('Truncated users table.');
                Log::info('Truncated users table.');
            }

            // Chèn dữ liệu theo lô 25 bản ghi
            $chunkSize = 25;
            $chunks = ceil($count / $chunkSize);
            $timestamp = time();
            $insertedCount = 0;

            for ($chunkIndex = 0; $chunkIndex < $chunks; $chunkIndex++) {
                $data = [];
                $recordsInChunk = min($chunkSize, $count - $insertedCount);

                Log::info("Preparing chunk " . ($chunkIndex + 1) . " with {$recordsInChunk} records");

                for ($i = 0; $i < $recordsInChunk; $i++) {
                    $index = $chunkIndex * $chunkSize + $i;
                    $uniqueSuffix = $timestamp . '_' . $index . '_' . Str::random(8);
                    $username = 'user_' . $uniqueSuffix;
                    $email = 'user_' . $uniqueSuffix . '@example.com';
                    $dateTime = $faker->optional(0.7)->dateTimeThisYear();
                    $deletedDateTime = $faker->optional(0.05)->dateTimeThisYear();

                    $data[] = [
                        'name' => $faker->randomElement($ho) . ' ' . $faker->randomElement($ten_dem) . ' ' . $faker->randomElement($ten),
                        'username' => $username,
                        'email' => $email,
                        'password' => Hash::make('password'),
                        'provider' => $faker->optional(0.3)->randomElement(['google', 'facebook', 'github']),
                        'provider_id' => $faker->optional(0.3)->uuid(),
                        'managed_by' => null,
                        'is_active' => $faker->boolean(90) ? 1 : 0,
                        'remember_token' => Str::random(10),
                        'created_at' => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
                        'updated_at' => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
                        'deleted_at' => $deletedDateTime ? $deletedDateTime->format('Y-m-d H:i:s') : null,
                    ];
                }

                // Chèn lô
                DB::table('users')->insert($data);
                $insertedCount += $recordsInChunk;
                $this->info("Inserted chunk " . ($chunkIndex + 1) . " of {$chunks} ({$recordsInChunk} records)");
                Log::info("Inserted chunk " . ($chunkIndex + 1), ['count' => $recordsInChunk, 'total_inserted' => $insertedCount]);
            }

            $totalRecords = DB::table('users')->count();
            $this->info("Successfully inserted {$insertedCount} demo users. Total records in table: {$totalRecords}");
            Log::info("Successfully inserted {$insertedCount} demo users", ['total_records' => $totalRecords]);
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('InsertUsersDemo failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return 1;
        } finally {
            // Bật lại ràng buộc
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
            DB::statement('SET UNIQUE_CHECKS = 1;');
            Log::info('Re-enabled FOREIGN_KEY_CHECKS and UNIQUE_CHECKS');
        }

        return 0;
    }
}