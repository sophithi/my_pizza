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
    // Drop ALL problematic unique indexes FIRST (SQLite requires this)
    $indexesToDrop = [
        'customers_facebook_id_unique',
        'customers_telegram_id_unique',
    ];

    foreach ($indexesToDrop as $index) {
        try {
            Schema::table('customers', function (Blueprint $table) use ($index) {
                $table->dropUnique($index);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, skip
        }
    }

    // Now safely drop columns
    Schema::table('customers', function (Blueprint $table) {
        $columns = Schema::getColumnListing('customers');

        $unusedColumns = [
            'facebook_id', 'telegram_id', 'total_orders_count',
            'total_spent', 'last_order_date', 'preferred_contact_method',
            'notes', 'is_vip', 'rating', 'address', 'city', 'postal_code',
            'company_name', 'credit_limit'
        ];

        $toDrop = array_values(array_filter(
            $unusedColumns, fn($col) => in_array($col, $columns)
        ));

        if (!empty($toDrop)) {
            $table->dropColumn($toDrop);
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
