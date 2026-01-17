<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Constants\ModuleConst;

class BreadcrumbService
{
    protected $config;

    public function __construct()
    {
        // Load cấu hình chính
        $this->config = config('breadcrumbs') ?? ['backend' => [], 'frontend' => []];

        // Load động các file cấu hình con từ config/breadcrumbs/{section}/
        $sections = ['backend', 'frontend'];
        foreach ($sections as $section) {
            $breadcrumbDir = config_path('breadcrumbs/' . $section . '/');
            if (is_dir($breadcrumbDir)) {
                foreach (glob($breadcrumbDir . '*.php') as $file) {
                    $moduleName = basename($file, '.php');
                    $this->config[$section][$moduleName] = include $file;
                }
            }
        }
    }

    public function generate($section = 'backend')
    {
        $currentRoute = Route::currentRouteName();
        $parameters = Route::current()->parameters();
        $cacheKey = 'breadcrumbs_' . $section . '_' . $currentRoute . '_' . md5(json_encode($parameters));

        return Cache::remember($cacheKey, 3600, function () use ($section, $currentRoute) {
            $breadcrumbs = [];

            // Ưu tiên sử dụng cấu hình từ config/breadcrumbs
            if (!$this->buildBreadcrumbs($this->config[$section] ?? [], $currentRoute, $breadcrumbs)) {
                // Nếu không tìm thấy trong config, thử tự động sinh từ ModuleConst
                $this->autoBuildFromModuleConst($currentRoute, $breadcrumbs, $section);
            }

            // Thêm breadcrumb động nếu cần (ví dụ: post.show, post.edit)
            if (empty($breadcrumbs)) {
                $this->buildDynamicBreadcrumbs($currentRoute, $breadcrumbs);
            }

            // Thêm breadcrumb dashboard mặc định
            array_unshift($breadcrumbs, [
                'title' => __('dashboard'),
                'url' => route('dashboard'),
            ]);

            return $breadcrumbs;
        });
    }

    protected function buildBreadcrumbs($config, $currentRoute, &$breadcrumbs, $parentUrl = '')
    {
        foreach ($config as $module => $moduleConfig) {
            foreach ($moduleConfig as $key => $item) {
                if (isset($item['url']) && $item['url'] === $currentRoute) {
                    $breadcrumbs[] = [
                        'title' => __($item['title']),
                        'url' => $this->generateRouteUrl($item['url']),
                    ];
                    return true;
                }

                if (isset($item['children'])) {
                    if ($this->buildBreadcrumbs([$item['children']], $currentRoute, $breadcrumbs, $item['url'] ?? '')) {
                        if (isset($item['url'])) {
                            array_unshift($breadcrumbs, [
                                'title' => __($item['title']),
                                'url' => $this->generateRouteUrl($item['url']),
                            ]);
                        }
                        return true;
                    }
                }
            }
        }
        return false;
    }

