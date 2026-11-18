<?php

namespace Tests\Feature;

use App\Http\Controllers\InvoiceController;
use App\Models\Invoice;
use App\Models\PackingDispatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PackingDispatchHistoryTest extends TestCase
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
        (require database_path('migrations/2026_11_18_000001_create_packing_dispatches_table.php'))->up();
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

    public function test_edit_keeps_invoice_in_packing_with_original_send_time_and_status(): void
    {
        $controller = new InvoiceController();
        Carbon::setTestNow('2026-11-13 19:30:00');
        $invoice = Invoice::findOrFail(1);
        $controller->sendToPacking($invoice);
        $invoice->refresh();
        $originalTime = $invoice->packing_sent_at->format('Y-m-d H:i:s');
        $originalRevision = $invoice->packingRevision();
        DB::table('order_items')->where('id', 1)->update(['quantity' => 5]);
        DB::table('customers')->where('id', 1)->update(['name' => 'Changed customer']);
        Carbon::setTestNow('2026-11-14 10:00:00');
        app(\App\Services\PackingDispatchService::class)->markEdited($invoice);
        $invoice->refresh();
        $day = $this->day('2026-11-13');
        $this->assertSame(1, $day['packingTotalCount']);
        $this->assertSame(1, $day['packingPendingCount']);
        $this->assertTrue($day['invoices']->first()->packing_edited);
        $this->assertSame($originalTime, $invoice->packing_sent_at->format('Y-m-d H:i:s'));
        $this->assertNotSame($originalRevision, $invoice->packingRevision());
        $this->assertNull($invoice->packing_completed_at);
        $this->assertSame(0, $this->day('2026-11-14')['packingTotalCount']);
        $this->assertSame('Changed customer', $day['invoices']->first()->order->customer->name);
        $this->assertSame(1, PackingDispatch::count());
        $this->assertSame(2, PackingDispatch::first()->snapshot['order']['items'][0]['quantity']);
        $controller->sendToPacking($invoice); // An edited invoice needs no resend.
        $this->assertSame(1, PackingDispatch::count());
    }

    public function test_completed_invoice_remains_completed_when_marked_edited(): void
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
        app(\App\Services\PackingDispatchService::class)->markEdited($invoice);
        $invoice->refresh();
        $this->assertSame($completed, $invoice->packing_completed_at->format('Y-m-d H:i:s'));
        $this->assertSame($checklist, $invoice->packing_checklist);
        $this->assertSame(1, $this->day('2026-11-13')['packingCompletedCount']);
        $this->assertTrue($invoice->packing_edited);
        $this->assertSame('completed', PackingDispatch::first()->state);
    }

    public function test_historical_resends_do_not_duplicate_invoice_rows(): void
    {
        Carbon::setTestNow('2026-11-13 19:15:00');
        $controller = new InvoiceController();
        $invoice = Invoice::findOrFail(1);
        $controller->sendToPacking($invoice);
        // Historical resends are still retained internally, not separate UI rows.
        Carbon::setTestNow('2026-11-14 19:30:00');
        $invoice->refresh()->update(['packing_sent_at' => now()]);
        $this->assertSame(2, PackingDispatch::count());
        $this->assertSame(1, $this->day('2026-11-13')['packingTotalCount']);
        $this->assertSame(1, $this->day('2026-11-14')['packingTotalCount']);
        $month = $controller->printIndex(Request::create('/packing/index', 'GET', ['period' => 'month']))->getData();
        $this->assertSame(1, $month['packingTotalCount']);
        $this->assertSame(1, $month['invoices']->total());
    }

    public function test_older_checklist_page_cannot_modify_an_edited_invoice(): void
    {
        Carbon::setTestNow('2026-11-13 10:00:00');
        $controller = new InvoiceController();
        $invoice = Invoice::findOrFail(1);
        $controller->sendToPacking($invoice);
        $invoice->refresh();
        $oldRevision = $invoice->packingRevision();
        app(\App\Services\PackingDispatchService::class)->markEdited($invoice);
        try {
            $controller->updatePackingChecklist(Request::create('/packing/checklist', 'POST', [
                'kind' => 'step', 'key' => 'prepared', 'checked' => true, 'revision' => $oldRevision,
            ]), $invoice);
            $this->fail('A stale checklist must be rejected.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(409, $e->getStatusCode());
        }
        $this->assertNull($invoice->fresh()->packing_checklist);
    }

    public function test_restore_recovers_previously_cleared_send_without_new_dispatch(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');
        DB::table('invoices')->where('id', 1)->update(['packing_first_sent_at' => '2026-11-13 09:00:00']);
        $this->artisan('packing:backfill-dispatches', ['--apply' => true])->assertSuccessful();
        $this->assertSame(0, $this->day('2026-11-13')['packingTotalCount']);
        $this->artisan('packing:restore-edited')->assertSuccessful();
        $this->assertNull(Invoice::find(1)->packing_sent_at);
        $this->artisan('packing:restore-edited', ['--apply' => true])->assertSuccessful();
        $invoice = Invoice::findOrFail(1);
        $this->assertTrue($invoice->packing_edited);
        $this->assertSame('2026-11-13 09:00:00', $invoice->packing_sent_at->format('Y-m-d H:i:s'));
        $this->assertSame(1, PackingDispatch::count());
        $this->assertSame('pending', PackingDispatch::first()->state);
        $this->assertSame(1, $this->day('2026-11-13')['packingTotalCount']);
        $this->assertCount(1, \Illuminate\Support\Facades\Storage::disk('local')->allFiles('packing-backups'));
        $this->artisan('packing:restore-edited', ['--apply' => true])->assertSuccessful();
        $this->assertSame(1, PackingDispatch::count());
        $this->assertSame(1, Invoice::find(1)->packing_revision);
    }

    public function test_history_is_retained_when_invoice_is_deleted(): void
    {
        Carbon::setTestNow('2026-11-13 10:00:00');
        $invoice = Invoice::findOrFail(1);
        (new InvoiceController())->sendToPacking($invoice);
        $invoice->forceDelete();
        $this->assertSame(1, PackingDispatch::count());
        $this->assertSame(0, $this->day('2026-11-13')['packingTotalCount']);
    }
}