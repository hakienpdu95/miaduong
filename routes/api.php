<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UserController;

Route::middleware('api')->group(function () {
    Route::get('/units/datatable', [UnitController::class, 'datatable'])->name('api.units.datatable');
    Route::delete('/units/{id}', [UnitController::class, 'destroy'])->name('api.units.destroy');

    Route::get('/users/datatable', [UserController::class, 'datatable'])->name('api.users.datatable');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('api.users.destroy');
});