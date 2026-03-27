<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $columns = Schema::getColumnListing('customers');
            
            if (in_array('facebook_id', $columns)) {
                $table->dropColumn('facebook_id');
            }
            if (in_array('telegram_id', $columns)) {
                $table->dropColumn('telegram_id');
            }
            if (in_array('total_orders_count', $columns)) {
                $table->dropColumn('total_orders_count');
            }
            if (in_array('total_spent', $columns)) {
                $table->dropColumn('total_spent');
            }
            if (in_array('last_order_date', $columns)) {
                $table->dropColumn('last_order_date');
            }
            if (in_array('preferred_contact_method', $columns)) {
                $table->dropColumn('preferred_contact_method');
            }
            if (in_array('notes', $columns)) {
                $table->dropColumn('notes');
            }
            if (in_array('is_vip', $columns)) {
                $table->dropColumn('is_vip');
            }
            if (in_array('rating', $columns)) {
                $table->dropColumn('rating');
            }
            if (in_array('address', $columns)) {
                $table->dropColumn('address');
            }
            if (in_array('city', $columns)) {
                $table->dropColumn('city');
            }
            if (in_array('postal_code', $columns)) {
                $table->dropColumn('postal_code');
            }
            if (in_array('company_name', $columns)) {
                $table->dropColumn('company_name');
            }
            if (in_array('credit_limit', $columns)) {
                $table->dropColumn('credit_limit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
