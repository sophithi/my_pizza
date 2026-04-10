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
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'staff', 'staff_inventory') DEFAULT 'staff'");
        } else {
            // SQLite doesn't support MODIFY COLUMN or ENUM, recreate the column
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('staff')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'staff') DEFAULT 'staff'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('staff')->change();
            });
        }
    }
};
