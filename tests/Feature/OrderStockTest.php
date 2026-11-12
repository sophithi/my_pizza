<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Services\OrderStockService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderStockTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Isolated schema: the existing legacy migrations currently fail on SQLite.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('products', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('category')->nullable(); $t->string('unit')->nullable();
        });
        Schema::create('customers', function (Blueprint $t) {
            $t->id(); $t->string('name');
        });
        Schema::create('orders', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('customer_id')->nullable();
            $t->string('status')->default('pending'); $t->dateTime('order_date')->nullable();
            $t->string('invoice_template')->default('family'); $t->string('sticker_template')->default('family');
            $t->boolean('stock_deducted')->default(false);
            $t->unsignedBigInteger('deleted_by')->nullable(); $t->string('delete_reason')->nullable();
            $t->softDeletes(); $t->timestamps();
        });
        Schema::create('invoices', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('order_id');
            $t->string('invoice_number')->nullable();
            $t->string('status')->default('draft');
            $t->timestamp('packing_sent_at')->nullable(); $t->timestamp('packing_completed_at')->nullable();
            $t->unsignedBigInteger('packing_completed_by')->nullable();
            $t->json('packing_checklist')->nullable();
            $t->unsignedBigInteger('deleted_by')->nullable(); $t->string('delete_reason')->nullable();
            $t->softDeletes(); $t->timestamps();
        });
        Schema::create('order_items', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('order_id'); $t->unsignedBigInteger('product_id');
            $t->string('item_type'); $t->integer('quantity'); $t->timestamps();
        });
        Schema::create('inventories', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('product_id')->unique(); $t->integer('quantity'); $t->timestamps();
        });
        Schema::create('inventory_movements', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('inventory_id'); $t->unsignedBigInteger('product_id');
            $t->unsignedBigInteger('order_id'); $t->unsignedBigInteger('user_id')->nullable();
            $t->string('type'); $t->string('item_type')->nullable(); $t->integer('quantity_change');
            $t->integer('quantity_before'); $t->integer('quantity_after'); $t->string('note')->nullable(); $t->timestamps();
        });
    }

    private function order(): Order
    {
        $order = Order::create([]);
        $order->items()->create(['product_id' => 1, 'item_type' => 'sale', 'quantity' => 10]);
        $order->items()->create(['product_id' => 1, 'item_type' => 'free', 'quantity' => 2]);
        Inventory::create(['product_id' => 1, 'quantity' => 100]);
        return $order;
    }

    public function test_order_print_routes_follow_one_saved_template_type(): void
    {
        $order = Order::create(['invoice_template' => 'mayo', 'sticker_template' => 'tamon']);
        $this->assertSame('packing.customer_mayo', $order->fresh()->invoicePrintRoute());
        $this->assertSame('packing.delivery_mayo', $order->fresh()->stickerPrintRoute());

        $legacy = Order::create([]);
        $this->assertSame('packing.customer', $legacy->fresh()->invoicePrintRoute());
        $this->assertSame('packing.delivery_pizza', $legacy->fresh()->stickerPrintRoute());
    }

    public function test_inventory_staff_cannot_edit_invoice_financial_status(): void
    {
        $order = $this->order();
        $invoice = \App\Models\Invoice::create(['order_id' => $order->id]);
        $this->actingAs((new \App\Models\User)->forceFill(['id' => 77, 'role' => 'staff_inventory']));
        $this->get(route('invoices.edit', $invoice))->assertForbidden();
        $this->put(route('invoices.update', $invoice), ['status' => 'paid'])->assertForbidden();
        $this->assertSame('draft', $invoice->fresh()->status);
    }

    public function test_inventory_staff_can_access_inventory_report(): void
    {
        $this->actingAs((new \App\Models\User)->forceFill(['id' => 77, 'role' => 'staff_inventory']));
        $this->partialMock(\App\Http\Controllers\ReportController::class, function ($mock) {
            $mock->shouldReceive('inventory')->once()->andReturn(response('inventory-report'));
        });
        $this->get(route('reports.inventory'))->assertOk()->assertSee('inventory-report');
        $this->get(route('reports.customers'))->assertForbidden();
    }

    public function test_packing_requires_dispatch_and_preserves_first_completion_actor(): void
    {
        $order = $this->order();
        $invoice = \App\Models\Invoice::create(['order_id' => $order->id]);
        $this->actingAs((new \App\Models\User)->forceFill(['id' => 77, 'role' => 'staff_inventory']));
        $this->post(route('packing.complete', $invoice))->assertSessionHasErrors('packing');
        $this->assertNull($invoice->fresh()->packing_completed_at);
        $invoice->update(['packing_sent_at' => now()]);
        $this->post(route('packing.complete', $invoice))->assertSessionHasErrors('packing');
        $invoice->update(['packing_checklist' => [
            'items' => $order->items->mapWithKeys(fn ($item) => [$item->id => true])->all(),
        ]]);
        $this->post(route('packing.complete', $invoice))->assertSessionHasNoErrors();
        $this->postJson(route('packing.complete', $invoice))->assertOk()->assertJson(['completed' => true]);
        $this->assertSame(77, (int) $invoice->fresh()->packing_completed_by);
        $completedAt = $invoice->fresh()->packing_completed_at;
        $this->actingAs((new \App\Models\User)->forceFill(['id' => 88, 'role' => 'staff_inventory']));
        $this->post(route('packing.complete', $invoice))->assertRedirect();
        $this->assertSame(77, (int) $invoice->fresh()->packing_completed_by);
        $this->assertEquals($completedAt, $invoice->fresh()->packing_completed_at);
        $this->assertSame(100, (int) Inventory::first()->quantity);
    }

    public function test_packing_filters_range_status_and_invoice_search_use_the_same_counts(): void
    {
        $order = Order::create([]);
        $order->invoice()->create(['invoice_number' => 'INV-101', 'packing_sent_at' => '2026-10-20 10:00:00']);
        $order->invoice()->create(['invoice_number' => 'INV-102', 'packing_sent_at' => '2026-10-21 10:00:00', 'packing_completed_at' => '2026-10-21 11:00:00']);
        $order->invoice()->create(['invoice_number' => 'INV-103', 'packing_sent_at' => '2026-10-22 10:00:00']);

        $controller = app(\App\Http\Controllers\InvoiceController::class);
        $request = \Illuminate\Http\Request::create('/packing/index', 'GET', [
            'period' => 'custom', 'from_date' => '2026-10-20', 'to_date' => '2026-10-21',
        ]);
        $data = $controller->printIndex($request)->getData();
        $this->assertSame(1, $data['packingPendingCount']);
        $this->assertSame(1, $data['packingCompletedCount']);
        $this->assertSame(2, $data['invoices']->total());

        $request = \Illuminate\Http\Request::create('/packing/index', 'GET', [
            'period' => 'custom', 'from_date' => '2026-10-20', 'to_date' => '2026-10-21',
            'status' => 'completed', 'search' => 'INV-102',
        ]);
        $data = $controller->printIndex($request)->getData();
        $this->assertSame(0, $data['packingPendingCount']);
        $this->assertSame(1, $data['packingCompletedCount']);
        $this->assertSame(1, $data['invoices']->total());
    }

    public function test_packing_checklist_saves_item_and_step_without_changing_stock(): void
    {
        $order = $this->order();
        $invoice = \App\Models\Invoice::create(['order_id' => $order->id, 'packing_sent_at' => now()]);
        $revision = $invoice->packing_sent_at->format('Y-m-d H:i:s');
        $this->actingAs((new \App\Models\User)->forceFill(['id' => 77, 'role' => 'staff_inventory']));

        $this->patchJson(route('packing.checklist', $invoice), [
            'kind' => 'item', 'key' => (string) $order->items->first()->id,
            'checked' => true, 'revision' => $revision,
        ])->assertOk();
        $this->patchJson(route('packing.checklist', $invoice), [
            'kind' => 'step', 'key' => 'quality', 'checked' => true, 'revision' => $revision,
        ])->assertOk();
        $this->assertTrue($invoice->fresh()->packing_checklist['items'][$order->items->first()->id]);
        $this->assertTrue($invoice->fresh()->packing_checklist['steps']['quality']);
        $this->assertSame(100, (int) Inventory::first()->quantity);

        $this->patchJson(route('packing.checklist', $invoice), [
            'kind' => 'item', 'key' => '99999', 'checked' => true, 'revision' => $revision,
        ])->assertUnprocessable();
        $this->patchJson(route('packing.checklist', $invoice), [
            'kind' => 'step', 'key' => 'ready', 'checked' => true, 'revision' => 'old',
        ])->assertStatus(409);

        $invoice->update(['packing_completed_at' => now()]);
        $this->patchJson(route('packing.checklist', $invoice), [
            'kind' => 'step', 'key' => 'ready', 'checked' => true, 'revision' => $revision,
        ])->assertUnprocessable();
    }

    public function test_inventory_with_history_cannot_be_deleted_even_at_zero(): void
    {
        $order = $this->order();
        app(OrderStockService::class)->deduct($order);
        $inventory = Inventory::first();
        $inventory->update(['quantity' => 0]);
        try {
            app(\App\Http\Controllers\InventoryController::class)->destroy($inventory);
            $this->fail('Inventory history must be protected.');
        } catch (ValidationException $e) {
            $this->assertNotEmpty($e->errors()['inventory']);
            $this->assertNotNull($inventory->fresh());
            $this->assertSame(2, InventoryMovement::count());
        }
    }

    public function test_inventory_linked_to_trashed_order_cannot_be_deleted(): void
    {
        $order = $this->order();
        $order->delete();
        $inventory = Inventory::first();
        $inventory->update(['quantity' => 0]);
        $this->expectException(ValidationException::class);
        app(\App\Http\Controllers\InventoryController::class)->destroy($inventory);
    }

    public function test_unused_inventory_with_nonzero_quantity_cannot_be_deleted(): void
    {
        $inventory = Inventory::create(['product_id' => 20, 'quantity' => -1]);
        $this->expectException(ValidationException::class);
        app(\App\Http\Controllers\InventoryController::class)->destroy($inventory);
    }

    public function test_only_unused_zero_inventory_can_be_deleted(): void
    {
        $inventory = Inventory::create(['product_id' => 20, 'quantity' => 0]);
        app(\App\Http\Controllers\InventoryController::class)->destroy($inventory);
        $this->assertNull($inventory->fresh());
    }

    public function test_product_delete_cannot_bypass_inventory_protection(): void
    {
        DB::table('products')->insert(['id' => 20, 'name' => 'Pizza']);
        Inventory::create(['product_id' => 20, 'quantity' => 0]);
        try {
            app(\App\Http\Controllers\ProductController::class)->destroy(\App\Models\Product::findOrFail(20));
            $this->fail('Product deletion must not cascade into inventory.');
        } catch (ValidationException $e) {
            $this->assertNotNull(\App\Models\Product::find(20));
            $this->assertSame(1, Inventory::count());
        }
    }

    public function test_sale_and_free_deduction_return_and_restore_are_applied_once(): void
    {
        $order = $this->order();
        $stale = $order->fresh();
        $service = app(OrderStockService::class);
        $service->deduct($order);
        $service->deduct($stale);
        $this->assertEquals(88, Inventory::first()->quantity);
        $this->assertEquals(-10, InventoryMovement::where('item_type', 'sale')->sum('quantity_change'));
        $this->assertEquals(-2, InventoryMovement::where('item_type', 'free')->sum('quantity_change'));
        $service->restore($order);
        $service->restore($stale);
        $this->assertEquals(100, Inventory::first()->quantity);
        $order->delete();
        $service->deduct($order);
        $order->restore();
        $this->assertEquals(88, Inventory::first()->quantity);
        $this->assertTrue($order->fresh()->stock_deducted);
    }

    public function test_edit_records_only_delta_and_no_customer_return(): void
    {
        $order = $this->order();
        $service = app(OrderStockService::class);
        $service->deduct($order);
        DB::transaction(function () use ($service, $order) {
            $before = $service->snapshot($order);
            $order->items()->where('item_type', 'sale')->update(['quantity' => 15]);
            $service->reconcile($order, $before);
        });
        $this->assertEquals(83, Inventory::first()->quantity);
        $this->assertEquals(-5, InventoryMovement::where('type', 'order_adjust')->sum('quantity_change'));
        $this->assertEquals(0, InventoryMovement::where('type', 'order_restore')->count());
        $count = InventoryMovement::count();
        DB::transaction(fn () => $service->reconcile($order, $service->snapshot($order)));
        $this->assertEquals($count, InventoryMovement::count());
    }

    public function test_orders_can_deduct_zero_and_negative_stock_once(): void
    {
        $order = $this->order();
        Inventory::first()->update(['quantity' => 0]);
        $service = app(OrderStockService::class);
        $service->deduct($order);
        $this->assertEquals(-12, Inventory::first()->quantity);
        $service->deduct($order->fresh());
        $this->assertEquals(-12, Inventory::first()->quantity);
        $service->restore($order);
        $this->assertEquals(0, Inventory::first()->quantity);
        Inventory::first()->update(['quantity' => -100]);
        $service->deduct($order);
        $this->assertEquals(-112, Inventory::first()->quantity);
        $this->assertEquals(-2, InventoryMovement::where('type', 'order_deduct')->where('item_type', 'free')->latest('id')->first()->quantity_change);
    }

    public function test_missing_inventory_rolls_back_all_deductions(): void
    {
        $order = $this->order();
        $order->items()->create(['product_id' => 2, 'item_type' => 'sale', 'quantity' => 4]);
        try {
            app(OrderStockService::class)->deduct($order);
            $this->fail('Missing inventory must reject the operation.');
        } catch (ValidationException $e) {
            $this->assertEquals(100, Inventory::where('product_id', 1)->first()->quantity);
            $this->assertEquals(0, InventoryMovement::count());
            $this->assertFalse($order->fresh()->stock_deducted);
        }
    }

    public function test_invoice_delete_and_restore_keep_order_and_stock_together(): void
    {
        $order = $this->order();
        $invoice = \App\Models\Invoice::create(['order_id' => $order->id]);
        app(OrderStockService::class)->deduct($order);
        $controller = app(\App\Http\Controllers\InvoiceController::class);
        $controller->destroy(\Illuminate\Http\Request::create('/', 'DELETE'), $invoice);
        $this->assertTrue($invoice->fresh()->trashed());
        $this->assertTrue($order->fresh()->trashed());
        $this->assertEquals(100, Inventory::first()->quantity);
        $controller->restore($invoice->id);
        $this->assertFalse($invoice->fresh()->trashed());
        $this->assertFalse($order->fresh()->trashed());
        $this->assertEquals(88, Inventory::first()->quantity);
    }

    public function test_inventory_summary_counts_real_items_once_despite_repeated_movements(): void
    {
        $order = $this->order();
        DB::table('products')->insert(['id' => 1, 'name' => 'Pizza']);
        $order->update(['order_date' => today()]);
        $service = app(OrderStockService::class);
        $service->deduct($order);
        $service->restore($order);
        $service->deduct($order);
        $view = app(\App\Http\Controllers\InventoryController::class)->show(
            \Illuminate\Http\Request::create('/', 'GET', ['period' => 'today']), Inventory::first()
        );
        $this->assertSame(10, $view->getData()['saleQty']);
        $this->assertSame(2, $view->getData()['freeQty']);
        $order->delete();
        $view = app(\App\Http\Controllers\InventoryController::class)->show(
            \Illuminate\Http\Request::create('/'), Inventory::first()
        );
        $this->assertSame(0, $view->getData()['freeQty']);
    }

    public function test_inventory_report_reconstructs_historical_balances_without_double_counting(): void
    {
        $order = $this->order();
        DB::table('products')->insert(['id' => 1, 'name' => 'Pizza', 'category' => 'Pizza']);
        $inventory = Inventory::first();
        // Opening 100, period net +8, later +5 => current 113.
        $inventory->update(['quantity' => 113]);
        foreach ([
            ['2026-10-10', 'stock_restock', null, 20],
            ['2026-10-10', 'order_deduct', 'sale', -10],
            ['2026-10-10', 'order_deduct', 'free', -2],
            ['2026-10-10', 'order_deduct', null, -3],
            ['2026-10-10', 'order_restore', 'sale', 10],
            ['2026-10-10', 'order_deduct', 'sale', -10],
            ['2026-10-10', 'order_adjust', 'free', 1],
            ['2026-10-10', 'order_adjust', 'free', -1],
            ['2026-10-10', 'manual_adjust', null, 3],
            ['2026-10-11', 'stock_restock', null, 5],
        ] as [$date, $type, $kind, $change]) {
            InventoryMovement::create([
                'inventory_id' => $inventory->id, 'product_id' => 1, 'order_id' => $order->id,
                'type' => $type, 'item_type' => $kind, 'quantity_change' => $change,
                'quantity_before' => 0, 'quantity_after' => 0,
            ])->forceFill(['created_at' => $date . ' 12:00:00'])->save();
        }
        $service = app(\App\Services\InventoryReportService::class);
        $row = $service->rows('2026-10-10', '2026-10-10')->first();
        $this->assertSame(100, $row->opening);
        $this->assertSame(108, $row->closing);
        $this->assertSame(20, $row->sold);
        $this->assertSame(3, $row->free);
        $this->assertSame(3, $row->unclassified);
        $this->assertSame(10, $row->returned);
        $this->assertSame(4, $row->adjustment);
        $this->assertSame($row->closing, $row->opening + $row->received - $row->sold - $row->free - $row->unclassified + $row->returned + $row->adjustment);
        $this->assertCount(0, $service->rows(null, null, null, 'Other'));
        $quietDay = $service->rows('2026-10-09', '2026-10-09')->first();
        $this->assertSame(100, $quietDay->closing);
        $this->assertSame($quietDay->opening, $quietDay->closing);
    }

    public function test_inventory_report_controller_returns_filtered_data_and_excel(): void
    {
        $this->order();
        DB::table('products')->insert(['id' => 1, 'name' => 'Pizza', 'category' => 'Pizza']);
        $controller = app(\App\Http\Controllers\ReportController::class);
        $view = $controller->inventory(\Illuminate\Http\Request::create('/', 'GET', ['period' => 'all', 'product_id' => 1]));
        $this->assertCount(1, $view->getData()['rows']);
        $this->assertSame(100, $view->getData()['rows']->first()->closing);
        $export = $controller->inventory(\Illuminate\Http\Request::create('/', 'GET', ['period' => 'all', 'export' => 'excel']));
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class, $export);
        ob_start();
        $export->sendContent();
        $bytes = ob_get_clean();
        $this->assertStringStartsWith('PK', $bytes);
    }
}
