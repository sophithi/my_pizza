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
use App\Http\Controllers\ProfileController;

// Health check route
Route::get('/health', function () {
    $dbStatus = 'unknown';
    try {
        \DB::connection()->getPDO();
        $dbStatus = 'connected (' . \DB::connection()->getDatabaseName() . ')';
    } catch (\Exception $e) {
        $dbStatus = 'ERROR: ' . $e->getMessage();
    }
    return response()->json([
        'status' => 'ok',
        'php' => PHP_VERSION,
        'laravel' => app()->version(),
        'db' => $dbStatus,
        'env' => app()->environment(),
        'debug' => config('app.debug'),
        'key_set' => !empty(config('app.key')),
        'session' => config('session.driver'),
        'cache' => config('cache.default'),
    ]);
});

// Authentication routes (accessible to everyone)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Protected routes (require authentication)
Route::middleware('auth')->group(function () {
    // Common routes for all authenticated users
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/activity-log', [AuthController::class, 'activityLog'])->name('activity-log');
    Route::get('/session-info', [AuthController::class, 'sessionInfo'])->name('session-info');

    // Profile management (all users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ============================================
    // ADMIN ONLY - User create/edit/delete
    // ============================================
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['index', 'show']);
    });

    // ============================================
    // ADMIN & MANAGER - View users (index + show)
    // ============================================
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });

    // ============================================
    // ADMIN & MANAGER - Full access except user management
    // ============================================
    Route::middleware('role:admin,manager')->group(function () {
        // Product management
        Route::resource('products', ProductController::class);

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
    // STAFF (OFFICE) - Create orders & view reports
    // ============================================
    Route::middleware('role:staff,manager,admin')->group(function () {
        // Customer management
        Route::resource('customers', CustomerController::class);

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

    // ============================================
    // STAFF (INVENTORY) - View orders, invoices, inventory
    // ============================================
    Route::middleware('role:staff_inventory,manager,admin')->group(function () {
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        // Preparation workflow
        Route::post('orders/{order}/prepare', [OrderController::class, 'prepare'])->name('orders.prepare');
        Route::post('orders/{order}/ready', [OrderController::class, 'ready'])->name('orders.ready');

        // View & print invoices
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

        // View inventory
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('inventory/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');

        // View reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        });
    });
});