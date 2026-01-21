<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MaintenanceTypeController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\EquipmentQrCodeController;
use App\Http\Controllers\Api\MaintenanceLogController;

Route::middleware('api')->group(function () {
    Route::get('/units/datatable', [UnitController::class, 'datatable'])->name('api.units.datatable');
    Route::delete('/units/{id}', [UnitController::class, 'destroy'])->name('api.units.destroy');

    Route::get('/users/datatable', [UserController::class, 'datatable'])->name('api.users.datatable');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('api.users.destroy');
    Route::post('/users/toggle-active', [UserController::class, 'toggleActive'])->name('api.users.toggle-active');
    Route::post('/users/reset-password', [UserController::class, 'resetPassword'])->name('api.users.reset-password');

    Route::get('/maintenance-types/datatable', [MaintenanceTypeController::class, 'datatable'])->name('api.maintenance-types.datatable');
    Route::delete('/maintenance-types/{id}', [MaintenanceTypeController::class, 'destroy'])->name('api.maintenance-types.destroy');

    Route::get('/equipments/datatable', [EquipmentController::class, 'datatable'])->name('api.equipments.datatable');
    Route::delete('/equipments/{id}', [EquipmentController::class, 'destroy'])->name('api.equipments.destroy');

    Route::get('/equipment-qr-codes/{equipmentId}/datatable', [EquipmentQrCodeController::class, 'datatable'])->name('api.equipment-qr-codes.datatable');

    Route::get('/equipment/{equipmentQrCodeId}/datatable', [MaintenanceLogController::class, 'datatable'])->name('api.maintenance-log.datatable');
    Route::delete('/equipment/{equipmentQrCodeId}/maintenance-log/{id}', [MaintenanceLogController::class, 'destroy'])->name('api.maintenance-log.destroy');
});