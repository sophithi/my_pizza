<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $products = Product::all();

        $statuses = ['pending', 'processing', 'completed'];
        $paymentStatuses = ['unpaid', 'partial', 'paid'];

        if (Order::count() > 0) {
            return;
        }

        for ($i = 0; $i < 15; $i++) {
            $customer = $customers->random();
            $numItems = rand(2, 5);
            $selectedProducts = $products->random($numItems);

            $subtotal = 0;
            $items = [];

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 10);
              $unitPrice = $product->price_usd;
                $totalPrice = $unitPrice * $quantity;
                $subtotal += $totalPrice;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            $tax = $subtotal * 0.08;
            $discount = rand(0, 1) ? round($subtotal * 0.05, 2) : 0;
            $total = $subtotal + $tax - $discount;

            $order = Order::create([
                'customer_id' => $customer->id,
                'order_date' => now()->subDays(rand(0, 30)),
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'status' => $statuses[array_rand($statuses)],
                'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                'notes' => 'Sample order for testing',
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);
            }
        }
    }
}
