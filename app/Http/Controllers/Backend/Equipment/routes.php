<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Equipment\MaintenanceLogController;


Route::prefix('{equipment_qr_code_id}/maintenance-log') // Prefix: /admin/equipment/{equipment_qr_code_id}/maintenance-log
    ->name('maintenance-log.') // Name: equipment.maintenance-log.*
    ->middleware(['web', 'auth:web']) // Kế thừa middleware từ parent
    ->group(function () {
        $fullModuleName = 'maintenance_log_management'; // Để check permission
        
        // Resource CRUD cho submodule (tương tự dynamic resource, param {id} cho log)
        Route::resource('/', MaintenanceLogController::class)
            ->parameters(['' => 'id']) // Force param {id} cho log ID
            ->middleware([
                'index' => "check.permission:{$fullModuleName}," . \App\Constants\ModuleConst::ACTION_VIEW,
                'create' => "check.permission:{$fullModuleName}," . \App\Constants\ModuleConst::ACTION_CREATE,
                'store' => "check.permission:{$fullModuleName}," . \App\Constants\ModuleConst::ACTION_CREATE,
                'show' => "check.permission:{$fullModuleName}," . \App\Constants\ModuleConst::ACTION_VIEW,
                'edit' => "check.permission:{$fullModuleName}," . \App\Constants\ModuleConst::ACTION_EDIT,
                'update' => "check.permission:{$fullModuleName}," . \App\Constants\ModuleConst::ACTION_EDIT,
                'destroy' => "check.permission:{$fullModuleName}," . \App\Constants\ModuleConst::ACTION_DELETE,
            ]);
        
        // (Tùy chọn) Routes custom khác cho submodule, e.g., export
        // Route::get('/export', [MaintenanceLogController::class, 'export'])->name('export');
    });