<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

// Resource routes for CRUD
Route::resource('products', ProductController::class);
Route::resource('customers', CustomerController::class);
Route::resource('orders', OrderController::class);
Route::resource('inventory', InventoryController::class);

// Invoice routes
Route::resource('invoices', InvoiceController::class);
Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

// Report routes
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');
    Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
    Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
    Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
});

// Settings routes
Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

// User management routes
Route::resource('users', UserController::class);
