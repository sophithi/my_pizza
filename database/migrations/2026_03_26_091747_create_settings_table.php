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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->default('Pizza Happy Family');
            $table->string('business_email')->nullable();
            $table->string('business_phone')->nullable();
            $table->string('business_address')->nullable();
            $table->string('business_city')->nullable();
            $table->string('business_postal_code')->nullable();
            $table->text('business_description')->nullable();
            $table->string('currency')->default('PHP');
            $table->decimal('tax_rate', 5, 2)->default(10);
            $table->string('tax_name')->default('VAT');
            $table->string('invoice_prefix')->default('INV');
            $table->boolean('enable_notifications')->default(true);
            $table->boolean('enable_email_invoices')->default(true);
            $table->string('mail_driver')->default('smtp');
            $table->string('mail_host')->nullable();
            $table->integer('mail_port')->default(587);
            $table->string('mail_username')->nullable();
            $table->string('mail_password')->nullable();
            $table->string('mail_encryption')->default('tls');
            $table->string('timezone')->default('Asia/Manila');
            $table->string('date_format')->default('Y-m-d');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
