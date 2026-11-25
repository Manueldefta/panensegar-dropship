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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('gateway_transaction_id')->unique(); // anti double-processing
            $table->string('payment_type')->nullable(); // qris, bank_transfer, dll
            $table->unsignedBigInteger('gross_amount');
            $table->string('transaction_status'); // pending, settlement, expire, cancel
            $table->json('raw_payload')->nullable(); // audit trail respons mentah webhook
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
