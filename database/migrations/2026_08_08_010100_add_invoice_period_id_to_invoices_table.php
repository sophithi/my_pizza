<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('invoice_period_id')->nullable()->after('invoice_number')
                ->constrained('invoice_periods')->nullOnDelete();
        });

        // Attach every existing invoice (including soft-deleted ones) to the
        // initial period seeded by the previous migration.
        $initialPeriodId = DB::table('invoice_periods')->orderBy('id')->value('id');
        DB::table('invoices')->update(['invoice_period_id' => $initialPeriodId]);

        // invoice_number can now repeat across periods (numbering restarts
        // each time a period is closed), so uniqueness is scoped per period
        // instead of globally.
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique('invoices_invoice_number_unique');
            $table->unique(['invoice_period_id', 'invoice_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The FK must drop before the composite unique index — MySQL
        // refuses to drop an index the FK still relies on (error 1553).
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['invoice_period_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique(['invoice_period_id', 'invoice_number']);
            $table->unique('invoice_number');
            $table->dropColumn('invoice_period_id');
        });
    }
};
