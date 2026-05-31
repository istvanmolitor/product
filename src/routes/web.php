<?php

use Illuminate\Support\Facades\Route;
use Molitor\Product\Http\Controllers\ProductCategoryController;
use Molitor\Product\Http\Controllers\ProductController;
use Molitor\Product\Http\Controllers\ProductUnitController;

// Admin routes
Route::prefix('admin/product')
    ->middleware(['auth:sanctum'])
    ->name('product.')
    ->group(function () {
        // Products
        Route::get('products/select', [ProductController::class, 'select'])->middleware('permission:product');
        Route::resource('products', ProductController::class)->middleware('permission:product');

        // Product Categories
        Route::resource('product-categories', ProductCategoryController::class)->middleware('permission:product');

        // Product Units
        Route::resource('product-units', ProductUnitController::class)->middleware('permission:product_unit');
    });
