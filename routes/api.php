<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\V1\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\V1\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Api\V1\Admin\UploadController as AdminUploadController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Public\InquiryController;
use App\Http\Controllers\Api\V1\Public\ProductController as PublicProductController;
use App\Http\Controllers\Api\V1\Public\TaxonomyController;
use App\Http\Controllers\Api\V1\Public\WebContentController;

Route::prefix('v1')->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:30,1');
        Route::middleware('auth:sanctum')->group(function (): void {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    Route::prefix('catalog')->group(function (): void {
        Route::get('/categories', [TaxonomyController::class, 'categories']);
        Route::get('/brands', [TaxonomyController::class, 'brands']);
        Route::get('/products', [PublicProductController::class, 'index']);
        Route::get('/products/{slug}', [PublicProductController::class, 'show']);
        Route::get('/content', [WebContentController::class, 'index']);
        Route::post('/inquiries', [InquiryController::class, 'store'])->middleware('throttle:20,1');
    });

    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function (): void {
        Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats']);
        Route::get('/products/import-template', [AdminProductController::class, 'importTemplate']);
        Route::post('/products/import', [AdminProductController::class, 'import']);
        Route::apiResource('/products', AdminProductController::class);
        Route::apiResource('/categories', AdminCategoryController::class)->except(['show']);
        Route::apiResource('/brands', AdminBrandController::class)->except(['show']);
        Route::post('/uploads/image', [AdminUploadController::class, 'image']);
    });
});
