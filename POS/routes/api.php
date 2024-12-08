<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::post('/adminregister', [AuthController::class, 'adminregister']);
Route::post('/adminlogin', [AuthController::class, 'adminlogin']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes that require authentication
Route::middleware('auth:sanctum')->group(function () {

    // Admin Routes
    Route::get('/admins/{id}', [AuthController::class, 'adminindex']);
    Route::get('/users', [UserController::class, 'index']); // Fetch users for the authenticated admin
    Route::get('/users/{id}', [UserController::class, 'show']); // Get details of a specific user

    // User Management Routes (for authenticated admin to manage users)
    Route::post('/users', [UserController::class, 'store']); // Create a new user
    Route::put('/users/{id}', [UserController::class, 'update']); // Update a user's details
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // Delete a user

    // Category Management Routes
    Route::get('/categories', [CategoryController::class, 'index']); // List categories
    Route::post('/categories', [CategoryController::class, 'store']); // Create a new category
    Route::put('/categories/{id}', [CategoryController::class, 'update']); // Update a category
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']); // Delete a category

    // Product Management Routes
    Route::get('/products', [ProductController::class, 'index']); // List products
    Route::post('/products', [ProductController::class, 'store']); // Create a new product
    Route::put('/products/{id}', [ProductController::class, 'update']); // Update a product
    Route::delete('/products/{id}', [ProductController::class, 'destroy']); // Delete a product

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout']);
});
