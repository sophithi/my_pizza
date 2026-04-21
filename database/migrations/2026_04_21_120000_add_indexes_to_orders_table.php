<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds indexes to columns frequently used in filters and sorting to
     * improve performance for large transaction volumes.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add indexes if they don't already exist
            $table->index('status', 'orders_status_index');
            $table->index('payment_status', 'orders_payment_status_index');
            $table->index('customer_id', 'orders_customer_id_index');
            $table->index('order_date', 'orders_order_date_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_index');
            $table->dropIndex('orders_payment_status_index');
            $table->dropIndex('orders_customer_id_index');
            $table->dropIndex('orders_order_date_index');
        });
    }
};
