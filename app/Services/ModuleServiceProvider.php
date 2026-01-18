<?php

namespace App\Services; // Hoặc App\Services nếu namespace cũ

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

        Route::prefix('admin')
            ->middleware(['web', 'auth:web'])
            ->group(function () use ($moduleDirs) {
                // Route dashboard (giả sử dùng controller)
                Route::get('/dashboard', [\App\Http\Controllers\Backend\DashboardController::class, 'index'])->name('dashboard');

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
    }
}