    protected function autoBuildFromModuleConst($currentRoute, &$breadcrumbs, $section)
    {
        $modulesWithCategories = ModuleConst::getModulesWithCategories();
        $routeParts = explode('.', $currentRoute);
        $moduleNameFromRoute = $routeParts[0];

        foreach ($modulesWithCategories as $category) {
            foreach ($category['modules'] as $module) {
                $moduleKey = $module['full_name'] ?? $module['label'];

                // Map moduleName giống logic trong ModuleServiceProvider để khớp route prefix
                if (str_ends_with($moduleKey, '_post_management')) {
                    $mappedModule = str_replace('_post_management', '-posts', $moduleKey);
                    $mappedModule = str_replace('_', '-', $mappedModule);
                } elseif (str_ends_with($moduleKey, '_listing_management')) {
                    $mappedModule = str_replace('_listing_management', '-listings', $moduleKey);
                    $mappedModule = str_replace('_', '-', $mappedModule);
                } else {
                    $mappedModule = str_replace('_management', '', $moduleKey);
                    $mappedModule = str_replace('_', '-', $mappedModule); // Thêm dòng này để thay thế '_' thành '-'
                }

                if ($mappedModule === $moduleNameFromRoute && str_starts_with($currentRoute, $moduleNameFromRoute . '.')) {
                    // Thêm breadcrumb cho category nếu cần
                    $categoryLabel = __($category['label']) ?: $category['label'];
                    $breadcrumbs[] = [
                        'title' => $categoryLabel,
                        'url' => '#',
                    ];

                    // Thêm breadcrumb cho module cha
                    $moduleLabel = __($module['label']) ?: $module['label'];
                    $breadcrumbs[] = [
                        'title' => $moduleLabel,
                        'url' => $this->generateRouteUrl($moduleNameFromRoute . '.index'),
                    ];

                    // Xử lý các phần nested (subs và action)
                    $remainingParts = array_slice($routeParts, 1);
                    $currentUrlBase = $moduleNameFromRoute;
                    $subPath = ''; // Để xây dựng key dịch cho sub

                    while (count($remainingParts) > 1) { // Xử lý subs (tất cả trừ action cuối)
                        $sub = array_shift($remainingParts);
                        $subPath .= ($subPath ? '.' : '') . $sub;
                        $subLabel = __($module['label'] . '.' . $subPath) ?: ucfirst($sub);
                        $subUrl = $this->generateRouteUrl($currentUrlBase . '.' . $sub . '.index');
                        $breadcrumbs[] = [
                            'title' => $subLabel,
                            'url' => $subUrl,
                        ];
                        $currentUrlBase .= '.' . $sub;
                    }

                    // Xử lý action cuối cùng
                    if (!empty($remainingParts)) {
                        $action = $remainingParts[0];
                        if ($action !== 'index') {
                            $actionLabelKey = $module['label'] . '.' . ($subPath ? $subPath . '.' : '') . $action;
                            $actionLabel = __($actionLabelKey) ?: ucfirst($action);
                            $breadcrumbs[] = [
                                'title' => $actionLabel,
                                'url' => route($currentRoute, Route::current()->parameters()),
                            ];
                        }
                    }

                    return true;
                }
            }
        }
        return false;
    }

    protected function buildDynamicBreadcrumbs($currentRoute, &$breadcrumbs)
    {
        $routeParts = explode('.', $currentRoute);
        if (count($routeParts) < 2) {
            return false;
        }

        $module = $routeParts[0];
        $action = array_pop($routeParts); // Lấy action cuối
        $subPath = implode('.', $routeParts); // Phần còn lại là module.sub (nếu có)

        if (in_array($action, ['show', 'edit', 'permissions', 'delete'])) {
            // Lấy ID từ route parameters (giả sử param cuối là id)
            $parameters = Route::current()->parameters();
            $id = end($parameters); // Lấy param cuối cùng

            // Lấy model dựa trên subPath hoặc module
            $modelName = ucfirst(str_replace(['-', '.'], '', $subPath ? $subPath : $module));
            $modelClass = 'App\\Models\\' . $modelName;

            if (class_exists($modelClass)) {
                $item = $modelClass::find($id);
                if ($item) {
                    // Thêm breadcrumb động
                    $breadcrumbs[] = [
                        'title' => $item->name ?? $item->title ?? 'Item ' . $id,
                        'url' => route($currentRoute, $parameters),
                    ];

                    // Thêm parent breadcrumb (list của sub hoặc module)
                    $parentRoute = ($subPath ? $subPath : $module) . '.index';
                    array_unshift($breadcrumbs, [
                        'title' => __(ucfirst(str_replace(['-', '.'], ' ', $subPath ? $subPath : $module)) . ' List'),
                        'url' => $this->generateRouteUrl($parentRoute),
                    ]);

                    return true;
                }
            }
        }

        return false;
    }

    protected function generateRouteUrl($routeName)
    {
        $route = Route::getRoutes()->getByName($routeName);
        if (!$route) {
            return '#';
        }

        preg_match_all('/\{([^\}]+)\}/', $route->uri(), $matches);
        $paramInfos = $matches[1] ?? [];

        $requiredParams = [];
        $optionalParams = [];
        foreach ($paramInfos as $paramInfo) {
            if (str_ends_with($paramInfo, '?')) {
                $name = rtrim($paramInfo, '?');
                $optionalParams[] = $name;
            } else {
                $name = $paramInfo;
                $requiredParams[] = $name;
            }
        }

        $currentParams = Route::current()->parameters();
        $genParams = [];

        foreach ($requiredParams as $name) {
            if (isset($currentParams[$name])) {
                $genParams[$name] = $currentParams[$name];
            } else {
                return '#'; // Missing required param
            }
        }

        foreach ($optionalParams as $name) {
            if (isset($currentParams[$name])) {
                $genParams[$name] = $currentParams[$name];
            }
        }

        return route($routeName, $genParams);
    }
}