<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();

        foreach ($orders as $index => $order) {
            Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => 'INV-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'invoice_date' => $order->order_date,
                'due_date' => $order->order_date->addDays(30),
                'subtotal' => $order->subtotal,
                'tax_amount' => $order->tax_amount,
                'discount_amount' => $order->discount_amount,
                'total_amount' => $order->total_amount,
                'status' => $order->status === 'completed' ? 'paid' : 'draft',
                'notes' => 'Invoice for order #' . $order->id,
            ]);
        }
    }
}
