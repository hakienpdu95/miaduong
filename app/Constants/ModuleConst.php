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
     *
     * @return array
     */
    public static function getModulesWithCategories(): array
    {
        self::loadConfigs();
        $categories = [];

        foreach (self::$moduleConfigs as $fullModuleName => $config) {
            $categoryKey = $config['category'] ?? 'uncategorized';
            if (!isset($categories[$categoryKey])) {
                $categories[$categoryKey] = [
                    'key' => $categoryKey,
                    'label' => $config['category_label'] ?? ucfirst(str_replace('_', ' ', $categoryKey)),
                    'modules' => [],
                ];
            }
            $categories[$categoryKey]['modules'][] = [
                'name' => $config['kebab_name'],
                'label' => $config['label'],
                'icon' => $config['icon'],
                'children' => $config['children'] ?? [],
                'full_name' => $fullModuleName,
            ];
        }

        return array_values($categories); // Trả về mảng để dễ loop
    }

    /**
     * Get authorized modules for a user based on their permissions.
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

        foreach (self::getModulesWithCategories() as $category) {
            $authorizedModules = [];
            foreach ($category['modules'] as $module) {
                if ($user->hasPermission($module['full_name'], self::ACTION_VIEW)) {
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