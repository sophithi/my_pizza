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

        // Permanently delete an invoice from the trash (irreversible — admin only)
        Route::delete('invoices/{id}/force-delete', [InvoiceController::class, 'forceDelete'])->name('invoices.force-delete');
    });

    // Allow admin, manager, office staff and inventory staff to fully manage inventory
    Route::middleware('role:admin,manager,staff,staff_inventory')->group(function () {
        Route::resource('inventory', InventoryController::class)->except(['index', 'show']);
        Route::post('inventory/{inventory}/quick-update', [InventoryController::class, 'quickUpdate'])->name('inventory.quick-update');
        Route::post('inventory/{inventory}/restock', [InventoryController::class, 'restock'])->name('inventory.restock');
        Route::post('inventory/{inventory}/reduce', [InventoryController::class, 'reduce'])->name('inventory.reduce');
    });

    // Stock viewing (view-only) — same roles as above, plus auditor
    Route::middleware('role:admin,manager,staff,staff_inventory,auditor')->group(function () {
        Route::resource('inventory', InventoryController::class)->only(['index', 'show']);
        Route::get('inventory/export/excel', [InventoryController::class, 'exportExcel'])->name('inventory.export.excel');
        Route::get('inventory/export/pdf', [InventoryController::class, 'exportPdf'])->name('inventory.export.pdf');
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


        // Payment management (write)
        Route::resource('payments', PaymentController::class)->only(['store', 'update']);
        Route::post('orders/{order}/payments', [PaymentController::class, 'recordOrderPayment'])->name('orders.payments.store');

        // Purchase / expense management (write)
        Route::resource('purchases', PurchaseController::class)->except(['index', 'show']);

        // Order delete (only admin/manager)
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

        // Deleted invoices — view & recover (only admin/manager)
        Route::get('invoices/trash', [InvoiceController::class, 'trashed'])->name('invoices.trash');
        Route::post('invoices/{id}/restore', [InvoiceController::class, 'restore'])->name('invoices.restore');

        // Close this month's invoice numbering (only admin/manager)
        Route::post('invoices/close-period', [InvoiceController::class, 'closePeriod'])->name('invoices.close-period');
        // Undo an accidental close, only while nothing's been invoiced under the new period yet
        Route::post('invoices/undo-close-period', [InvoiceController::class, 'undoClosePeriod'])->name('invoices.undo-close-period');
        // Force-merge the active period back into the last closed one, renumbering its invoices to continue that sequence
        Route::post('invoices/merge-back-period', [InvoiceController::class, 'mergeBackPeriod'])->name('invoices.merge-back-period');

        // Invoice delete (only admin/manager — staff/staff_inventory can't recover from trash)
        Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    });

    // ============================================
    // ADMIN, MANAGER & AUDITOR - Payments & Expenses (view-only for auditor)
    // ============================================
    Route::middleware('role:admin,manager,auditor')->group(function () {
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/export/excel', [PaymentController::class, 'exportExcel'])->name('payments.export.excel');
        Route::get('payments/export/pdf', [PaymentController::class, 'exportPdf'])->name('payments.export.pdf');

        Route::resource('purchases', PurchaseController::class)->only(['index', 'show']);
        Route::get('purchasing', [PurchaseController::class, 'index'])->name('purchasing');
    });

    // Product management
    Route::middleware('role:admin,manager,staff')->group(function () {
        Route::get('products/images/{filename}', [ProductController::class, 'image'])
            ->where('filename', '.*')
            ->name('products.image');
        Route::resource('products', ProductController::class);
        Route::get('products/export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
        Route::get('products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
    });

    // Delivery management
    Route::middleware('role:admin,manager,staff')->group(function () {
        Route::resource('deliveries', DeliveryController::class);
        Route::patch('deliveries/{delivery}/orders/{order}', [DeliveryController::class, 'updateOrderPacking'])->name('deliveries.orders.update-packing');
        Route::get('deliveries/{delivery}/export/excel', [DeliveryController::class, 'exportExcel'])->name('deliveries.export.excel');
        Route::get('deliveries/{delivery}/export/pdf', [DeliveryController::class, 'exportPdf'])->name('deliveries.export.pdf');
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

    Route::middleware('role:admin,manager,staff,staff_inventory')->group(function () {
        Route::resource('invoices', InvoiceController::class)->only(['edit', 'update']);
        Route::post('invoices/{invoice}/toggle-printed', [InvoiceController::class, 'togglePrinted'])->name('invoices.toggle-printed');
    });

    // Invoice viewing/printing/export — read-only, includes auditor
    Route::middleware('role:admin,manager,staff,staff_inventory,auditor')->group(function () {
        Route::get('invoices/export/report', [InvoiceController::class, 'exportReport'])->name('invoices.export');
        Route::get('invoices/print-bulk', [InvoiceController::class, 'printBulk'])->name('invoices.print-bulk');
        Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    });

    // Packing labels
    Route::middleware('role:admin,manager,staff,staff_inventory')->group(function () {
        Route::get('packing/index', [InvoiceController::class, 'printIndex'])->name('packing.index');
        Route::post('packing/{invoice}/complete', [InvoiceController::class, 'markPackingCompleted'])->name('packing.complete');
        Route::get('packing/{invoice}/prep', [InvoiceController::class, 'stickerPrep'])->name('packing.prep');
        Route::get('packing/{invoice}/ready', [InvoiceController::class, 'stickerReady'])->name('packing.delivery_pizza');
        Route::get('packing/{invoice}/mayo', [InvoiceController::class, 'stickerMayo'])->name('packing.delivery_mayo');
        Route::get('packing/{invoice}/tamon', [InvoiceController::class, 'stickerTamon'])->name('packing.delivery_tamon');

        Route::get('packing/{invoice}/customer', [InvoiceController::class, 'stickerCustomer'])->name('packing.customer');
        Route::get('packing/{invoice}/customer-mayo', [InvoiceController::class, 'stickerCustomerMayo'])->name('packing.customer_mayo');
        Route::get('packing/{invoice}/customer-tamon', [InvoiceController::class, 'stickerCustomerTamon'])->name('packing.customer_tamon');
        // routes/web.php
        Route::get('/packing/sticker/{invoice}/download', [InvoiceController::class, 'downloadSticker'])
            ->name('packing.sticker.download');
    });

    // Invoice index/show view access. Staff can only view; admin/manager/staff_inventory use full routes above for write actions.
    Route::middleware('role:admin,manager,staff,staff_inventory,auditor')->group(function () {
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    });

    // ============================================
    // STAFF (OFFICE) - Customers
    // ============================================
    Route::middleware('role:staff,manager,admin')->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::get('customers/export/excel', [CustomerController::class, 'exportExcel'])->name('customers.export.excel');
        Route::get('customers/export/pdf', [CustomerController::class, 'exportPdf'])->name('customers.export.pdf');
    });

    // ============================================
    // ALL USERS - Reports
    // ============================================
    Route::middleware('role:admin,manager,staff,staff_inventory,auditor')->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/sales/export/excel', [ReportController::class, 'exportSalesExcel'])->name('sales.export.excel');
            Route::get('/sales/export/pdf', [ReportController::class, 'exportSalesPdf'])->name('sales.export.pdf');
        });
    });

    // Admin, Manager & Auditor - Extra reports
    Route::middleware('role:admin,manager,auditor')->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/daily', [ReportController::class, 'daily'])->name('daily');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        });
    });
});
