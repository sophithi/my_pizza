<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentKhrNormalizationTest extends TestCase
{
    public function test_repair_is_previewed_backed_up_exact_and_repeatable(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Storage::fake('local');
        Schema::create('payments', function ($t) {
            $t->id(); $t->integer('order_id'); $t->float('total_amount_khr');
            $t->float('paid_amount_khr'); $t->float('paid_amount');
            $t->float('exchange_rate'); $t->string('notes')->nullable(); $t->timestamps();
        });
        Schema::create('payment_lines', function ($t) {
            $t->id(); $t->integer('payment_id'); $t->string('method'); $t->string('currency');
            $t->float('amount_original'); $t->float('amount_usd'); $t->float('exchange_rate'); $t->timestamps();
        });
        foreach ([[1, 87500, 87520, 21.88], [2, 109500, 109501, 27.38], [3, 20000, 14000, 3.5]] as [$id, $total, $paid, $usd]) {
            DB::table('payments')->insert(['id' => $id, 'order_id' => $id, 'total_amount_khr' => $total,
                'paid_amount_khr' => $paid, 'paid_amount' => $usd, 'exchange_rate' => 4000,
                'created_at' => '2026-11-13 12:00:00']);
        }
        foreach ([[1, 'USD', 21.88, 21.88], [2, 'KHR', 109501, 27.38], [3, 'USD', 1, 1], [3, 'KHR', 10000, 2.5]] as [$id, $currency, $original, $usd]) {
            DB::table('payment_lines')->insert(['payment_id' => $id, 'method' => 'ABA', 'currency' => $currency,
                'amount_original' => $original, 'amount_usd' => $usd, 'exchange_rate' => 4000,
                'created_at' => '2026-11-13 12:00:00']);
        }
        $this->artisan('payments:normalize-khr', ['--correction' => ['2:109500']])->assertSuccessful();
        $this->assertDatabaseHas('payments', ['id' => 1, 'paid_amount_khr' => 87520]);
        $this->assertCount(0, Storage::disk('local')->allFiles());
        $this->artisan('payments:normalize-khr', ['--apply' => true, '--correction' => ['2:109500']])->assertSuccessful();
        $this->assertDatabaseHas('payments', ['id' => 1, 'paid_amount_khr' => 87500, 'paid_amount' => 21.88]);
        $this->assertDatabaseHas('payments', ['id' => 2, 'paid_amount_khr' => 109500]);
        $this->assertDatabaseHas('payments', ['id' => 3, 'paid_amount_khr' => 14000]);
        $this->assertSame(0, DB::table('payment_lines')->where('currency', 'USD')->count());
        $this->assertDatabaseHas('payment_lines', ['payment_id' => 1, 'amount_original' => 87500, 'created_at' => '2026-11-13 12:00:00']);
        $files = Storage::disk('local')->allFiles('payment-khr-backups');
        $this->assertCount(1, $files);
        $backup = json_decode(Storage::disk('local')->get($files[0]), true);
        $this->assertSame('USD', $backup[0]['before']['lines'][0]['currency']);
        $this->artisan('payments:normalize-khr', ['--apply' => true, '--correction' => ['2:109500']])->assertSuccessful();
        $this->assertCount(1, Storage::disk('local')->allFiles('payment-khr-backups'));
    }
}
