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
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('reference_number')->unique()->nullable()->after('id');
            $table->string('supplier_name')->after('reference_number');
            $table->date('purchase_date')->after('supplier_name');
            $table->decimal('total_amount', 10, 2)->default(0)->after('purchase_date');
            $table->string('status')->default('pending')->after('total_amount'); // pending, received, cancelled
            $table->text('notes')->nullable()->after('status');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['reference_number', 'supplier_name', 'purchase_date', 'total_amount', 'status', 'notes']);
            $table->dropSoftDeletes();
        });
    }
};
