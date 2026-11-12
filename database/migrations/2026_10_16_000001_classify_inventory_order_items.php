<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('item_type', 10)->default('sale');
        });
        // Legacy orders identified free items by their zero USD unit price.
        DB::table('order_items')->where('unit_price', 0)->update(['item_type' => 'free']);
        Schema::table('inventory_movements', function (Blueprint $table) {
            // Historical combined movements cannot be reliably split after order edits.
            $table->string('item_type', 10)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', fn (Blueprint $table) => $table->dropColumn('item_type'));
        Schema::table('order_items', fn (Blueprint $table) => $table->dropColumn('item_type'));
    }
};
