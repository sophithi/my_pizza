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
        Schema::dropIfExists('deliveries');

        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_name');
            $table->decimal('delivery_price', 10, 2)->default(0);
            $table->text('delivery_desc')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
