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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('status')->default(0)->comment('0: not paid, 1: pending, 2: shipped, 3: delivered, 4: canceled');
            $table->decimal('order_price', 10, 2);
            $table->decimal('shipping_price', 10, 2);
            $table->string('shipping_address');
            $table->string('contact_number');
            $table->string('note')->nullable();
            $table->string('payment_method');
            $table->string('trx')->nullable();
            $table->timestamps();
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
