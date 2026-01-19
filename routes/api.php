<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\UnitController;

Route::middleware('api')->group(function () {
    Route::get('/units/datatable', [UnitController::class, 'datatable'])->name('api.units.datatable');
    Route::delete('/units/{id}', [UnitController::class, 'destroy'])->name('api.units.destroy');
});