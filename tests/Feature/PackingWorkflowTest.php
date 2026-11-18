<?php

namespace Tests\Feature;

use App\Http\Controllers\InvoiceController;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PackingWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('customers', function ($t) { $t->id(); $t->string('name'); });
        Schema::create('products', function ($t) { $t->id(); $t->string('name'); });
        Schema::create('orders', function ($t) {
            $t->id(); $t->integer('customer_id'); $t->integer('delivery_id')->nullable();
            $t->string('status'); $t->softDeletes(); $t->timestamps();
        });
        Schema::create('order_items', function ($t) {
            $t->id(); $t->integer('order_id'); $t->integer('product_id'); $t->integer('quantity');
        });
        Schema::create('invoices', function ($t) {
            $t->id(); $t->integer('order_id'); $t->string('invoice_number'); $t->date('invoice_date');
            $t->string('status'); $t->dateTime('packing_sent_at')->nullable();
            $t->dateTime('packing_first_sent_at')->nullable(); $t->dateTime('packing_completed_at')->nullable();
            $t->integer('packing_completed_by')->nullable(); $t->json('packing_checklist')->nullable();
            $t->boolean('packing_edited')->default(false); $t->unsignedInteger('packing_revision')->default(0);
            $t->softDeletes(); $t->timestamps();
        });
        DB::table('customers')->insert(['id' => 1, 'name' => 'Original customer']);
        DB::table('products')->insert(['id' => 1, 'name' => 'Pizza']);
        DB::table('orders')->insert(['id' => 1, 'customer_id' => 1, 'status' => 'pending']);
        DB::table('order_items')->insert(['id' => 1, 'order_id' => 1, 'product_id' => 1, 'quantity' => 2]);
        DB::table('invoices')->insert(['id' => 1, 'order_id' => 1, 'invoice_number' => 'INV-TEST', 'invoice_date' => '2026-11-13', 'status' => 'draft']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function day(string $date): array
    {
        return (new InvoiceController())->printIndex(Request::create('/packing/index', 'GET', [
            'period' => 'custom', 'from_date' => $date, 'to_date' => $date,
        ]))->getData();
    }

    public function test_edit_keeps_original_day_and_status_and_shows_current_details(): void
    {
        Carbon::setTestNow('2026-11-13 19:30:00');
        $controller = new InvoiceController();
        $invoice = Invoice::findOrFail(1);
        $controller->sendToPacking($invoice);
        $invoice->refresh();
        $originalTime = $invoice->packing_sent_at->format('Y-m-d H:i:s');
        $originalRevision = $invoice->packingRevision();
        DB::table('order_items')->where('id', 1)->update(['quantity' => 5]);
        DB::table('customers')->where('id', 1)->update(['name' => 'Changed customer']);
        Carbon::setTestNow('2026-11-14 10:00:00');
        $invoice->markPackingEdited();
        $invoice->refresh();
        $day = $this->day('2026-11-13');
        $this->assertSame(1, $day['packingTotalCount']);
        $this->assertSame(1, $day['packingPendingCount']);
        $this->assertTrue($day['invoices']->first()->packing_edited);
        $this->assertSame($originalTime, $invoice->packing_sent_at->format('Y-m-d H:i:s'));
        $this->assertNotSame($originalRevision, $invoice->packingRevision());
        $this->assertSame(0, $this->day('2026-11-14')['packingTotalCount']);
        $this->assertSame('Changed customer', $day['invoices']->first()->order->customer->name);
        $this->assertSame(5, $day['invoices']->first()->order->items->first()->quantity);
        $this->assertFalse(Schema::hasTable('packing_dispatches'));
    }

    public function test_editing_completed_invoice_preserves_completion_and_checklist(): void
    {
        Carbon::setTestNow('2026-11-13 10:00:00');
        $controller = new InvoiceController();
        $invoice = Invoice::findOrFail(1);
        $controller->sendToPacking($invoice);
        $invoice->refresh()->update(['packing_checklist' => ['items' => ['1' => true]]]);
        $controller->markPackingCompleted(Request::create('/packing/complete', 'POST'), $invoice);
        $invoice->refresh();
        $completed = $invoice->packing_completed_at->format('Y-m-d H:i:s');
        $checklist = $invoice->packing_checklist;
        $invoice->markPackingEdited();
        $invoice->refresh();
        $this->assertSame($completed, $invoice->packing_completed_at->format('Y-m-d H:i:s'));
        $this->assertSame($checklist, $invoice->packing_checklist);
        $this->assertSame(1, $this->day('2026-11-13')['packingCompletedCount']);
        $this->assertTrue($invoice->packing_edited);
    }

    public function test_repeated_send_after_edit_does_not_duplicate_or_move_invoice(): void
    {
        Carbon::setTestNow('2026-11-13 10:00:00');
        $controller = new InvoiceController();
        $invoice = Invoice::findOrFail(1);
        $controller->sendToPacking($invoice);
        $invoice->refresh()->markPackingEdited();
        Carbon::setTestNow('2026-11-14 10:00:00');
        $controller->sendToPacking($invoice);
        $this->assertSame(1, $this->day('2026-11-13')['packingTotalCount']);
        $this->assertSame(0, $this->day('2026-11-14')['packingTotalCount']);
        $this->assertSame('2026-11-13 10:00:00', $invoice->fresh()->packing_sent_at->format('Y-m-d H:i:s'));
    }

    public function test_stale_checklist_cannot_modify_edited_invoice(): void
    {
        Carbon::setTestNow('2026-11-13 10:00:00');
        $controller = new InvoiceController();
        $invoice = Invoice::findOrFail(1);
        $controller->sendToPacking($invoice);
        $invoice->refresh();
        $revision = $invoice->packingRevision();
        $invoice->markPackingEdited();
        try {
            $controller->updatePackingChecklist(Request::create('/packing/checklist', 'POST', [
                'kind' => 'step', 'key' => 'prepared', 'checked' => true, 'revision' => $revision,
            ]), $invoice);
            $this->fail('A stale checklist must be rejected.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(409, $e->getStatusCode());
        }
        $this->assertNull($invoice->fresh()->packing_checklist);
    }

    public function test_legacy_edit_columns_are_optional_and_checklist_is_preserved(): void
    {
        Schema::table('invoices', fn($t) => $t->dropColumn(['packing_edited', 'packing_revision']));
        Carbon::setTestNow('2026-11-13 10:00:00');
        $controller = new InvoiceController();
        $invoice = Invoice::findOrFail(1);
        $controller->sendToPacking($invoice);
        $invoice->refresh()->update(['packing_checklist' => ['items' => ['1' => true]]]);
        $invoice->markPackingEdited();
        $invoice->refresh();
        $this->assertTrue($invoice->packing_edited);
        $this->assertSame(1, $invoice->packing_revision);
        $this->assertTrue($invoice->packing_checklist['items'][1]);
        $controller->markPackingCompleted(Request::create('/packing/complete', 'POST'), $invoice);
        $this->assertSame(1, $this->day('2026-11-13')['packingCompletedCount']);
        $invoice->delete();
        $this->assertSame(0, $this->day('2026-11-13')['packingTotalCount']);
    }

    public function test_never_sent_invoice_is_not_marked_edited(): void
    {
        $invoice = Invoice::findOrFail(1);
        $invoice->markPackingEdited();
        $this->assertFalse($invoice->fresh()->packing_edited);
        $this->assertNull($invoice->fresh()->packing_sent_at);
        $this->assertSame(0, $this->day('2026-11-13')['packingTotalCount']);
    }
}