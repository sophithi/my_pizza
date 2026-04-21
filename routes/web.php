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

        // Inventory management (full CRUD)
        Route::resource('inventory', InventoryController::class);
        Route::post('inventory/{inventory}/quick-update', [InventoryController::class, 'quickUpdate'])->name('inventory.quick-update');


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

        // Order delete (only admin/manager)
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });

    // ============================================
    // ALL USERS - Orders (view, create, edit)
    // ============================================
    Route::middleware('role:admin,manager,staff,')->group(function () {
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::post('orders/{order}/payments', [PaymentController::class, 'recordOrderPayment'])->name('orders.payments.store');

        // Preparation workflow
        Route::post('orders/{order}/prepare', [OrderController::class, 'prepare'])->name('orders.prepare');
        Route::post('orders/{order}/ready', [OrderController::class, 'ready'])->name('orders.ready');
    });

    // ============================================
    // INVOICES - Admin, Manager, Staff Inventory can create; all can view
    // ============================================
    Route::middleware('role:admin,manager,staff_inventory')->group(function () {
        Route::resource('invoices', InvoiceController::class);
        Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    });

    // Print & Sticker printing (only Admin & Staff Inventory)
    Route::middleware('role:admin,staff_inventory')->group(function () {
        Route::get('print/index', [InvoiceController::class, 'printIndex'])->name('print.index');
        Route::get('print/{invoice}/prep', [InvoiceController::class, 'stickerPrep'])->name('print.prep');
        Route::get('print/{invoice}/customer', [InvoiceController::class, 'stickerCustomer'])->name('print.customer');
    });

    // Staff (office) - view invoices only
    Route::middleware('role:staff')->group(function () {
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    });

    // ============================================
    // STAFF (OFFICE) - Customers
    // ============================================
    Route::middleware('role:staff,manager,admin')->group(function () {
        Route::resource('customers', CustomerController::class);
    });

    // ============================================
    // STAFF (INVENTORY) - View inventory
    // ============================================
    Route::middleware('role:staff_inventory')->group(function () {
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('inventory/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');
    });

    // ============================================
    // ALL USERS - Reports
    // ============================================
    Route::middleware('role:admin,manager,staff,staff_inventory')->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        });
    });

    // Admin & Manager - Extra reports
    Route::middleware('role:admin,manager')->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        });
    });
});