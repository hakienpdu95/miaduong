<?php

namespace App\Services;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Constants\ModuleConst;

/**
 * Service provider for dynamically registering module routes.
 */
class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load module configs trước khi đăng ký routes
        ModuleConst::loadConfigs();

        // Quét động các module từ thư mục controllers
        $moduleDirs = glob(app_path('Http/Controllers/Backend/*'), GLOB_ONLYDIR);

        // Đăng ký routes backend
        Route::prefix('admin')
            ->middleware(['web', 'auth:web'])
            ->group(function () use ($moduleDirs) {
                // Route dashboard (giả sử dùng controller)
                Route::get('/dashboard', [\App\Http\Controllers\Backend\DashboardController::class, 'index'])->name('admin.dashboard');

                foreach ($moduleDirs as $dir) {
                    $modulePascal = basename($dir); // e.g., 'User'
                    $moduleSnake = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $modulePascal));
                    $moduleKebab = str_replace('_', '-', $moduleSnake);
                    $fullModuleName = $moduleSnake . '_management';

                    // Kiểm tra nếu module hợp lệ (có config)
                    if (!ModuleConst::isValidModule($fullModuleName)) {
                        continue;
                    }

                    // Namespace cho controller
                    $namespace = "App\\Http\\Controllers\\Backend\\{$modulePascal}";
                    $controller = "{$namespace}\\{$modulePascal}Controller";
                    if (!class_exists($controller)) {
                        continue; // Bỏ qua nếu không có controller (ngăn lỗi cho module chưa hoàn thiện)
                    }

                    // Đăng ký routes CRUD với resource (sử dụng parameter {id} để tránh "{}")
                    Route::prefix($moduleKebab)
                        ->name("{$moduleKebab}.")
                        ->group(function () use ($controller, $fullModuleName) {
                            Route::resource('/', $controller)->parameters(['' => 'id']) // Force parameter là {id} thay vì {}
                                ->middleware([
                                    'index' => "check.permission:{$fullModuleName}," . ModuleConst::ACTION_VIEW,
                                    'create' => "check.permission:{$fullModuleName}," . ModuleConst::ACTION_CREATE,
                                    'store' => "check.permission:{$fullModuleName}," . ModuleConst::ACTION_CREATE,
                                    'show' => "check.permission:{$fullModuleName}," . ModuleConst::ACTION_VIEW,
                                    'edit' => "check.permission:{$fullModuleName}," . ModuleConst::ACTION_EDIT,
                                    'update' => "check.permission:{$fullModuleName}," . ModuleConst::ACTION_EDIT,
                                    'destroy' => "check.permission:{$fullModuleName}," . ModuleConst::ACTION_DELETE,
                                ]);
                        });

                    // Hỗ trợ routes tùy chỉnh per module (tùy chọn)
                    $routeFile = $dir . '/routes.php';
                    if (file_exists($routeFile)) {
                        Route::group(['prefix' => $moduleKebab, 'name' => "{$moduleKebab}."], function () use ($routeFile) {
                            require $routeFile;
                        });
                    }
                }
            });

        // Đăng ký routes API (sử dụng middleware 'web' để hỗ trợ CSRF và session cho backend AJAX)
        Route::prefix('api')
            ->middleware(['web', 'auth:web']) // Sử dụng 'web' để có CSRF, session; thay vì 'api'
            ->group(function () use ($moduleDirs) {
                foreach ($moduleDirs as $dir) {
                    $modulePascal = basename($dir); // e.g., 'User'
                    $moduleSnake = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $modulePascal));
                    $moduleKebab = str_replace('_', '-', $moduleSnake);
                    $fullModuleName = $moduleSnake . '_management';

                    // Kiểm tra nếu module hợp lệ (có config)
                    if (!ModuleConst::isValidModule($fullModuleName)) {
                        continue;
                    }

                    // Namespace cho API controller (giả sử App\Http\Controllers\Api\{ModulePascal}Controller)
                    $apiNamespace = "App\\Http\\Controllers\\Api";
                    $apiController = "{$apiNamespace}\\{$modulePascal}Controller";
                    if (!class_exists($apiController)) {
                        continue; // Bỏ qua nếu không có API controller
                    }

                    // Đăng ký API routes với middleware check.permission
                    Route::prefix($moduleKebab)
                        ->name("api.{$moduleKebab}.")
                        ->group(function () use ($apiController, $fullModuleName) {
                            Route::get('/datatable', [$apiController, 'datatable'])->name('datatable')
                                ->middleware("check.permission:{$fullModuleName}," . ModuleConst::ACTION_VIEW);

                            Route::post('/{id}/toggle-active', [$apiController, 'toggleActive'])->name('toggle-active')
                                ->middleware("check.permission:{$fullModuleName}," . ModuleConst::ACTION_EDIT);

                            Route::post('/{id}/reset-password', [$apiController, 'resetPassword'])->name('reset-password')
                                ->middleware("check.permission:{$fullModuleName}," . ModuleConst::ACTION_EDIT);

                            Route::delete('/{id}', [$apiController, 'destroy'])->name('destroy')
                                ->middleware("check.permission:{$fullModuleName}," . ModuleConst::ACTION_DELETE);
                        });

                    // Hỗ trợ routes API tùy chỉnh per module (tùy chọn, ví dụ api_routes.php trong dir)
                    $apiRouteFile = $dir . '/api_routes.php';
                    if (file_exists($apiRouteFile)) {
                        Route::group(['prefix' => $moduleKebab, 'name' => "api.{$moduleKebab}."], function () use ($apiRouteFile) {
                            require $apiRouteFile;
                        });
                    }
                }
            });
    }
}