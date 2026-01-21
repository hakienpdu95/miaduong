<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SerialNumberController;

// Route::get('/', function () { return view('pages.home'); })->name('home');

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/serial/{serial_number}', [SerialNumberController::class, 'getSerialNumber'])
        ->name('serial_number.show')
        ->where('serial_number', '[A-Z0-9-]+');

Route::get('/lang/{locale}.json', [LanguageController::class, 'getTranslation']);


