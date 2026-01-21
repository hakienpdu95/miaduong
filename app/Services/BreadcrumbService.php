<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;
use App\Constants\ModuleConst;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Thêm để query nếu cần

/**
 * Service for generating breadcrumbs dynamically in backend/frontend.
 * Tích hợp với ModuleConst cho auto build, hiệu suất cao với static cache nội bộ.
 * Nâng cao: Hỗ trợ sub-pages/module con (e.g., serials dưới equipment, maintenance-log dưới serial)
 * - Dynamic fetch tên từ model (e.g., tên equipment/serial từ DB via id param)
 * - Linh hoạt: Không fixed cứng route/module, detect từ route name/parts/parameters
 *   - Detect model động từ route parts (e.g., 'equipment' -> App\Models\Equipment, 'serials' -> App\Models\EquipmentQrCode)
 *   - Xử lý nested params động: Loop qua params theo thứ tự route parts (e.g., param1 cho model1, param2 cho model2)
 *   - Cache model per request để tối ưu (không query lặp)
 *   - Mở rộng dễ: Khi thêm module mới (e.g., MaintenanceLog), chỉ cần route convention (e.g., 'equipment.serials.maintenance-log.{maintenance_log_id}')
 *     - Service sẽ auto detect model từ 'maintenance-log' -> App\Models\MaintenanceLog, fetch tên từ $item->name hoặc fallback
 * - Tối ưu: Chỉ query cần thiết, cache static configs, per-request cache generated & models
 * - Chỉnh sửa: Insert tên item (từ model) vào sau module index, trước sub-action (e.g., Quản lý thiết bị > [Tên] > Danh sách mã Serial)
 * - Fix duplicate: Skip thêm action title nếu nó trùng với sub-part cuối hoặc nếu đã insert item (check logic để tránh 'Serials > Serials')
 * - Fix label: Ưu tiên title từ config (e.g., 'edit' => 'Edit Maintenance Log')
 * - Fix error route: Strip 'admin.' prefix khi match config, add 'admin.' khi generate URL; wrap Route::has() fallback '#'
 * - Fix order: Insert item name trước action (e.g., ... > Màn hình LCD Dell > Edit), và ưu tiên config label cho action (e.g., 'Edit Maintenance Type')
 */
class BreadcrumbService
{
    protected static array $configs = []; // Static cache configs (load 1 lần per app)
    protected static array $generated = []; // Cache generated per route per request
    protected static array $modelCache = []; // Cache model instances per request (tối ưu query)
    protected string $routePrefix = 'admin.'; // Prefix name cho backend (linh hoạt, có thể config)

