<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packing_dispatches', function (Blueprint $table) {
            $table->id();
            // History survives invoice deletion; deliberately no cascading foreign key.
            $table->unsignedBigInteger('invoice_id')->index();
            $table->dateTime('sent_at')->index();
            $table->string('state', 20)->default('pending');
            $table->dateTime('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->string('source', 30)->default('live');
            $table->json('snapshot');
            $table->timestamps();
            $table->index(['invoice_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packing_dispatches');
    }
};
