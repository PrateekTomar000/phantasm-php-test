<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;

// Customer routes
Route::post('customer/register', [CustomerController::class, 'register']);
Route::post('customer/login', [CustomerController::class, 'login']);
Route::get('customer/profile', [CustomerController::class, 'profile'])->middleware('auth:api');
Route::put('customer/address', [CustomerController::class, 'updateAddress'])->middleware('auth:api');

// Admin product routes – only JWT auth now
Route::prefix('admin')->middleware('auth:api')->group(function () {
    Route::post('products', [ProductController::class, 'create']);  // create product
    Route::get('products', [ProductController::class, 'getAll']);    // list products
    Route::get('products/{id}', [ProductController::class, 'getById']); // single product
});

// Brand & Category API (JWT protected)
Route::middleware('auth:api')->get('/brands', [BrandController::class, 'getAll']);
Route::middleware('auth:api')->get('/categories', [CategoryController::class, 'getAll']);