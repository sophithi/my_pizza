<?php

namespace Tests\Feature;

use App\Http\Controllers\PaymentController;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PaymentKhrPersistenceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('orders', function ($t) {
            $t->id(); $t->integer('customer_id')->nullable(); $t->dateTime('order_date');
            $t->float('total_amount'); $t->float('delivery_fee_khr')->default(0);
            $t->string('payment_status'); $t->softDeletes(); $t->timestamps();
        });
        Schema::create('order_items', function ($t) {
            $t->id(); $t->integer('order_id'); $t->integer('product_id')->nullable();
            $t->integer('quantity'); $t->float('unit_price_khr'); $t->float('unit_price');
            $t->float('discount_percent')->default(0); $t->string('item_type')->default('sale');
        });
        Schema::create('invoices', function ($t) {
            $t->id(); $t->integer('order_id'); $t->float('delivery_fee_khr')->default(0);
            $t->string('status'); $t->softDeletes(); $t->timestamps();
        });
        Schema::create('payments', function ($t) {
            $t->id(); $t->integer('order_id'); $t->string('customer_name');
            $t->float('total_amount'); $t->float('total_amount_khr');
            $t->float('paid_amount'); $t->float('paid_amount_khr'); $t->float('exchange_rate');
            $t->string('method'); $t->string('status'); $t->string('notes')->nullable(); $t->timestamps();
        });
        Schema::create('payment_lines', function ($t) {
            $t->id(); $t->integer('payment_id'); $t->string('method'); $t->string('currency');
            $t->float('amount_original'); $t->float('amount_usd'); $t->float('exchange_rate'); $t->timestamps();
        });
        DB::table('orders')->insert(['id' => 1, 'order_date' => '2026-11-13', 'total_amount' => 40, 'payment_status' => 'unpaid']);
        DB::table('order_items')->insert(['order_id' => 1, 'quantity' => 1, 'unit_price_khr' => 160000, 'unit_price' => 40]);
        DB::table('invoices')->insert(['order_id' => 1, 'status' => 'draft']);
    }

    private function paymentRequest(int $amount, bool $additional = false): Request
    {
        return Request::create('/payments', 'POST', [
            'source_order_id' => 1, 'additional_payment' => $additional ? '1' : '0',
            'payment_date' => '2026-11-13',
            'payment_lines' => json_encode([['method' => 'ABA', 'currency' => 'KHR', 'amount' => $amount]]),
        ]);
    }

    public function test_installments_and_edits_keep_exact_khr_and_cumulative_status(): void
    {
        $controller = new PaymentController();
        $controller->store($this->paymentRequest(95000));
        $controller->store($this->paymentRequest(65000, true));
        $this->assertSame(160000.0, (float) Payment::sum('paid_amount_khr'));
        $this->assertDatabaseHas('orders', ['id' => 1, 'payment_status' => 'paid']);
        $this->assertDatabaseHas('invoices', ['order_id' => 1, 'status' => 'paid']);
        $first = Payment::orderBy('id')->first();
        $controller->update($this->paymentRequest(94500), $first);
        $this->assertSame(159500.0, (float) Payment::sum('paid_amount_khr'));
        $this->assertDatabaseHas('orders', ['id' => 1, 'payment_status' => 'partial']);
        $this->assertDatabaseHas('invoices', ['order_id' => 1, 'status' => 'draft']);
        $this->assertDatabaseHas('payment_lines', ['payment_id' => $first->id, 'currency' => 'KHR', 'amount_original' => 94500]);
    }

    public function test_one_riel_excess_is_rejected_without_changing_receipts(): void
    {
        $controller = new PaymentController();
        $controller->store($this->paymentRequest(95000));
        try {
            $controller->store($this->paymentRequest(65001, true));
            $this->fail('Overpayment should be rejected.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('payment_lines', $e->errors());
        }
        $this->assertSame(1, Payment::count());
        $this->assertSame(95000.0, (float) Payment::sum('paid_amount_khr'));
    }

    public function test_mapping_payment_rows_does_not_query_each_orders_khr_relations(): void
    {
        foreach ([2, 3] as $id) {
            DB::table('orders')->insert(['id' => $id, 'order_date' => '2026-11-13', 'total_amount' => 21.88, 'payment_status' => 'unpaid']);
            DB::table('order_items')->insert(['order_id' => $id, 'quantity' => 1, 'unit_price_khr' => 87500, 'unit_price' => 21.88]);
        }
        $controller = new PaymentController();
        $orders = (new \ReflectionMethod($controller, 'applyFilters'))->invoke(
            $controller, Request::create('/payments', 'GET', ['period' => 'all'])
        )->get();
        $this->assertCount(3, $orders);
        foreach ($orders as $order) {
            $this->assertTrue($order->relationLoaded('items'));
            $this->assertTrue($order->relationLoaded('invoice'));
        }
        DB::enableQueryLog();
        DB::flushQueryLog();
        try {
            $map = new \ReflectionMethod($controller, 'mapOrderToPaymentRow');
            $rows = $orders->map(fn($order) => $map->invoke($controller, $order));
            $this->assertCount(0, DB::getQueryLog(), 'Mapping rows must not trigger per-order database queries.');
            $this->assertSame(335000.0, (float) $rows->sum('total_amount_khr'));
        } finally {
            DB::disableQueryLog();
        }
    }

    public function test_legacy_database_can_open_payments_and_pdf_without_normalization(): void
    {
        Schema::create('deliveries', function ($t) { $t->id(); $t->string('delivery_name'); $t->softDeletes(); });
        $controller = new PaymentController();
        $controller->store($this->paymentRequest(87500));
        DB::table('payment_lines')->update(['currency' => 'USD', 'amount_original' => 21.88]);
        $request = Request::create('/payments', 'GET', [
            'period' => 'custom', 'date_from' => '2026-11-13', 'date_to' => '2026-11-13', 'method' => 'ABA',
        ]);
        $index = $controller->index($request)->getData();
        $pdf = $controller->exportPdf($request)->getData();
        $this->assertSame(87500.0, $index['stats']['collected_khr']);
        $this->assertSame(87500.0, $index['stats']['method_breakdown']['ABA']['khr']);
        $this->assertSame(87500.0, $pdf['stats']['method_breakdown']['ABA']['khr']);
        $this->assertDatabaseHas('payment_lines', ['currency' => 'USD', 'amount_original' => 21.88]);
        $this->assertSame(87500.0, (float) Payment::first()->paid_amount_khr);
    }
}
