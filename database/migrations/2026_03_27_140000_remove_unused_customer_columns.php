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
            // Get existing columns
            $columns = Schema::getColumnListing('customers');
            
            // Drop unused columns
            $unusedColumns = [
                'facebook_id', 'telegram_id', 'total_orders_count', 
                'total_spent', 'last_order_date', 'preferred_contact_method', 
                'notes', 'is_vip', 'rating', 'address', 'city', 'postal_code', 
                'company_name', 'credit_limit'
            ];
            
            $columnsToDropFlip = [];
            foreach ($unusedColumns as $col) {
                if (in_array($col, $columns)) {
                    $columnsToDropFlip[] = $col;
                }
            }
            
            if (!empty($columnsToDropFlip)) {
                $table->dropColumn($columnsToDropFlip);
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