    public function __construct()
    {
        if (!empty(static::$configs)) {
            return; // Load chỉ 1 lần
        }

        // Sync configs từ ModuleConst (dynamic từ module configs) - Linh hoạt, load động từ ModuleConst
        ModuleConst::loadConfigs();
        $modulesWithCategories = ModuleConst::getModulesWithCategories();

        // Build config array từ modules (backend/frontend tách nếu cần) - Linh hoạt, loop động
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

        // Load thêm từ thư mục config/breadcrumbs/{section}/ (override nếu tồn tại) - Linh hoạt, scan dir động
        $sections = ['backend', 'frontend'];
        foreach ($sections as $section) {
            $breadcrumbDir = config_path('breadcrumbs/' . $section . '/');
            if (is_dir($breadcrumbDir)) {
                foreach (glob($breadcrumbDir . '*.php') as $file) {
                    $moduleName = basename($file, '.php');
                    $fileConfig = include $file;
                    // Merge sâu để hỗ trợ sub-keys như 'index', 'create', 'edit' as assoc array
                    static::$configs[$section][$moduleName] = array_merge_recursive(
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

        // Build từ config (ưu tiên, và sẽ handle action label custom)
        if ($this->buildFromConfig(static::$configs[$section] ?? [], $currentRoute, $breadcrumbs)) {
            // OK
        } else {
            // Fallback auto từ route hierarchy + ModuleConst
            $this->autoBuildFromRoute($currentRoute, $breadcrumbs);
        }

        // Dynamic cho show/edit/delete/index/sub-pages (lấy title từ model nếu có) - Insert vào vị trí đúng, trước action
        $this->buildDynamic($currentRoute, $breadcrumbs);

        // Thêm dashboard/home mặc định ở đầu nếu là backend/frontend (sửa dùng 'admin.dashboard')
        array_unshift($breadcrumbs, [
            'title' => __('Bảng điều khiển'),
            'url' => $this->safeRoute('admin.dashboard', []), // Use safeRoute to fix error
        ]);

        static::$generated[$currentRoute] = $breadcrumbs; // Cache per request
        return $breadcrumbs;
    }

    /**
     * Safe way to get route URL, fallback '#' if not defined - Fix error Route not defined
     */
    protected function safeRoute(string $name, array $params = []): string
    {
        if (Route::has($name)) {
            return route($name, $params);
        }
        return '#';
    }

    /**
     * Build breadcrumbs recursively from config.
     * Chỉnh: Hỗ trợ sub-keys như 'edit' trực tiếp trong module config (e.g., $config['edit']['title'] or string)
     * - Strip prefix 'admin.' khi match currentRoute để linh hoạt
     * - Nếu match action sub-key, dùng title custom (fix generic 'Edit')
     */
    protected function buildFromConfig(array $config, string $currentRoute, array &$breadcrumbs, string $parentKey = ''): bool
    {
        $strippedRoute = str_replace($this->routePrefix, '', $currentRoute); // Strip 'admin.' để match config không prefix
        $parts = explode('.', $strippedRoute); // e.g., ['equipment', 'edit']
        $action = end($parts); // e.g., 'edit'

        foreach ($config as $key => $item) {
            if ($key === $action && (is_string($item) || (is_array($item) && isset($item['title'])))) { // Match action sub-key (string or array)
                $title = is_string($item) ? $item : $item['title'];
                $breadcrumbs[] = [
                    'title' => __($title),
                    'url' => $this->safeRoute($currentRoute, Route::current()->parameters()), // Full route for URL
                ];
                return true;
            }

            if (is_array($item) && isset($item['url'])) { // Child item with url
                $itemUrl = $item['url'] ?? null;
                if ($itemUrl === $strippedRoute) {
                    $fullUrl = $this->routePrefix . $itemUrl; // Add prefix khi generate
                    $breadcrumbs[] = [
                        'title' => __($item['title']),
                        'url' => $this->safeRoute($fullUrl, Route::current()->parameters()),
                    ];
                    return true;
                }
                if (isset($item['children']) && $this->buildFromConfig($item['children'], $currentRoute, $breadcrumbs, $key)) {
                    if ($itemUrl) {
                        $fullParent = $this->routePrefix . $itemUrl;
                        array_unshift($breadcrumbs, [
                            'title' => __($item['title']),
                            'url' => $this->safeRoute($fullParent),
                        ]);
                    }
                    return true;
                }
            } elseif (is_array($item)) { // Nested sub-config (recurse)
                if ($this->buildFromConfig($item, $currentRoute, $breadcrumbs, $parentKey . ($parentKey ? '.' : '') . $key)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Auto build breadcrumbs from route hierarchy and ModuleConst.
     * Sửa để không thêm category thừa nếu không cần (tự động theo route, bỏ category nếu url = '#').
     * Linh hoạt: Custom title cho sub-parts động (e.g., 'serials' -> 'Danh sách mã Serial', 'maintenance-log' -> 'Nhật ký bảo dưỡng')
     * - Fix duplicate: Chỉ thêm sub nếu không trùng last, và no separate action block
     */
    protected function autoBuildFromRoute(string $currentRoute, array &$breadcrumbs): void { 
        $routeParts = explode('.', $currentRoute); // e.g., ['admin', 'equipment', 'serials'] hoặc không có 'admin' 
        if (count($routeParts) < 1) return; 
        // Bỏ prefix 'admin.' nếu có (match name full) 
        if ($routeParts[0] === 'admin') array_shift($routeParts); 
        $moduleName = array_shift($routeParts); // e.g., 'equipment' (kebab) 
        // Tính fullModuleName để lấy trans (e.g., 'equipment_management') 
        $moduleSnake = str_replace('-', '_', $moduleName); 
        $fullModuleName = $moduleSnake . '_management'; 
        // Tìm module từ ModuleConst (match kebab_name) - Linh hoạt, loop động 
        $modulesWithCategories = ModuleConst::getModulesWithCategories(); 
        $found = false; 
        foreach ($modulesWithCategories as $category) { 
            foreach ($category['modules'] as $module) { 
                if (($module['name'] ?? '') === $moduleName) { // An toàn, check isset 
                    // Chỉ thêm category nếu có route thực (không '#') - sửa để bỏ thừa "System Management" 
                    if (Route::has($category['key'] . '.index')) { // Giả sử category có route index 
                        $breadcrumbs[] = ['title' => __($category['label']), 'url' => $this->safeRoute($category['key'] . '.index')]; 
                    } 
                    // Thêm module index với title custom (e.g., 'Quản lý thiết bị') 
                    $breadcrumbs[] = [ 
                        'title' => __($module['label'] ?? 'Quản lý ' . Str::title($moduleName)), 
                        'url' => $this->safeRoute("{$moduleName}.index"), 
                    ]; 
                    // Xử lý sub-parts và action (e.g., 'serials' -> 'Danh sách mã Serial') - Linh hoạt, map title động 
                    $currentBase = $moduleName; 
                    $lastTitle = ''; 
                    while (count($routeParts) > 0) { 
                        $sub = array_shift($routeParts); 
                        if ($sub === 'index') continue; // Skip 'index' để tránh duplicate với module 
                        // Ưu tiên title từ translation nếu tồn tại (e.g., 'equipment_management.edit' -> 'Chỉnh sửa thiết bị') 
                        $potentialKey = $fullModuleName . '.' . $sub; 
                        $subTitle = __($potentialKey); 
                        if ($subTitle === $potentialKey) { // Không có trans, fallback match hoặc Str::title 
                            $subTitle = match ($sub) { 
                                'serials' => 'Danh sách mã Serial', 
                                'maintenance-log' => 'Nhật ký bảo dưỡng', 
                                default => __(Str::title($sub)), 
                            }; 
                        } 
                        $url = $this->safeRoute("{$currentBase}.{$sub}", Route::current()->parameters()); // Use current params, safe 
                        // Fix duplicate: Skip nếu title trùng last 
                        if ($subTitle !== $lastTitle) { 
                            $breadcrumbs[] = [ 
                                'title' => $subTitle, 
                                'url' => $url, 
                            ]; 
                            $lastTitle = $subTitle; 
                        } 
                        $currentBase .= ".{$sub}"; 
                    } 
                    $found = true; 
                    break 2; 
                } 
            } 
        } 
        // Fallback generic nếu không tìm thấy trong ModuleConst (sửa thêm check Route::has để tránh error) - Linh hoạt 
        if (!$found) { 
            $currentBase = ''; 
            $lastTitle = ''; 
            foreach ($routeParts as $part) { 
                if ($part === 'index') continue; // Skip 'index' ở fallback 
                $tempBase = $currentBase . ($currentBase ? '.' : '') . $part; 
                $title = __(Str::title($part)); 
                $url = $this->safeRoute($tempBase); // Safe 
                if ($title !== $lastTitle) { 
                    $breadcrumbs[] = ['title' => $title, 'url' => $url]; 
                    $lastTitle = $title; 
                } 
                $currentBase = $tempBase; 
            } 
        } 
    }

    /**
     * Build dynamic breadcrumbs for show/edit/delete/index/sub-pages (lấy title từ model nếu có).
     * Nâng cao: Hỗ trợ fetch dynamic tên từ model (e.g., tên equipment/serial từ id param)
     * - Linh hoạt: Detect từ route parts động (e.g., route parts map sang model class via convention Str::studly(singular(part)))
     * - Xử lý nested params động: Loop qua params theo thứ tự route parts (e.g., param1 cho model1, param2 cho model2)
     * - Insert item name vào vị trí đúng (sau module index, trước sub-action) - e.g., insert 'Màn hình LCD Dell' sau 'Quản lý thiết bị' và trước 'Edit'
     * - Fix order/duplicate: Tìm vị trí action (last) và insert trước nó nếu action là 'edit/create' etc.
     * - Cache model per request để tối ưu (không query lặp)
     * - Mở rộng dễ: Khi thêm module mới (e.g., MaintenanceLog), chỉ cần route convention (e.g., 'equipment.serials.maintenance-log.{maintenance_log_id}')
     *   - Service sẽ auto detect model từ 'maintenance-log' -> App\Models\MaintenanceLog, fetch tên từ $item->name hoặc fallback
     */
    protected function buildDynamic(string $currentRoute, array &$breadcrumbs): void
    {
        $routeParts = explode('.', $currentRoute);
        if (count($routeParts) < 2) return;

        // Bỏ prefix 'admin.' nếu có
        if ($routeParts[0] === 'admin') array_shift($routeParts);

        $parameters = Route::current()->parameters(); // Lấy tất cả params (e.g., ['id' => 1, 'serial_id' => 2, 'log_id' => 3])
        $paramKeys = array_keys($parameters); // e.g., ['id', 'serial_id', 'log_id'] - thứ tự theo route definition
        $paramIndex = 0; // Theo thứ tự param

        // Loop qua route parts để map model động (bỏ action cuối nếu là 'index/create/edit/serials' etc.)
        $modelParts = array_slice($routeParts, 0, -1); // e.g., ['equipment'] cho 'equipment.edit'
        $action = end($routeParts); // e.g., 'edit'
        $actionIndex = count($breadcrumbs) - 1; // Giả sử action là last, sẽ adjust

        $moduleIndex = -1; // Tìm vị trí module index
        foreach ($breadcrumbs as $idx => $crumb) {
            if (str_contains($crumb['url'], ".{$routeParts[0]}.index")) {
                $moduleIndex = $idx;
                break;
            }
        }

        foreach ($modelParts as $index => $part) {
            $modelName = Str::studly(Str::singular($part)); // e.g., 'Equipment', 'EquipmentQrCode', 'MaintenanceLog' - linh hoạt convention
            $modelClass = "App\\Models\\{$modelName}";

            if (class_exists($modelClass) && isset($paramKeys[$paramIndex]) && $parameters[$paramKeys[$paramIndex]]) {
                $id = $parameters[$paramKeys[$paramIndex]];
                // Cache model instance
                $cacheKey = $modelClass . '_' . $id;
                if (!isset(static::$modelCache[$cacheKey])) {
                    static::$modelCache[$cacheKey] = $modelClass::find($id); // Query động
                }
                $item = static::$modelCache[$cacheKey];

                if ($item) {
                    // Dynamic title từ model (ưu tiên name > title > serial_number > id fallback)
                    $itemTitle = $item->name ?? $item->title ?? $item->serial_number ?? "Item #{$id}";

                    // Custom prefix nếu cần (linh hoạt based on model name, không hardcode - dùng str_contains)
                    if (str_contains($modelName, 'QrCode') || str_contains($modelName, 'Serial')) {
                        $itemTitle = 'Serial ' . $itemTitle;
                    } elseif (str_contains($modelName, 'Log')) {
                        $itemTitle = 'Nhật ký ' . $itemTitle;
                    }

                    // URL: Giả sử route show cho model (e.g., 'equipment.show', 'equipment.serials.show') - linh hoạt check
                    $showRoute = implode('.', array_slice($routeParts, 0, $index + 1)) . '.show';
                    $url = $this->safeRoute($showRoute, array_slice($parameters, 0, $paramIndex + 1)); // Chỉ params đến hiện tại

                    // Insert vào sau module index, trước action (e.g., trước 'Edit')
                    if ($moduleIndex !== -1) {
                        $insertPos = $moduleIndex + 1; // Sau module
                        if (in_array($action, ['edit', 'create', 'show'])) { // Nếu có action, insert trước last (action)
                            $insertPos = count($breadcrumbs); // Append trước khi thêm action, nhưng vì action chưa thêm, adjust in autoBuild
                        }
                        array_splice($breadcrumbs, $insertPos, 0, [['title' => $itemTitle, 'url' => $url]]);
                        $moduleIndex++; // Adjust for next
                    } else {
                        $breadcrumbs[] = ['title' => $itemTitle, 'url' => $url]; // Fallback append
                    }
                }

                $paramIndex++; // Next param cho model con tiếp theo
            }
        }

        // Fix duplicate action: Check if last breadcrumb title trùng với prev, remove last if duplicate
        $count = count($breadcrumbs);
        if ($count >= 2 && $breadcrumbs[$count - 1]['title'] === $breadcrumbs[$count - 2]['title']) {
            array_pop($breadcrumbs); // Remove duplicate last
        }
    }
}