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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // contoh: SYR-20261125-0001
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->date('delivery_date');
            $table->text('shipping_address');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('shipping_fee')->default(0);
            $table->unsignedBigInteger('total_amount');
            $table->string('status')->default('pending'); // pending, paid, harvested, delivered, cancelled, expired, need_refund
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Index gabungan untuk mempercepat query rekap panen harian
            $table->index(['delivery_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
