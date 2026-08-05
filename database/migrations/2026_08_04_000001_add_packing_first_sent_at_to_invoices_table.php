<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'packing_first_sent_at')) {
                $table->timestamp('packing_first_sent_at')->nullable()->after('packing_sent_at');
            }
        });

        // Backfill: for invoices already sent to packing, treat their current
        // packing_sent_at as the original first-send time so the "edited"
        // badge only lights up for sends that happen from here on.
        DB::table('invoices')
            ->whereNotNull('packing_sent_at')
            ->whereNull('packing_first_sent_at')
            ->update(['packing_first_sent_at' => DB::raw('packing_sent_at')]);
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'packing_first_sent_at')) {
                $table->dropColumn('packing_first_sent_at');
            }
        });
    }
};
