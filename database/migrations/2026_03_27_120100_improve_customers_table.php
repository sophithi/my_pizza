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
            // Platform-specific IDs for integration
            if (!Schema::hasColumn('customers', 'facebook_id')) {
                $table->string('facebook_id')->nullable()->unique();
            }
            if (!Schema::hasColumn('customers', 'telegram_id')) {
                $table->string('telegram_id')->nullable()->unique();
            }
            
            // Customer engagement tracking
            if (!Schema::hasColumn('customers', 'total_orders_count')) {
                $table->unsignedInteger('total_orders_count')->default(0);
            }
            if (!Schema::hasColumn('customers', 'total_spent')) {
                $table->decimal('total_spent', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('customers', 'last_order_date')) {
                $table->dateTime('last_order_date')->nullable();
            }
            
            // Customer preferences
            if (!Schema::hasColumn('customers', 'preferred_contact_method')) {
                $table->string('preferred_contact_method')->default('phone');
            }
            if (!Schema::hasColumn('customers', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('customers', 'is_vip')) {
                $table->boolean('is_vip')->default(false);
            }
            if (!Schema::hasColumn('customers', 'rating')) {
                $table->decimal('rating', 3, 2)->default(0)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $columns = [
                'facebook_id', 'telegram_id', 'total_orders_count', 
                'total_spent', 'last_order_date', 'preferred_contact_method',
                'notes', 'is_vip', 'rating'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
