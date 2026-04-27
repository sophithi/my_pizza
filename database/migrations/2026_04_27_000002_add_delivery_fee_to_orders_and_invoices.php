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

            if (!Schema::hasColumn('orders', 'delivery_fee_khr')) {
                $table->decimal('delivery_fee_khr', 12, 2)->default(0)->after('discount_amount');
            }

            if (!Schema::hasColumn('orders', 'delivery_fee_usd')) {
                $table->decimal('delivery_fee_usd', 12, 2)->default(0)->after('delivery_fee_khr');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'delivery_fee_khr')) {
                $table->decimal('delivery_fee_khr', 12, 2)->default(0)->after('discount_amount');
            }

            if (!Schema::hasColumn('invoices', 'delivery_fee_usd')) {
                $table->decimal('delivery_fee_usd', 12, 2)->default(0)->after('delivery_fee_khr');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'delivery_fee_usd')) {
                $table->dropColumn('delivery_fee_usd');
            }

            if (Schema::hasColumn('invoices', 'delivery_fee_khr')) {
                $table->dropColumn('delivery_fee_khr');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'delivery_id')) {
                $table->dropForeign(['delivery_id']);
                $table->dropColumn('delivery_id');
            }

            if (Schema::hasColumn('orders', 'delivery_fee_usd')) {
                $table->dropColumn('delivery_fee_usd');
            }

            if (Schema::hasColumn('orders', 'delivery_fee_khr')) {
                $table->dropColumn('delivery_fee_khr');
            }
        });
    }
};
