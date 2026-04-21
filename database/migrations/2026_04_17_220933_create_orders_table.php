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
            $table->foreignId('marketer_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_phone2')->nullable();
            $table->string('address');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('city_id');
            $table->string('city_name');
            $table->decimal('delivery_cost', 10, 2)->default(0);
            $table->decimal('products_total', 10, 2);
            $table->decimal('grand_total', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'shipped', 'delivered', 'cancelled'])->default('pending');
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
