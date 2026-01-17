<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EnterpriseController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostRedirectController;
use App\Http\Controllers\Api\MorenewsZoneController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StandardController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\EditorImageUploadController;
use App\Models\Ward;

Route::get('/roles/fetch', [RoleController::class, 'fetchData'])->name('api.roles.fetch');
Route::get('/users/fetch', [UserController::class, 'fetchData'])->name('api.users.fetch');
Route::get('/enterprises/fetch', [EnterpriseController::class, 'fetchData'])->name('api.enterprises.fetch');
Route::get('/categories/fetch', [CategoryController::class, 'fetchData'])->name('api.categories.fetch');
Route::get('/posts/fetch', [PostController::class, 'fetchData'])->name('api.posts.fetch');
Route::get('/post-redirects/fetch', [PostRedirectController::class, 'fetchData'])->name('api.post-redirects.fetch');
Route::get('/products/fetch', [ProductController::class, 'fetchData'])->name('api.products.fetch');
Route::get('/standards/fetch', [StandardController::class, 'fetchData'])->name('api.standards.fetch');
Route::get('/events/fetch', [EventController::class, 'fetchData'])->name('api.events.fetch');

Route::get('/upload/images/load', [ImageUploadController::class, 'load'])->name('api.upload.images.load');
Route::post('/upload/images', [ImageUploadController::class, 'upload'])->name('api.upload.images');
Route::delete('/upload/images/revert', [ImageUploadController::class, 'revert'])->name('api.upload.revert.images');

Route::post('/upload/editor-images', [EditorImageUploadController::class, 'upload'])->name('api.upload.editor-images');
Route::delete('/upload/editor-images/revert', [EditorImageUploadController::class, 'revert'])->name('api.upload.editor-images.revert');

Route::get('/morenews-zone-{page}.html', [MorenewsZoneController::class, 'moreNewsZone'])->name('api.morenews.zone');
Route::get('/morenews-zone-{categoryid}-{page}.html', [MorenewsZoneController::class, 'moreNewsZoneCategory'])->name('api.morenews.zone.category');

Route::get('/wards/{province_code}', function ($province_code) {
    $wards = Cache::remember("wards_{$province_code}", 3600, function () use ($province_code) { // Cache 1 giờ
        return Ward::select('ward_code', 'name', 'province_code')
            ->where('province_code', $province_code)
            ->get();
    });
    return response()->json($wards);
});