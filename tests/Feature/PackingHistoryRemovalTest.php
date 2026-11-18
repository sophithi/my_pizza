<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PackingHistoryRemovalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Storage::fake('local');
    }

    public function test_removal_archives_every_row_and_does_not_change_invoice_data(): void
    {
        (require database_path('migrations/2026_11_18_000001_create_packing_dispatches_table.php'))->up();
        Schema::create('invoices', function ($t) { $t->id(); $t->integer('total_amount_khr'); $t->dateTime('packing_sent_at'); });
        DB::table('invoices')->insert(['id' => 1, 'total_amount_khr' => 87500, 'packing_sent_at' => '2026-11-13 10:00:00']);
        foreach (range(1, 105) as $id) {
            DB::table('packing_dispatches')->insert([
                'id' => $id, 'invoice_id' => 1, 'sent_at' => '2026-11-13 10:00:00',
                'snapshot' => json_encode(['invoice_number' => 'INV-TEST', 'quantity' => $id]),
            ]);
        }
        (require database_path('migrations/2026_11_18_000003_remove_packing_dispatches_table.php'))->up();
        $this->assertFalse(Schema::hasTable('packing_dispatches'));
        $files = Storage::disk('local')->allFiles('packing-backups');
        $this->assertCount(1, $files);
        $lines = array_filter(explode("\n", Storage::disk('local')->get($files[0])));
        $this->assertCount(105, $lines);
        $first = json_decode($lines[0], true);
        $last = json_decode($lines[104], true);
        $this->assertSame(1, $first['id']);
        $this->assertSame(105, $last['id']);
        $this->assertSame('INV-TEST', json_decode($first['snapshot'], true)['invoice_number']);
        $this->assertDatabaseHas('invoices', ['id' => 1, 'total_amount_khr' => 87500, 'packing_sent_at' => '2026-11-13 10:00:00']);
    }

    public function test_removal_is_safe_when_hosting_never_created_the_table(): void
    {
        (require database_path('migrations/2026_11_18_000003_remove_packing_dispatches_table.php'))->up();
        $this->assertFalse(Schema::hasTable('packing_dispatches'));
        $this->assertCount(0, Storage::disk('local')->allFiles());
    }
}
