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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('unit'); // contoh: 1 ikat, 500 gram, 1 kg
            $table->unsignedBigInteger('base_price'); // harga modal kebun
            $table->unsignedBigInteger('selling_price'); // harga jual ke pembeli
            $table->unsignedInteger('daily_quota')->default(0); // batas panen harian
            $table->boolean('is_available')->default(true); // tombol on/off petani
            $table->timestamps();        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
