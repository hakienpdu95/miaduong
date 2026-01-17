<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;
use App\Constants\ModuleConst;

class AdminPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->first();
        if (!$admin) {
            $this->command->error('Tài khoản admin không tồn tại!');
            return;
        }

        $modules = ModuleConst::getModules();
        $actions = array_map(fn($action) => "can_{$action}", ModuleConst::getActions());
        $permissionData = array_fill_keys($actions, true);

        foreach ($modules as $module) {
            Permission::updateOrCreate(
                ['user_id' => $admin->id, 'module_name' => $module],
                $permissionData
            );
        }

        $this->command->info('Đã gán full quyền cho tài khoản admin!');
    }
}