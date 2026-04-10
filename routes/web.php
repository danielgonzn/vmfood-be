<?php

use App\Http\Controllers\Api\V1\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\InquiryReportExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/login', function () {
    return redirect('/admin/login');
});

Route::middleware(['auth', 'admin'])->prefix('admin/reports')->name('admin.reports.')->group(function (): void {
    Route::get('/inquiries/csv', [InquiryReportExportController::class, 'csv'])->name('inquiries.csv');
    Route::get('/inquiries/xlsx', [InquiryReportExportController::class, 'xlsx'])->name('inquiries.xlsx');
});

Route::middleware(['auth', 'admin'])->prefix('admin/products')->name('admin.products.')->group(function (): void {
    Route::get('/import-template', [AdminProductController::class, 'importTemplate'])->name('import-template');
});
