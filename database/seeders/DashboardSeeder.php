<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Invoice;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create products
        $products = [
            ['name' => 'Regular Pizza Dough', 'sku' => 'SKU-001', 'price_usd' => 5.00, 'description' => 'Fresh pizza dough', 'price_khr' => 20150],
            ['name' => 'Mozzarella Cheese 1kg', 'sku' => 'SKU-002', 'price_usd' => 5.00, 'description' => 'Premium mozzarella', 'price_khr' => 20150],
            ['name' => 'Tomato Sauce 5L', 'sku' => 'SKU-003', 'price_usd' => 5.00, 'description' => 'Italian tomato sauce', 'price_khr' => 20150],
            ['name' => 'Olive Oil Premium', 'sku' => 'SKU-004', 'price_usd' => 12.50, 'description' => 'Extra virgin olive oil', 'price_khr' => 50375],
            ['name' => 'Fresh Basil Bundle', 'sku' => 'SKU-005', 'price_usd' => 5.00, 'description' => 'Fresh basil herbs', 'price_khr' => 20150],
            ['name' => 'Buffalo Mozzarella', 'sku' => 'SKU-006', 'price_usd' => 8.00, 'description' => 'Buffalo mozzarella cheese', 'price_khr' => 32240],
            ['name' => 'San Marzano Tomatoes', 'sku' => 'SKU-007', 'price_usd' => 6.00, 'description' => 'San Marzano tomatoes', 'price_khr' => 24180],
            ['name' => 'Pepperoni Sliced', 'sku' => 'SKU-008', 'price_usd' => 7.50, 'description' => 'Italian pepperoni', 'price_khr' => 30225],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $createdProducts = Product::all();

        // Create customers
        $customers = [
            ['name' => 'John Restaurant', 'type' => 'facebook', 'phone' => '555-0101'],
            ['name' => 'Pizza Italia', 'type' => 'telegram', 'phone' => '555-0102'],
            ['name' => 'Quick Bite Cafe', 'type' => 'facebook', 'phone' => '555-0103'],
            ['name' => 'Family Bistro', 'type' => 'telegram', 'phone' => '555-0104'],
            ['name' => 'Gourmet Hub', 'type' => 'facebook', 'phone' => '555-0105'],
            ['name' => 'Modern Eatery', 'type' => 'telegram', 'phone' => '555-0106'],
            ['name' => 'Taste of Italy', 'type' => 'facebook', 'phone' => '555-0107'],
            ['name' => 'Premium Pizzeria', 'type' => 'telegram', 'phone' => '555-0108'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        $createdCustomers = Customer::all();

        // Create orders with items for the last 10 days
        for ($i = 9; $i >= 0; $i--) {
            for ($j = 0; $j < rand(2, 5); $j++) {
                $orderDate = Carbon::now()->subDays($i);
                
                $order = Order::create([
                    'customer_id' => $createdCustomers->random()->id,
                    'order_date' => $orderDate,
                    'status' => collect(['completed', 'pending', 'processing'])->random(),
                    'payment_status' => 'pending',
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => 0,
                ]);

                // Add 2-5 items per order
                $total = 0;
                for ($k = 0; $k < rand(2, 5); $k++) {
                    $product = $createdProducts->random();
                    $quantity = rand(5, 50);
                    $unitPrice = $product->price_usd;
                    $itemTotal = $quantity * $unitPrice;
                    $total += $itemTotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $itemTotal,
                    ]);
                }

                $tax = round($total * 0.1, 2);
                $order->update([
                    'subtotal' => $total,
                    'tax_amount' => $tax,
                    'total_amount' => $total + $tax,
                ]);

                // Create invoice
                if ($order->status === 'completed') {
                    Invoice::create([
                        'order_id' => $order->id,
                        'invoice_number' => 'INV-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                        'invoice_date' => $orderDate,
                        'due_date' => $orderDate->addDays(30),
                        'subtotal' => $order->subtotal,
                        'tax_amount' => $order->tax_amount,
                        'discount_amount' => 0,
                        'total_amount' => $order->total_amount,
                        'status' => 'sent',
                    ]);
                }
            }
        }

        // Create inventory for products
        foreach ($createdProducts as $product) {
            Inventory::create([
                'product_id' => $product->id,
                'quantity' => rand(100, 500),
                'reorder_level' => 50,
                'reorder_quantity' => 200,
                'cost_per_unit' => $product->price * 0.6,
            ]);
        }

        // Set some items to low stock for alerts
        Inventory::whereIn('product_id', [1, 3, 6])->update(['quantity' => rand(5, 15)]);
    }
}
