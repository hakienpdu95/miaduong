<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Constants\ModuleConst;

/**
 * Seeder to synchronize modules from constants to database.
 */
class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = ModuleConst::getModules(); // Giả sử getModules() trả về array module names từ code trước

        foreach ($modules as $module) {
            // Update or create để tránh duplicate, efficient cho bulk
            Module::updateOrCreate(
                ['name' => $module],
                ['name' => $module] // Có thể thêm fields khác nếu model có
            );
        }

        // Bỏ Cache::forget theo yêu cầu không dùng cache
        $this->command->info('Đã đồng bộ danh sách module!');
    }
}