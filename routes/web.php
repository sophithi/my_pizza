<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\AuthController;

// Authentication routes (accessible to everyone)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Protected routes (require authentication)
Route::middleware('auth')->group(function () {
    // Common routes for all authenticated users
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/activity-log', [AuthController::class, 'activityLog'])->name('activity-log');
    Route::get('/session-info', [AuthController::class, 'sessionInfo'])->name('session-info');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ============================================
    // ADMIN ROUTES - Full access to everything
    // ============================================
    Route::middleware('role:admin')->group(function () {
        // Product management
        Route::resource('products', ProductController::class);

        // Customer management
        Route::resource('customers', CustomerController::class);

        // Inventory management
        Route::resource('inventory', InventoryController::class);
        Route::post('inventory/{inventory}/quick-update', [InventoryController::class, 'quickUpdate'])->name('inventory.quick-update');

        // Invoice management
        Route::resource('invoices', InvoiceController::class);
        Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

        // Settings management
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/exchange-rate', [SettingController::class, 'updateExchangeRate'])->name('settings.exchange_rate');

        // User management
        Route::resource('users', UserController::class);

        // Payment management
        Route::resource('payments', PaymentController::class);

        // Purchase management
        Route::resource('purchases', PurchaseController::class);
        Route::get('purchasing', [PurchaseController::class, 'index'])->name('purchasing');

        // Delivery management
        Route::resource('deliveries', DeliveryController::class);

        // Order management (full CRUD)
        Route::resource('orders', OrderController::class);
        Route::post('orders/{order}/payments', [PaymentController::class, 'recordOrderPayment'])->name('orders.payments.store');

        // All reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        });
    });

    // ============================================
    // MANAGER ROUTES - Orders & Reports only
    // ============================================
    Route::middleware('role:manager,admin')->group(function () {
        // Order management (full CRUD)
        Route::resource('orders', OrderController::class);
        Route::post('orders/{order}/payments', [PaymentController::class, 'recordOrderPayment'])->name('orders.payments.store');

        // View and manage deliveries for orders
        Route::get('deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('deliveries/{delivery}', [DeliveryController::class, 'show'])->name('deliveries.show');
        Route::post('deliveries/{delivery}/mark-out-for-delivery', [DeliveryController::class, 'markOutForDelivery'])->name('deliveries.mark-out');
        Route::post('deliveries/{delivery}/mark-delivered', [DeliveryController::class, 'markDelivered'])->name('deliveries.mark-delivered');
        Route::post('deliveries/{delivery}/cancel', [DeliveryController::class, 'cancel'])->name('deliveries.cancel');

        // All reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        });
    });

    // ============================================
    // STAFF ROUTES - Can create orders & view reports
    // ============================================
    Route::middleware('role:staff,manager,admin')->group(function () {
        // Create orders only (view & store)
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        // View reports only
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        });
    });
});