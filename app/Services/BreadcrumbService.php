<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;
use App\Constants\ModuleConst;
use Illuminate\Support\Str;

/**
 * Service for generating breadcrumbs dynamically in backend/frontend.
 * Tích hợp với ModuleConst cho auto build, hiệu suất cao với static cache nội bộ.
 */
class BreadcrumbService
{
    protected static array $configs = []; // Static cache configs (load 1 lần per app)
    protected static array $generated = []; // Cache generated per route per request

    public function __construct()
    {
        if (!empty(static::$configs)) {
            return; // Load chỉ 1 lần
        }

        // Sync configs từ ModuleConst (dynamic từ module configs)
        ModuleConst::loadConfigs();
        $modulesWithCategories = ModuleConst::getModulesWithCategories();

        // Build config array từ modules (backend/frontend tách nếu cần)
        foreach (['backend', 'frontend'] as $section) {
            static::$configs[$section] = [];
            foreach ($modulesWithCategories as $category) {
                foreach ($category['modules'] as $module) {
                    $moduleKey = $module['name'] ?? Str::kebab($module['label'] ?? 'unknown'); // An toàn, fallback nếu thiếu key
                    static::$configs[$section][$moduleKey] = [
                        'title' => $module['label'] ?? 'Unknown Module',
                        'url' => "{$moduleKey}.index", // Route name không prefix, match 'user.index'
                        'children' => array_map(function ($child) {
                            return [
                                'title' => $child['label'],
                                'url' => $child['name'],
                            ];
                        }, $module['children'] ?? []),
                    ];
                }
            }
        }

        // Load thêm từ thư mục config/breadcrumbs/{section}/ (override nếu tồn tại)
        $sections = ['backend', 'frontend'];
        foreach ($sections as $section) {
            $breadcrumbDir = config_path('breadcrumbs/' . $section . '/');
            if (is_dir($breadcrumbDir)) {
                foreach (glob($breadcrumbDir . '*.php') as $file) {
                    $moduleName = basename($file, '.php');
                    $fileConfig = include $file;
                    static::$configs[$section][$moduleName] = array_merge(
                        static::$configs[$section][$moduleName] ?? [],
                        $fileConfig
                    );
                }
            }
        }
    }

    /**
     * Generate breadcrumbs for current route.
     *
     * @param string $section 'backend' or 'frontend'
     * @return array
     */
    public function generate(string $section = 'backend'): array
    {
        $currentRoute = Route::currentRouteName();
        if (isset(static::$generated[$currentRoute])) {
            return static::$generated[$currentRoute]; // Cache nội bộ per request
        }

        $breadcrumbs = [];

        // Build từ config (ưu tiên)
        if ($this->buildFromConfig(static::$configs[$section] ?? [], $currentRoute, $breadcrumbs)) {
            // OK
        } else {
            // Fallback auto từ route hierarchy + ModuleConst
            $this->autoBuildFromRoute($currentRoute, $breadcrumbs);
        }

        // Dynamic cho show/edit/delete/index (lấy title từ model nếu có)
        if (empty($breadcrumbs)) {
            $this->buildDynamic($currentRoute, $breadcrumbs);
        }

        // Thêm dashboard/home mặc định ở đầu nếu là backend/frontend (sửa dùng 'admin.dashboard')
        array_unshift($breadcrumbs, [
            'title' => __('dashboard'),
            'url' => route($section === 'backend' ? 'admin.dashboard' : 'home'), // Sửa match name mới
        ]);

        static::$generated[$currentRoute] = $breadcrumbs; // Cache per request
        return $breadcrumbs;
    }

