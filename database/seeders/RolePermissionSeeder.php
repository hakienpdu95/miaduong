<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Module;
use App\Models\RolePermission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $permissionsConfig = config('permissions.roles');
        $modules = Module::pluck('name')->toArray();

        foreach ($permissionsConfig as $roleName => $modulePermissions) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) {
                \Log::warning("Vai trò {$roleName} không tồn tại. Bỏ qua.");
                continue;
            }

            foreach ($modulePermissions as $moduleName => $permissions) {
                if (!in_array($moduleName, $modules)) {
                    \Log::warning("Module {$moduleName} không tồn tại. Bỏ qua.");
                    continue;
                }

                // Chỉ tạo mới nếu chưa có bản ghi trong role_permissions
                RolePermission::firstOrCreate(
                    [
                        'role_id' => $role->id,
                        'module_name' => $moduleName,
                    ],
                    array_merge(
                        [
                            'can_view' => false,
                            'can_create' => false,
                            'can_edit' => false,
                            'can_delete' => false,
                        ],
                        $permissions
                    )
                );
            }
        }
    }
}