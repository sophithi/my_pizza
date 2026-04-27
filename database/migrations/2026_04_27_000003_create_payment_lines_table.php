<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->string('method', 50);
            $table->string('currency', 3)->default('USD');
            $table->decimal('amount_original', 12, 2)->default(0);
            $table->decimal('amount_usd', 12, 2)->default(0);
            $table->decimal('exchange_rate', 12, 2)->default(4000);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_lines');
    }
};
