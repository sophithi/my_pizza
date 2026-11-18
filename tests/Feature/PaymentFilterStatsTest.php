<?php

namespace Tests\Feature;

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PaymentFilterStatsTest extends TestCase
{
    public function test_empty_custom_dates_default_to_today_without_becoming_all_time(): void
    {
        \Carbon\Carbon::setTestNow('2026-11-18 12:00:00');
        try {
            $controller = new PaymentController();
            $resolve = new \ReflectionMethod($controller, 'resolveDateRange');
            $request = \Illuminate\Http\Request::create('/payments', 'GET', ['period' => 'custom']);
            $this->assertSame(['2026-11-18', '2026-11-18'], $resolve->invoke($controller, $request));
            $this->assertSame('2026-11-18', $request->date_from);
            $this->assertSame('2026-11-18', $request->date_to);
            foreach (['date_from', 'date_to'] as $field) {
                $singleDate = \Illuminate\Http\Request::create('/payments', 'GET', ['period' => 'custom', $field => '2026-11-13']);
                $this->assertSame(['2026-11-13', '2026-11-13'], $resolve->invoke($controller, $singleDate));
            }
            $all = \Illuminate\Http\Request::create('/payments', 'GET', ['period' => 'all']);
            $this->assertSame([null, null], $resolve->invoke($controller, $all));
        } finally {
            \Carbon\Carbon::setTestNow();
        }
    }

    public function test_reversed_custom_date_range_is_rejected(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        (new \ReflectionMethod(PaymentController::class, 'resolveDateRange'))->invoke(
            new PaymentController(), \Illuminate\Http\Request::create('/payments', 'GET', [
                'period' => 'custom', 'date_from' => '2026-11-14', 'date_to' => '2026-11-13',
            ])
        );
    }

    public function test_invalid_custom_date_is_rejected(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        (new \ReflectionMethod(PaymentController::class, 'resolveDateRange'))->invoke(
            new PaymentController(), \Illuminate\Http\Request::create('/payments', 'GET', [
                'period' => 'custom', 'date_from' => '2026-02-30',
            ])
        );
    }

    public function test_reporting_uses_exact_khr_even_when_display_usd_is_rounded(): void
    {
        $build = new \ReflectionMethod(PaymentController::class, 'buildMethodBreakdownFromPayments');
        $payment = new \App\Models\Payment(['paid_amount_khr' => 490000, 'paid_amount' => 122.51]);
        $payment->setRelation('lines', collect([
            (object) ['method' => 'ABA', 'currency' => 'KHR', 'amount_original' => 87500, 'amount_usd' => 21.88, 'exchange_rate' => 4000],
            (object) ['method' => 'ABA', 'currency' => 'KHR', 'amount_original' => 402500, 'amount_usd' => 100.63, 'exchange_rate' => 4000],
        ]));
        $result = $build->invoke(new PaymentController(), collect([$payment]), 'ABA');
        $this->assertSame(490000.0, $result['ABA']['khr']);
        $this->assertSame(122.5, $result['ABA']['usd']);
    }

    public function test_legacy_usd_line_uses_saved_khr_without_changing_the_receipt(): void
    {
        $payment = new \App\Models\Payment(['paid_amount_khr' => 87500, 'paid_amount' => 21.88]);
        $payment->setRelation('lines', collect([(object) [
            'method' => 'ABA', 'currency' => 'USD', 'amount_original' => 21.88,
            'amount_usd' => 21.88, 'exchange_rate' => 4000,
        ]]));
        $before = serialize($payment->toArray());
        $result = (new \ReflectionMethod(PaymentController::class, 'buildMethodBreakdownFromPayments'))
            ->invoke(new PaymentController(), collect([$payment]), 'ABA');
        $this->assertSame(87500.0, $result['ABA']['khr']);
        $this->assertSame(21.875, $result['ABA']['usd']);
        $this->assertSame($before, serialize($payment->toArray()));
    }

    public function test_legacy_mixed_currencies_keep_original_khr_and_allocate_only_the_remainder(): void
    {
        $payment = new \App\Models\Payment(['paid_amount_khr' => 190000]);
        $payment->setRelation('lines', collect([
            (object) ['method' => 'Cash', 'currency' => 'KHR', 'amount_original' => 150000, 'amount_usd' => 37.5, 'exchange_rate' => 4000],
            (object) ['method' => 'ABA', 'currency' => 'USD', 'amount_original' => 10, 'amount_usd' => 10, 'exchange_rate' => 4000],
        ]));
        $build = new \ReflectionMethod(PaymentController::class, 'buildMethodBreakdownFromPayments');
        $all = $build->invoke(new PaymentController(), collect([$payment]));
        $this->assertSame(150000.0, $all['Cash']['khr']);
        $this->assertSame(40000.0, $all['ABA']['khr']);
        $this->assertSame(190000.0, array_sum(array_column($all, 'khr')));
        $aba = $build->invoke(new PaymentController(), collect([$payment]), 'ABA');
        $this->assertSame(0.0, $aba['Cash']['khr']);
        $this->assertSame(40000.0, $aba['ABA']['khr']);
    }

    public function test_multiple_legacy_usd_methods_preserve_exact_total_and_recorded_rate_weights(): void
    {
        $payment = new \App\Models\Payment(['paid_amount_khr' => 64999]);
        $payment->setRelation('lines', collect([
            (object) ['method' => 'Cash', 'currency' => 'KHR', 'amount_original' => 5000, 'exchange_rate' => 4000],
            (object) ['method' => 'ABA', 'currency' => 'USD', 'amount_original' => 10, 'exchange_rate' => 2000],
            (object) ['method' => 'Wing', 'currency' => 'USD', 'amount_original' => 10, 'exchange_rate' => 4000],
        ]));
        $result = (new \ReflectionMethod(PaymentController::class, 'buildMethodBreakdownFromPayments'))
            ->invoke(new PaymentController(), collect([$payment]));
        $this->assertSame(5000.0, $result['Cash']['khr']);
        $this->assertSame(19999.0, $result['ABA']['khr']);
        $this->assertSame(40000.0, $result['Wing']['khr']);
        $this->assertSame(64999.0, array_sum(array_column($result, 'khr')));
    }

    public function test_payment_input_keeps_integer_khr_and_derives_usd_after_summing(): void
    {
        $controller = new PaymentController();
        $parse = new \ReflectionMethod($controller, 'parsePaymentLines');
        $sum = new \ReflectionMethod($controller, 'sumLinesKhr');
        $request = \Illuminate\Http\Request::create('/', 'POST', [
            'exchange_rate' => 9999,
            'payment_lines' => json_encode([
                ['method' => 'Cash', 'currency' => 'KHR', 'amount' => 87500],
                ['method' => 'ABA', 'currency' => 'KHR', 'amount' => 402500],
            ]),
        ]);
        $lines = $parse->invoke($controller, $request);
        $this->assertSame(490000.0, $sum->invoke($controller, $lines));
        $this->assertSame(87500, $lines[0]['amount_original']);
        $this->assertSame(4000, $lines[0]['exchange_rate']);
        $this->assertSame(122.5, $sum->invoke($controller, $lines) / 4000);
    }

    public function test_usd_input_is_rejected_instead_of_converted_back_to_khr(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        (new \ReflectionMethod(PaymentController::class, 'parsePaymentLines'))->invoke(
            new PaymentController(), \Illuminate\Http\Request::create('/', 'POST', [
                'payment_lines' => json_encode([['method' => 'ABA', 'currency' => 'USD', 'amount' => 21.88]]),
            ])
        );
    }

    public function test_fractional_riel_is_rejected(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        (new \ReflectionMethod(PaymentController::class, 'parsePaymentLines'))->invoke(
            new PaymentController(), \Illuminate\Http\Request::create('/', 'POST', [
                'payment_lines' => json_encode([['method' => 'ABA', 'amount' => 109500.5]]),
            ])
        );
    }

    public function test_one_riel_overpayment_is_rejected(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        (new \ReflectionMethod(PaymentController::class, 'validatePaidKhr'))
            ->invoke(new PaymentController(), 109501, 109500);
    }

    public function test_method_summary_uses_filtered_orders_and_all_matching_installments(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('payments', function ($table) {
            $table->id();
            $table->integer('order_id');
            $table->string('method');
            $table->float('paid_amount');
            $table->float('paid_amount_khr');
            $table->timestamps();
        });
        Schema::create('payment_lines', function ($table) {
            $table->id();
            $table->integer('payment_id');
            $table->string('method');
            $table->string('currency');
            $table->float('amount_original');
            $table->float('amount_usd');
            $table->float('exchange_rate');
        });
        DB::table('payments')->insert([
            ['order_id' => 1, 'method' => 'Cash', 'paid_amount' => 10, 'paid_amount_khr' => 40000, 'created_at' => '2026-11-13 09:00:00'],
            ['order_id' => 1, 'method' => 'ABA', 'paid_amount' => 5, 'paid_amount_khr' => 20000, 'created_at' => '2026-11-13 12:00:00'],
            ['order_id' => 1, 'method' => 'Cash', 'paid_amount' => 2, 'paid_amount_khr' => 8000, 'created_at' => '2026-11-14 09:00:00'],
            ['order_id' => 2, 'method' => 'ACLEDA', 'paid_amount' => 100, 'paid_amount_khr' => 400000, 'created_at' => '2026-11-13 09:00:00'],
        ]);
        DB::table('payment_lines')->insert([
            ['payment_id' => 1, 'method' => 'Cash', 'currency' => 'KHR', 'amount_original' => 32000, 'amount_usd' => 8, 'exchange_rate' => 4000],
            ['payment_id' => 1, 'method' => 'ABA', 'currency' => 'KHR', 'amount_original' => 8000, 'amount_usd' => 2, 'exchange_rate' => 4000],
        ]);
        $rows = collect([(object) [
            'source_order_id' => 1, 'is_old_debt' => false, 'order_status' => 'completed',
            'total_amount' => 17, 'total_amount_khr' => 68000,
            'balance' => 0, 'balance_khr' => 0, 'status' => 'paid',
        ]]);
        $buildStats = new \ReflectionMethod(PaymentController::class, 'buildStatsFromRows');
        $controller = new PaymentController();
        $bounded = $buildStats->invoke($controller, $rows, '2026-11-13', '2026-11-13')['method_breakdown'];
        $this->assertSame(8.0, $bounded['Cash']['usd']);
        $this->assertSame(7.0, $bounded['ABA']['usd']);
        $this->assertSame(0.0, $bounded['ACLEDA']['usd']);
        $all = $buildStats->invoke($controller, $rows)['method_breakdown'];
        $this->assertSame(10.0, $all['Cash']['usd']);
        $this->assertSame(7.0, $all['ABA']['usd']);
        $filtered = $buildStats->invoke($controller, $rows, '2026-11-13', '2026-11-13', 'ABA');
        $this->assertSame(7.0, $filtered['collected']);
        $this->assertSame(28000.0, $filtered['collected_khr']);
        $this->assertSame(0.0, $filtered['method_breakdown']['Cash']['usd']);
        $this->assertSame(7.0, $filtered['method_breakdown']['ABA']['usd']);
        $period = $buildStats->invoke($controller, $rows, '2026-11-13', '2026-11-13');
        $this->assertSame(15.0, $period['collected']);
        $rows[0]->is_old_debt = true;
        $debt = $buildStats->invoke($controller, $rows, '2026-11-13', '2026-11-13', 'Cash');
        $this->assertSame(0.0, $debt['collected']);
        $this->assertSame(8.0, $debt['old_debt_collected']);
        $this->assertSame(32000.0, $debt['old_debt_collected_khr']);
        $empty = $buildStats->invoke($controller, collect(), '2026-11-13', '2026-11-13')['method_breakdown'];
        $this->assertSame(0.0, array_sum(array_column($empty, 'usd')));
    }
}
