<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'small_pack_qty')) {
                $table->unsignedInteger('small_pack_qty')->default(1)->after('box_qty');
            }
            if (!Schema::hasColumn('orders', 'big_pack_qty')) {
                $table->unsignedInteger('big_pack_qty')->default(0)->after('small_pack_qty');
            }
        });

        // Backfill existing orders: their box_qty was always "small pack" cases
        // under the old single-price model, so preserve that as small_pack_qty.
        DB::table('orders')->update([
            'small_pack_qty' => DB::raw('box_qty'),
            'big_pack_qty' => 0,
        ]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'big_pack_qty')) {
                $table->dropColumn('big_pack_qty');
            }
            if (Schema::hasColumn('orders', 'small_pack_qty')) {
                $table->dropColumn('small_pack_qty');
            }
        });
    }
};
