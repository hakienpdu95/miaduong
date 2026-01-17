<?php
namespace App\Traits;

use App\Constants\ModuleConst;
use Illuminate\Support\Facades\Log;

trait HasPermissions
{
    /**
     * Lưu trữ quyền trong request hiện tại để giảm truy vấn
     *
     * @var array|null
     */
    protected $permissionCache = null;

    public function hasPermission(string $module, string $action): bool
    {
        // Kiểm tra module hợp lệ
        if (!ModuleConst::isValidModule($module)) {
            Log::warning("Module {$module} không tồn tại trong ModuleConst");
            return false;
        }

        // Kiểm tra action hợp lệ
        if (!in_array($action, ModuleConst::getActions())) {
            Log::warning("Action {$action} không hợp lệ");
            return false;
        }

        // Lấy quyền từ cache in-memory hoặc database
        $permissions = $this->loadUserPermissions();

        // Kiểm tra quyền
        if (!isset($permissions[$module]) || !($permissions[$module]["can_{$action}"] ?? false)) {
            Log::warning("User {$this->id} không có quyền {$action} trên module {$module}");
            return false;
        }

        return true;
    }

    protected function loadUserPermissions(): array
    {
        // Trả về cache nếu đã có trong request
        if ($this->permissionCache !== null) {
            return $this->permissionCache;
        }

        // Lấy danh sách cột động từ ModuleConst::getActions()
        $columns = array_merge(['module_name'], array_map(fn($action) => "can_{$action}", ModuleConst::getActions()));

        // Tải quyền từ database
        $permissions = $this->permissions()
            ->get($columns)
            ->keyBy('module_name')
            ->map(fn($permission) => $permission->toArray())
            ->toArray();

        // Lưu vào cache in-memory
        $this->permissionCache = $permissions;

        return $permissions;
    }
}