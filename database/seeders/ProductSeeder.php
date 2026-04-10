<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Pizza Dough Mix (5kg)',
                'description' => 'Premium pizza dough mix for professional use',
                'sku' => 'DOUGH-5KG',
                'price_usd' => 12.00,
                'price_khr' => 48600,
                'category' => 'Flour & Mixes',
                'unit' => 'kg',
                'supplier' => 'Italian Imports Co.',
            ],
            [
                'name' => 'Mozzarella Cheese 1kg',
                'description' => 'Fresh mozzarella cheese, ideal for pizza',
                'sku' => 'MOZ-1KG',
                'price_usd' => 5.50,
                'price_khr' => 22275,
                'category' => 'Cheese',
                'unit' => 'kg',
                'supplier' => 'Dairy Fresh Ltd.',
            ],
            [
                'name' => 'San Marzano Tomatoes (2.5kg)',
                'description' => 'Premium San Marzano tomatoes, crushed',
                'sku' => 'TOMATO-2.5KG',
                'price_usd' => 8.50,
                'price_khr' => 34425,
                'category' => 'Sauces & Bases',
                'unit' => 'kg',
                'supplier' => 'Italy Foods Inc.',
            ],
            [
                'name' => 'Extra Virgin Olive Oil (5L)',
                'description' => 'Premium extra virgin olive oil',
                'sku' => 'OIL-5L',
                'price_usd' => 18.00,
                'price_khr' => 72900,
                'category' => 'Oils',
                'unit' => 'ltr',
                'supplier' => 'Mediterranean Exports',
            ],
            [
                'name' => 'Fresh Basil (Bundle)',
                'description' => 'Fresh green basil leaves',
                'sku' => 'BASIL-FRESH',
                'price_usd' => 2.50,
                'price_khr' => 10125,
                'category' => 'Fresh Herbs',
                'unit' => 'pcs',
                'supplier' => 'Farm Fresh Suppliers',
            ],
            [
                'name' => 'Oregano Dried (500g)',
                'description' => 'Dried oregano for seasoning',
                'sku' => 'OREGANO-500G',
                'price_usd' => 3.50,
                'price_khr' => 14175,
                'category' => 'Spices & Herbs',
                'unit' => 'kg',
                'supplier' => 'Spice World Ltd.',
            ],
            [
                'name' => 'Pepperoni Slices (1kg)',
                'description' => 'Premium pepperoni slices',
                'sku' => 'PEPP-1KG',
                'price_usd' => 9.50,
                'price_khr' => 38475,
                'category' => 'Toppings',
                'unit' => 'kg',
                'supplier' => 'Meat Delicacy Co.',
            ],
            [
                'name' => 'Bell Peppers (10kg box)',
                'description' => 'Fresh mixed bell peppers',
                'sku' => 'PEPPERS-10KG',
                'price_usd' => 15.00,
                'price_khr' => 60750,
                'category' => 'Fresh Vegetables',
                'unit' => 'kg',
                'supplier' => 'Fresh Produce Hub',
            ],
            [
                'name' => 'Onions White (10kg)',
                'description' => 'Fresh white onions',
                'sku' => 'ONIONS-10KG',
                'price_usd' => 8.00,
                'price_khr' => 32400,
                'category' => 'Fresh Vegetables',
                'unit' => 'kg',
                'supplier' => 'Fresh Produce Hub',
            ],
            [
                'name' => 'Mushrooms (5kg)',
                'description' => 'Fresh button mushrooms',
                'sku' => 'MUSHROOMS-5KG',
                'price_usd' => 10.50,
                'price_khr' => 42525,
                'category' => 'Fresh Vegetables',
                'unit' => 'kg',
                'supplier' => 'Fresh Produce Hub',
            ],
            [
                'name' => 'Garlic (1kg)',
                'description' => 'Fresh garlic bulbs',
                'sku' => 'GARLIC-1KG',
                'price_usd' => 4.00,
                'price_khr' => 16200,
                'category' => 'Fresh Vegetables',
                'unit' => 'kg',
                'supplier' => 'Fresh Produce Hub',
            ],
            [
                'name' => 'Parmesan Cheese (500g)',
                'description' => 'Grated parmesan cheese',
                'sku' => 'PARMESAN-500G',
                'price_usd' => 7.50,
                'price_khr' => 30375,
                'category' => 'Cheese',
                'unit' => 'kg',
                'supplier' => 'Dairy Fresh Ltd.',
            ],
        ];

        foreach ($products as $product) {
            $createdProduct = Product::firstOrCreate(
                ['sku' => $product['sku']],
                $product
            );

            Inventory::firstOrCreate(
                ['product_id' => $createdProduct->id],
                [
                    'quantity' => rand(50, 200),
                    'reorder_level' => rand(10, 30),
                    'reorder_quantity' => rand(50, 150),
                    'cost_per_unit' => $createdProduct->price_usd * 0.6,
                    'warehouse_location' => 'Section ' . chr(65 + rand(0, 5)),
                ]
            );
        }
    }
}
