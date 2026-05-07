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

    // Allow admin, manager, office staff and inventory staff to fully manage inventory
    Route::middleware('role:admin,manager,staff,staff_inventory')->group(function () {
        Route::resource('inventory', InventoryController::class);
        Route::post('inventory/{inventory}/quick-update', [InventoryController::class, 'quickUpdate'])->name('inventory.quick-update');
        Route::post('inventory/{inventory}/restock', [InventoryController::class, 'restock'])->name('inventory.restock');
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
        // Inventory management (full CRUD)
        // NOTE: inventory resource moved to a dedicated group below so staff_inventory
        // role can also be granted full access without exposing other admin routes.


        // Payment management
        Route::resource('payments', PaymentController::class)->only(['index', 'store', 'update']);
        Route::get('payments/export/excel', [PaymentController::class, 'exportExcel'])->name('payments.export.excel');
        Route::get('payments/export/pdf', [PaymentController::class, 'exportPdf'])->name('payments.export.pdf');
        Route::post('orders/{order}/payments', [PaymentController::class, 'recordOrderPayment'])->name('orders.payments.store');

        // Purchase management
        Route::resource('purchases', PurchaseController::class);
        Route::get('purchasing', [PurchaseController::class, 'index'])->name('purchasing');

        // Order delete (only admin/manager)
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });

    // Product management
    Route::middleware('role:admin,manager,staff')->group(function () {
        Route::resource('products', ProductController::class);
    });

    // Delivery management
    Route::middleware('role:admin,manager,staff')->group(function () {
        Route::resource('deliveries', DeliveryController::class);
    });

    // ============================================
    // ALL USERS - Orders (view, create, edit)
    // ============================================
    Route::middleware('role:admin,manager,staff')->group(function () {
        Route::get('orders', function () {
            return redirect()->route('orders.create');
        })->name('orders.index');
        Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');

        // Preparation workflow
        Route::post('orders/{order}/prepare', [OrderController::class, 'prepare'])->name('orders.prepare');
        Route::post('orders/{order}/ready', [OrderController::class, 'ready'])->name('orders.ready');
    });

    // ============================================
    // INVOICES - Admin, Manager, Staff Inventory can create; all can view
    // ============================================
    Route::middleware('role:admin,manager,staff')->group(function () {
        Route::post('invoices/{invoice}/send-to-packing', [InvoiceController::class, 'sendToPacking'])->name('invoices.send-to-packing');
    });

    Route::middleware('role:admin,manager,staff_inventory')->group(function () {
        Route::get('invoices/export/report', [InvoiceController::class, 'exportReport'])->name('invoices.export');
        Route::resource('invoices', InvoiceController::class)->only(['edit', 'update', 'destroy']);
        Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    });

    // Packing labels
    Route::middleware('role:admin,manager,staff,staff_inventory')->group(function () {
        Route::get('packing/index', [InvoiceController::class, 'printIndex'])->name('packing.index');
        Route::post('packing/{invoice}/complete', [InvoiceController::class, 'markPackingCompleted'])->name('packing.complete');
        Route::get('packing/{invoice}/prep', [InvoiceController::class, 'stickerPrep'])->name('packing.prep');
        Route::get('packing/{invoice}/customer', [InvoiceController::class, 'stickerCustomer'])->name('packing.customer');

    });

    // Invoice index/show view access. Staff can only view; admin/manager/staff_inventory use full routes above for write actions.
    Route::middleware('role:admin,manager,staff,staff_inventory')->group(function () {
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
            Route::get('/daily', [ReportController::class, 'daily'])->name('daily');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        });
    });
});
