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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('password');
            $table->timestamp('last_logout_at')->nullable()->after('last_login_at');
            $table->string('last_login_ip')->nullable()->after('last_logout_at');
            $table->string('last_login_user_agent')->nullable()->after('last_login_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_login_at', 'last_logout_at', 'last_login_ip', 'last_login_user_agent']);
        });
    }
};
