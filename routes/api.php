<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResellerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);
Route::get('/categories', [ServiceController::class, 'categories']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', [UserController::class, 'profile']);
    Route::put('/user', [UserController::class, 'updateProfile']);
    Route::get('/balance', [UserController::class, 'balance']);
    
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/orders/stats', [OrderController::class, 'stats']);
    
    Route::post('/deposit', [PaymentController::class, 'deposit']);
    Route::get('/transactions', [PaymentController::class, 'transactions']);
    
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/tickets/{id}', [TicketController::class, 'show']);
    
    Route::prefix('reseller')->group(function () {
        Route::get('/services', [ResellerController::class, 'services']);
        Route::post('/orders', [ResellerController::class, 'placeOrder']);
        Route::get('/orders/{id}/status', [ResellerController::class, 'status']);
        Route::get('/balance', [ResellerController::class, 'balance']);
    });
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    
    Route::get('/services', [AdminServiceController::class, 'index']);
    Route::post('/services', [AdminServiceController::class, 'store']);
    Route::put('/services/{id}', [AdminServiceController::class, 'update']);
    Route::delete('/services/{id}', [AdminServiceController::class, 'destroy']);
    Route::post('/services/bulk-import', [AdminServiceController::class, 'bulkImport']);
    
    Route::apiResource('categories', CategoryController::class);
    
    Route::get('/orders', [OrderController::class, 'adminIndex']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    
    Route::get('/users', [UserController::class, 'adminIndex']);
    Route::put('/users/{id}/balance', [UserController::class, 'updateBalance']);
    Route::put('/users/{id}/role', [UserController::class, 'updateRole']);
    
    Route::get('/reports/orders', [ReportController::class, 'orders']);
    Route::get('/reports/revenue', [ReportController::class, 'revenue']);
    Route::get('/reports/users', [ReportController::class, 'users']);
});

Route::post('/mpesa/callback', [PaymentController::class, 'callback']);
