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
        $indexes = Schema::getIndexes('customers');
        $indexNames = array_column($indexes, 'name');

        // Drop the unique index FIRST (SQLite requires this)
        if (in_array('customers_facebook_id_unique', $indexNames)) {
            $table->dropUnique('customers_facebook_id_unique');
        }

        $unusedColumns = [
            'facebook_id', 'telegram_id', 'total_orders_count',
            'total_spent', 'last_order_date', 'preferred_contact_method',
            'notes', 'is_vip', 'rating', 'address', 'city', 'postal_code',
            'company_name', 'credit_limit'
        ];

        $toDrop = array_filter($unusedColumns, fn($col) => in_array($col, $columns));

        if (!empty($toDrop)) {
            $table->dropColumn(array_values($toDrop));
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
