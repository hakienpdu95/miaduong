<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LanguageController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Route::post('/logout', function () { Auth::logout(); return redirect()->route('login'); })->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('backend.dashboard');
    })->name('admin.dashboard');
});

Route::get('/lang/{locale}.json', [LanguageController::class, 'getTranslation']);

Route::get('/', function () { return view('pages.home'); })->name('home');
