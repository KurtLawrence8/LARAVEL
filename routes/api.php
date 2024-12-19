<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/adminregister', [AuthController::class, 'adminregister']);
Route::post('/adminlogin', [AuthController::class, 'adminlogin']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/admins/{id}', [AuthController::class, 'adminupdate']);
    Route::get('/admins/{id}', [AuthController::class, 'adminindex']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);

    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    Route::post('/scan-product', [OrderController::class, 'scanProduct']);
    Route::post('/pay', [OrderController::class, 'pay']);
    Route::get('/orders', [OrderController::class, 'index']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/adminlogout', [AuthController::class, 'adminlogout']);
});
