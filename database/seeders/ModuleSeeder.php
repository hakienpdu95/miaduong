<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Constants\ModuleConst;
use Illuminate\Support\Facades\Cache;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = ModuleConst::getModules();
        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['name' => $module],
                ['name' => $module]
            );
            Cache::forget('modules'); // Xóa cache nếu có
        }
        $this->command->info('Đã đồng bộ danh sách module!');
    }
}