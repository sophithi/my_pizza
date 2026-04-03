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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('delivery_address');
            $table->string('delivery_phone')->nullable();
            $table->dateTime('scheduled_delivery_at')->nullable();
            $table->dateTime('actual_delivery_at')->nullable();
            $table->string('status')->default('pending'); // pending, preparing, out_for_delivery, delivered, cancelled
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable(); // reason if delivery was rejected
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
