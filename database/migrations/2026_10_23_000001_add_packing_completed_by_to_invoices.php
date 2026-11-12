<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('packing_completed_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', fn (Blueprint $table) => $table->dropConstrainedForeignId('packing_completed_by'));
    }
};