    /**
     * Build breadcrumbs recursively from config.
     */
    protected function buildFromConfig(array $config, string $currentRoute, array &$breadcrumbs, string $parentUrl = ''): bool
    {
        foreach ($config as $key => $item) {
            $itemUrl = $item['url'] ?? null;
            if ($itemUrl === $currentRoute) {
                $breadcrumbs[] = [
                    'title' => __($item['title']),
                    'url' => route($itemUrl, Route::current()->parameters()),
                ];
                return true;
            }

            if (isset($item['children']) && $this->buildFromConfig($item['children'], $currentRoute, $breadcrumbs, $itemUrl)) {
                if ($itemUrl) {
                    array_unshift($breadcrumbs, [
                        'title' => __($item['title']),
                        'url' => route($itemUrl),
                    ]);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Auto build breadcrumbs from route hierarchy and ModuleConst.
     * Sửa để không thêm category thừa nếu không cần (tự động theo route, bỏ category nếu url = '#').
     */
    protected function autoBuildFromRoute(string $currentRoute, array &$breadcrumbs): void
    {
        $routeParts = explode('.', $currentRoute); // e.g., ['unit', 'edit']
        if (count($routeParts) < 1) return;

        $moduleName = array_shift($routeParts); // e.g., 'unit'

        // Tìm module từ ModuleConst (match kebab_name)
        $modulesWithCategories = ModuleConst::getModulesWithCategories();
        $found = false;
        foreach ($modulesWithCategories as $category) {
            foreach ($category['modules'] as $module) {
                if (($module['name'] ?? '') === $moduleName) { // An toàn, check isset
                    // Chỉ thêm category nếu có route thực (không '#') - sửa để bỏ thừa "System Management"
                    if (Route::has($category['key'] . '.index')) { // Giả sử category có route index
                        $breadcrumbs[] = ['title' => __($category['label']), 'url' => route($category['key'] . '.index')];
                    } // Else bỏ category, chỉ thêm module + action

                    // Thêm module index
                    $breadcrumbs[] = [
                        'title' => __($module['label']),
                        'url' => route("{$moduleName}.index"),
                    ];

                    // Xử lý sub-parts và action
                    $currentBase = $moduleName;
                    while (count($routeParts) > 1) {
                        $sub = array_shift($routeParts);
                        $breadcrumbs[] = [
                            'title' => __(Str::title($sub)),
                            'url' => route("{$currentBase}.{$sub}.index"),
                        ];
                        $currentBase .= ".{$sub}";
                    }

                    // Action cuối (nếu != index) - tùy chỉnh title cho 'edit' thành 'Chỉnh sửa đơn vị'
                    if (!empty($routeParts) && $routeParts[0] !== 'index') {
                        $action = $routeParts[0];
                        $actionTitle = ($action === 'edit') ? 'Chỉnh sửa đơn vị' : __(Str::title($action)); // Sửa custom cho 'edit'
                        $breadcrumbs[] = [
                            'title' => $actionTitle,
                            'url' => route($currentRoute, Route::current()->parameters()),
                        ];
                    }

                    $found = true;
                    break 2;
                }
            }
        }

        // Fallback generic nếu không tìm thấy trong ModuleConst (sửa thêm check Route::has để tránh error)
        if (!$found) {
            $currentBase = '';
            foreach ($routeParts as $part) {
                $tempBase = $currentBase . ($currentBase ? '.' : '') . $part;
                $title = __(Str::title($part));
                $url = Route::has($tempBase) ? route($tempBase) : '#'; // Sửa: Check has trước gọi route, fallback '#'
                $breadcrumbs[] = ['title' => $title, 'url' => $url];
                $currentBase = $tempBase;
            }
        }
    }

    /**
     * Build dynamic breadcrumbs for show/edit/delete/index (lấy title từ model nếu có).
     */
    protected function buildDynamic(string $currentRoute, array &$breadcrumbs): void
    {
        $routeParts = explode('.', $currentRoute);
        if (count($routeParts) < 2) return;

        $module = implode('.', array_slice($routeParts, 0, -1)); // e.g., 'unit'
        $action = end($routeParts); // e.g., 'edit'

        $parameters = Route::current()->parameters();
        $id = end($parameters) ?? null; // Giả sử param cuối là id (nếu có)

        $modelName = Str::studly(Str::singular($module)); // e.g., 'Unit'
        $modelClass = "App\\Models\\{$modelName}";

        if (class_exists($modelClass)) {
            if (in_array($action, ['show', 'edit', 'delete']) && $id) {
                $item = $modelClass::find($id);
                if ($item) {
                    $breadcrumbs[] = [
                        'title' => $item->name ?? $item->title ?? "Item #{$id}",
                        'url' => route($currentRoute, $parameters),
                    ];
                }
            } elseif ($action === 'index') {
                // Cho index, chỉ thêm list title (không cần model instance)
                $breadcrumbs[] = [
                    'title' => __(Str::title(Str::plural($module))),
                    'url' => route($currentRoute),
                ];
            }
        } else {
            // Fallback nếu không có model
            $breadcrumbs[] = [
                'title' => __(Str::title($action)),
                'url' => route($currentRoute, $parameters),
            ];
            array_unshift($breadcrumbs, [
                'title' => __(Str::title($module)),
                'url' => Route::has("{$module}.index") ? route("{$module}.index") : '#', // Sửa tương tự, check has
            ]);
        }
    }
}