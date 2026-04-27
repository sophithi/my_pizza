<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivery_id')) {
                $table->foreignId('delivery_id')->nullable()->after('customer_id')->constrained('deliveries')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'delivery_id')) {
                $table->dropForeign(['delivery_id']);
                $table->dropColumn('delivery_id');
            }
        });
    }
};
