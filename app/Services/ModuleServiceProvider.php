<?php

namespace App\Services;

use Illuminate\Support\ServiceProvider;
use App\Constants\ModuleConst;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        // Cache danh sách module để giảm truy vấn
        $modules = Cache::remember('module_routes', 3600, fn () => ModuleConst::getModules());

        Route::prefix('admin')->middleware(['web', 'auth:web'])->group(function () use ($modules) {
            Route::get('/dashboard', \App\Http\Livewire\Backend\Dashboard::class)->name('dashboard');

            foreach ($modules as $module) {
                // Xử lý tên route
                if (str_ends_with($module, '_post_management')) {
                    $routeName = str_replace('_post_management', '-posts', $module);
                    $routeName = str_replace('_', '-', $routeName);
                } else {
                    $routeName = str_replace('_management', '', $module);
                    $routeName = str_replace('_', '-', $routeName);
                }

                // Xử lý namespace cho Livewire components
                $moduleName = str_replace('_management', '', $module);
                $moduleParts = explode('_', $moduleName);
                $namespaceSegment = implode('', array_map('ucfirst', $moduleParts));
                $namespace = "App\\Http\\Livewire\\Backend\\{$namespaceSegment}";

                // Kiểm tra class tồn tại để tránh lỗi
                if (!class_exists("{$namespace}\\Index")) {
                    continue;
                }

                // Định nghĩa các route trong group để giảm lặp code
                Route::prefix($routeName)->name("{$routeName}.")->middleware("check.permission:{$module}," . ModuleConst::ACTION_VIEW)->group(function () use ($namespace, $module) {
                    Route::get('/', "{$namespace}\\Index")->name('index');
                    Route::get('/create', "{$namespace}\\Create")->name('create')->middleware("check.permission:{$module}," . ModuleConst::ACTION_CREATE);
                    Route::get('/{id}/edit', "{$namespace}\\Edit")->name('edit')->middleware("check.permission:{$module}," . ModuleConst::ACTION_EDIT);
                    Route::delete('/{id}', "{$namespace}\\Delete")->name('delete')->middleware("check.permission:{$module}," . ModuleConst::ACTION_DELETE);
                });

                // Bao gồm file route tùy chỉnh của module nếu tồn tại
                $routeFile = app_path("Modules/{$namespaceSegment}/routes.php");
                if (file_exists($routeFile)) {
                    require $routeFile;
                }
            }
        });
    }
}