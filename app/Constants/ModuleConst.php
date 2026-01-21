<?php

namespace App\Constants;

use Illuminate\Support\Facades\File;

/**
 * Constants for module names and actions in the system.
 */
final class ModuleConst
{
    // Permission actions (full list từ code gốc)
    public const ACTION_VIEW = 'view';
    public const ACTION_CREATE = 'create';
    public const ACTION_EDIT = 'edit';
    public const ACTION_DELETE = 'delete';

    // Static storage for module configs (load động, chỉ 1 lần)
    private static array $moduleConfigs = [];
    private static array $moduleLabels = [];

    /**
     * Load all module configurations dynamically from module directories.
     * Mỗi module có file config.php trong thư mục của nó (e.g., app/Http/Controllers/Backend/Role/config.php).
     * Nâng cao: Hỗ trợ load submodules từ 'submodules' key trong config cha (để seed permission riêng cho submodule).
     */
    public static function loadConfigs(): void
    {
        if (!empty(self::$moduleConfigs)) {
            return; // Load chỉ 1 lần
        }

        $moduleDirs = glob(app_path('Http/Controllers/Backend/*'), GLOB_ONLYDIR);
        foreach ($moduleDirs as $dir) {
            $modulePascal = basename($dir); // e.g., 'Role'
            $configFile = $dir . '/config.php';
            if (File::exists($configFile)) {
                $config = require $configFile;
                $moduleSnake = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $modulePascal));
                $fullModuleName = $moduleSnake . '_management';
                self::$moduleConfigs[$fullModuleName] = array_merge([
                    'pascal_name' => $modulePascal,
                    'snake_name' => $moduleSnake,
                    'kebab_name' => str_replace('_', '-', $moduleSnake),
                ], $config);
                self::$moduleLabels[$fullModuleName] = $config['label'] ?? ucfirst(str_replace('_', ' ', $moduleSnake));

                // Hỗ trợ submodules: Merge vào $moduleConfigs nếu có key 'submodules' (array assoc fullName => config)
                if (isset($config['submodules']) && is_array($config['submodules'])) {
                    foreach ($config['submodules'] as $subFullName => $subConfig) {
                        // Merge sub config, thêm parent info nếu cần (linh hoạt)
                        self::$moduleConfigs[$subFullName] = array_merge([
                            'parent' => $fullModuleName, // Để biết là sub của module nào
                            'kebab_name' => str_replace('_', '-', str_replace('_management', '', $subFullName)),
                        ], $subConfig);
                        self::$moduleLabels[$subFullName] = $subConfig['label'] ?? ucfirst(str_replace('_', ' ', $subFullName));
                    }
                }
            }
        }
    }

    /**
     * Get all module names (full names like 'role_management').
     * Used for seeding or listing modules.
     *
     * @return array
     */
    public static function getModules(): array
    {
        self::loadConfigs();
        return array_keys(self::$moduleConfigs); // Trả về array unique full module names
    }

    /**
     * Get all modules with their categories, labels, icons, and children.
     * Bổ sung lọc visible (nếu visible = false, bỏ qua module).
     * Nâng cao: Hỗ trợ submodules như children của module cha (nếu có 'parent').
     *
     * @return array
     */
    public static function getModulesWithCategories(): array
    {
        self::loadConfigs();
        $categories = [];
        // Xử lý main modules trước
        foreach (self::$moduleConfigs as $fullModuleName => $config) {
            if (isset($config['parent'])) {
                continue; // Bỏ qua sub, sẽ add vào parent sau
            }
            // Lọc: Chỉ lấy module có visible = true (hoặc không set, mặc định true)
            $visible = $config['visible'] ?? true;
            if (!$visible) {
                continue; // Bỏ qua module ẩn
            }
            $categoryKey = $config['category'] ?? 'uncategorized';
            if (!isset($categories[$categoryKey])) {
                $categories[$categoryKey] = [
                    'key' => $categoryKey,
                    'label' => $config['category_label'] ?? ucfirst(str_replace('_', ' ', $categoryKey)),
                    'modules' => [],
                ];
            }
            $modulesEntry = [
                'name' => $config['kebab_name'],
                'label' => $config['label'],
                'icon' => $config['icon'],
                'children' => $config['children'] ?? [],
                'full_name' => $fullModuleName,
            ];
            $categories[$categoryKey]['modules'][] = $modulesEntry;
        }
        // Add submodules vào children của parent
        foreach (self::$moduleConfigs as $fullModuleName => $config) {
            if (!isset($config['parent'])) {
                continue; // Chỉ xử lý sub
            }
            $parentFullName = $config['parent'];
            // Tìm parent category và module để add child
            foreach ($categories as &$category) {
                foreach ($category['modules'] as &$module) {
                    if ($module['full_name'] === $parentFullName) {
                        // Lọc visible cho sub
                        $visible = $config['visible'] ?? true;
                        if ($visible) {
                            $module['children'][] = [
                                'name' => $config['kebab_name'],
                                'label' => $config['label'],
                                'icon' => $config['icon'] ?? null,
                                'children' => $config['children'] ?? [],
                                'full_name' => $fullModuleName,
                            ];
                        }
                        break 2;
                    }
                }
            }
        }
        return array_values($categories); // Trả về mảng để dễ loop
    }

    /**
     * Get authorized modules for a user based on their permissions.
     * Đã tích hợp lọc visible từ getModulesWithCategories().
     * Sửa: Thêm check isset($child['full_name']) để tránh lỗi undefined key cho children là route (không có full_name).
     *
     * @param \App\Models\User|null $user
     * @return array
     */
    public static function getAuthorizedModules(?\App\Models\User $user): array
    {
        if (!$user) {
            return [];
        }
        self::loadConfigs();
        $categories = [];
        foreach (self::getModulesWithCategories() as $category) { // Sử dụng method đã lọc visible
            $authorizedModules = [];
            foreach ($category['modules'] as $module) {
                if ($user->hasPermission($module['full_name'], self::ACTION_VIEW)) {
                    // Lọc children authorized
                    $authorizedChildren = [];
                    foreach ($module['children'] ?? [] as $child) {
                        if (isset($child['full_name'])) {
                            // Chỉ check permission nếu child là submodule (có full_name)
                            if ($user->hasPermission($child['full_name'], self::ACTION_VIEW)) {
                                $authorizedChildren[] = $child;
                            }
                        } else {
                            // Giữ nguyên children là route (không có full_name, không cần check permission riêng)
                            $authorizedChildren[] = $child;
                        }
                    }
                    $module['children'] = $authorizedChildren;
                    $authorizedModules[] = $module;
                }
            }
            if (!empty($authorizedModules)) {
                $categories[] = [
                    'key' => $category['key'],
                    'label' => $category['label'],
                    'modules' => $authorizedModules,
                ];
            }
        }
        return $categories;
    }

    /**
     * Get display name for a specific module.
     *
     * @param string $module
     * @return string
     */
    public static function getModuleLabel(string $module): string
    {
        self::loadConfigs();
        return self::$moduleLabels[$module] ?? ucfirst(str_replace('_', ' ', $module));
    }

    /**
     * Check if a module name is valid.
     *
     * @param string $module
     * @return bool
     */
    public static function isValidModule(string $module): bool
    {
        self::loadConfigs();
        return isset(self::$moduleConfigs[$module]);
    }

    /**
     * Get all actions.
     *
     * @return array
     */
    public static function getActions(): array
    {
        return [
            self::ACTION_VIEW,
            self::ACTION_CREATE,
            self::ACTION_EDIT,
            self::ACTION_DELETE,
        ];
    }
}