<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Customer;
use Carbon\Carbon;

class PaymentDateTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_date_is_saved_to_created_at()
    {
        // Create customer and order
        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'order_date' => now()->toDateString(),
            'total_amount' => 50,
        ]);

        $paymentDate = Carbon::parse('2026-08-03')->startOfDay()->toDateString();

        $response = $this->post(route('payments.store'), [
            'source_order_id' => $order->id,
            'payment_lines' => json_encode([[
                'method' => 'Cash',
                'currency' => 'USD',
                'amount' => 20,
            ]]),
            'payment_date' => $paymentDate,
        ]);

        $response->assertRedirect();

        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);

        $this->assertEquals(
            Carbon::parse($paymentDate)->toDateString(),
            $payment->created_at->toDateString()
        );
    }

    public function test_installment_payments_keep_separate_dates_and_cumulative_khr_total()
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'order_date' => '2026-09-16',
            'total_amount' => 40,
        ]);

        $this->post(route('payments.store'), [
            'source_order_id' => $order->id,
            'payment_date' => '2026-09-16',
            'payment_lines' => json_encode([[
                'method' => 'Cash',
                'currency' => 'KHR',
                'amount' => 95000,
            ]]),
        ])->assertRedirect();

        $this->post(route('payments.store'), [
            'source_order_id' => $order->id,
            'additional_payment' => '1',
            'payment_date' => '2026-09-17',
            'payment_lines' => json_encode([[
                'method' => 'Cash',
                'currency' => 'KHR',
                'amount' => 65000,
            ]]),
        ])->assertRedirect();

        $payments = Payment::where('order_id', $order->id)->orderBy('id')->get();

        $this->assertCount(2, $payments);
        $this->assertSame('2026-09-16', $payments[0]->created_at->toDateString());
        $this->assertSame('2026-09-17', $payments[1]->created_at->toDateString());
        $this->assertSame(160000.0, (float) $payments->sum('paid_amount_khr'));
        $this->assertSame('paid', $payments[1]->status);
    }
}
