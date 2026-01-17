<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\Module;
use App\Models\RolePermission;

class SyncRolePermissions extends Command
{
    protected $signature = 'permissions:sync {--force : Ghi đè quyền hiện có}';
    protected $description = 'Đồng bộ quyền vai trò từ config/permissions.php';

    public function handle()
    {
        $force = $this->option('force');
        $permissionsConfig = config('permissions.roles');
        $modules = Module::pluck('name')->toArray();

        foreach ($permissionsConfig as $roleName => $modulePermissions) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) {
                $this->warn("Vai trò {$roleName} không tồn tại. Bỏ qua.");
                continue;
            }

            foreach ($modulePermissions as $moduleName => $permissions) {
                if (!in_array($moduleName, $modules)) {
                    $this->warn("Module {$moduleName} không tồn tại. Bỏ qua.");
                    continue;
                }

                if ($force) {
                    // Ghi đè quyền nếu dùng --force
                    RolePermission::updateOrCreate(
                        [
                            'role_id' => $role->id,
                            'module_name' => $moduleName,
                        ],
                        $permissions
                    );
                } else {
                    // Chỉ tạo mới nếu chưa có
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
                                'can_export' => false,
                                'can_view_report' => false,
                                'can_export_report' => false,
                                'can_assign_permission' => false,
                                'can_approve' => false,
                                'can_reject' => false,
                            ],
                            $permissions
                        )
                    );
                }
            }
        }

        $this->info('Đồng bộ quyền vai trò hoàn tất!');
    }
}