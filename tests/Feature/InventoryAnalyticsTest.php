<?php

namespace Tests\Feature;

use App\Services\InventoryAnalyticsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InventoryAnalyticsTest extends TestCase
{
    public function test_daily_outflow_separates_sold_free_and_other_and_ignores_incoming_movements(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('products', function ($t) { $t->id(); $t->string('name'); });
        Schema::create('inventories', function ($t) {
            $t->id(); $t->integer('product_id'); $t->integer('quantity'); $t->integer('reorder_level');
            $t->decimal('cost_per_unit')->nullable(); $t->timestamps();
        });
        Schema::create('inventory_movements', function ($t) {
            $t->id(); $t->integer('inventory_id'); $t->integer('product_id');
            $t->string('type')->nullable(); $t->string('item_type')->nullable();
            $t->integer('quantity_change'); $t->timestamps();
        });
        DB::table('products')->insert([['id' => 1, 'name' => 'Pizza'], ['id' => 2, 'name' => 'Cheese']]);
        DB::table('inventories')->insert([
            ['id'=>1,'product_id'=>1,'quantity'=>15,'reorder_level'=>2,'cost_per_unit'=>2,'created_at'=>'2026-10-01'],
            ['id'=>2,'product_id'=>1,'quantity'=>5,'reorder_level'=>10,'cost_per_unit'=>3,'created_at'=>'2026-10-11'],
            ['id'=>3,'product_id'=>1,'quantity'=>0,'reorder_level'=>0,'cost_per_unit'=>null,'created_at'=>'2026-10-01'],
            ['id'=>4,'product_id'=>2,'quantity'=>5,'reorder_level'=>0,'cost_per_unit'=>null,'created_at'=>'2026-10-01'],
        ]);
        DB::table('inventory_movements')->insert([
            ['inventory_id'=>1,'product_id'=>1,'type'=>'order_deduct','item_type'=>'sale','quantity_change'=>-2,'created_at'=>'2026-10-10 12:00:00'],
            ['inventory_id'=>1,'product_id'=>1,'type'=>'order_adjust','item_type'=>'free','quantity_change'=>-3,'created_at'=>'2026-10-11 12:00:00'],
            ['inventory_id'=>1,'product_id'=>1,'type'=>'order_deduct','item_type'=>null,'quantity_change'=>-6,'created_at'=>'2026-10-10 13:00:00'],
            ['inventory_id'=>2,'product_id'=>1,'type'=>'stock_reduce','item_type'=>null,'quantity_change'=>-4,'created_at'=>'2026-10-09 12:00:00'],
            ['inventory_id'=>1,'product_id'=>1,'type'=>'stock_restock','item_type'=>null,'quantity_change'=>7,'created_at'=>'2026-10-10 14:00:00'],
            ['inventory_id'=>1,'product_id'=>1,'type'=>'order_deduct','item_type'=>'sale','quantity_change'=>-9,'created_at'=>'2026-10-12 12:00:00'],
            ['inventory_id'=>999,'product_id'=>1,'type'=>'order_deduct','item_type'=>'sale','quantity_change'=>-1000,'created_at'=>'2026-10-10 12:00:00'],
            ['inventory_id'=>4,'product_id'=>2,'type'=>'order_deduct','item_type'=>'sale','quantity_change'=>-8,'created_at'=>'2026-10-10 12:00:00'],
        ]);
        $result = app(InventoryAnalyticsService::class)->dashboard('2026-10-09', '2026-10-11');
        $this->assertSame([0, 10, 0], $result['sold']);
        $this->assertSame([0, 0, 3], $result['free']);
        $this->assertSame([4, 6, 0], $result['other']);
        $this->assertSame('day', $result['granularity']);
        $this->assertSame([0, 2, 0], app(InventoryAnalyticsService::class)->dashboard('2026-10-09', '2026-10-11', 1)['sold']);
        $weekly = app(InventoryAnalyticsService::class)->dashboard('2026-09-01', '2026-10-23', 1);
        $this->assertSame('week', $weekly['granularity']);
        $this->assertCount(8, $weekly['labels']);
        $this->assertSame(11, array_sum($weekly['sold']));
        $this->assertSame(3, array_sum($weekly['free']));
        $monthly = app(InventoryAnalyticsService::class)->dashboard('2026-01-01', '2026-10-23', 1);
        $this->assertSame('month', $monthly['granularity']);
        $this->assertCount(10, $monthly['labels']);
        $this->assertCount(2, $result['lowStockAlerts']);
        $this->assertCount(6, $result['recentActivities']);
    }
}
