<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\DashboardApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes – FitPulse Tracker Lab 6
| Real-Time Inventory Tracking
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Product resource routes (CRUD)
    Route::resource('products', ProductController::class);

    // Inventory Transactions – static routes BEFORE parameterized ones
    Route::get('/transactions', [InventoryTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [InventoryTransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [InventoryTransactionController::class, 'store'])->name('transactions.store');

    // Stock Adjustment (must be before {transaction} binding)
    Route::get('/transactions/adjustment/form', [InventoryTransactionController::class, 'adjustmentForm'])->name('transactions.adjustment');
    Route::post('/transactions/adjustment', [InventoryTransactionController::class, 'processAdjustment'])->name('transactions.adjustment.process');

    Route::get('/transactions/{transaction}', [InventoryTransactionController::class, 'show'])->name('transactions.show');
    Route::delete('/transactions/{transaction}', [InventoryTransactionController::class, 'destroy'])->name('transactions.destroy');

    // Mark all notifications as read
    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    })->name('notifications.read-all');

    // API endpoints for real-time dashboard (AJAX polling)
    Route::prefix('api')->group(function () {
        Route::get('/dashboard/stats', [DashboardApiController::class, 'stats'])->name('api.dashboard.stats');
        Route::get('/products/low-stock', [DashboardApiController::class, 'lowStock'])->name('api.products.low-stock');
        Route::get('/transactions/recent', [DashboardApiController::class, 'recentTransactions'])->name('api.transactions.recent');
        Route::get('/products/{product}/quantity', [DashboardApiController::class, 'productQuantity'])->name('api.products.quantity');
    });
});

// Profile routes (from Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
