<?php

use Illuminate\Support\Facades\Route;
use Molitor\Product\Http\Controllers\ProductCategoryController;
use Molitor\Product\Http\Controllers\ProductController;
use Molitor\Product\Http\Controllers\ProductUnitController;

// Admin routes
Route::prefix('admin/product')
    ->middleware(['api', 'auth:sanctum'])
    ->name('product.')
    ->group(function () {
        // Products
        Route::resource('products', ProductController::class);

        // Product Categories
        Route::resource('product-categories', ProductCategoryController::class);

        // Product Units
        Route::resource('product-units', ProductUnitController::class);
    });

