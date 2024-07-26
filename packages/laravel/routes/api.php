<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/filter', [ProductController::class, 'filter']);
Route::get('/products/{sku}', [ProductController::class, 'show']);

Route::get('/categories/{category}/products', [CategoryController::class, 'products']);

Route::post('/orders', [OrderController::class, 'store']);
