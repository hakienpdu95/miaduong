<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
// use App\Http\Livewire\Auth\Login;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\UploadController;

// Route::get('/login', Login::class)->name('login')->middleware('guest');
// Route::post('/logout', function () { Auth::logout(); return redirect()->route('login'); })->name('logout');
Route::post('/upload/temp', [UploadController::class, 'uploadTemp'])->name('upload.temp');
Route::post('/upload/temp/delete', [UploadController::class, 'deleteTemp'])->name('upload.temp.delete');

// Routes cache ngắn (dynamic nhưng cacheable, như translations)
Route::get('/lang/{locale}.json', [LanguageController::class, 'getTranslation']);

Route::get('/', function () { return view('pages.home'); })->name('home');
