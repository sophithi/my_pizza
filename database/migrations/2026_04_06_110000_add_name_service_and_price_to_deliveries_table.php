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
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('name_service')->nullable()->after('delivery_type');
            // Name of the service provider (e.g., "Fast Logistic", "Taxi XYZ", etc.)
            $table->decimal('price_of_delivery', 8, 2)->default(0)->after('name_service');
            // Price/cost of the delivery service
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['name_service', 'price_of_delivery']);
        });
    }
};
