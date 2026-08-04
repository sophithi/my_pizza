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
}
