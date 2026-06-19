<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductFieldController;
use App\Http\Controllers\Api\ProductVariationController;


Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::post('/product-fields', [ProductFieldController::class, 'store']);
Route::put('/product-fields/{id}', [ProductFieldController::class, 'update']);
Route::post('/products/{productId}/variations', [ProductVariationController::class, 'store']);
Route::put('/variations/{id}', [ProductVariationController::class, 'update']);