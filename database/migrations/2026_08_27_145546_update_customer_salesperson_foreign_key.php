<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop old foreign key constraint pointing to users
            $table->dropForeign(['salesperson_id']);
            
            // Add new foreign key constraint pointing to salespersons
            $table->foreign('salesperson_id')
                ->references('id')
                ->on('salespersons')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop new foreign key constraint
            $table->dropForeign(['salesperson_id']);
            
            // Restore old foreign key constraint pointing to users
            $table->foreign('salesperson_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }
};
