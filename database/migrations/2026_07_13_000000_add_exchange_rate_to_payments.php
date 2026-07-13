<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'exchange_rate')) {
                $table->decimal('exchange_rate', 8, 2)->nullable()->default(4000)->after('paid_amount');
            }
            if (!Schema::hasColumn('payments', 'exchange_rate_notes')) {
                $table->text('exchange_rate_notes')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['exchange_rate', 'exchange_rate_notes']);
        });
    }
};
