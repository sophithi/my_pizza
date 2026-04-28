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
        Schema::table('customers', function (Blueprint $table) {
            $columns = ['email', 'company_name', 'postal_code', 'contact_person'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'email')) {
                $table->string('email')->nullable()->after('type');
            }

            if (!Schema::hasColumn('customers', 'company_name')) {
                $table->string('company_name')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('customers', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('city');
            }

            if (!Schema::hasColumn('customers', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('company_name');
            }
        });
    }
};
