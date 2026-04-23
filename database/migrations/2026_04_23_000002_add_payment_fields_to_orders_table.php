<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','processing','with_agent','delivered','returning','returned','rejected','cancelled') NOT NULL DEFAULT 'pending'");

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'bank_transfer'])->default('cash')->after('status');
            $table->string('payment_proof')->nullable()->after('payment_method');
            $table->boolean('delivery_included')->default(false)->after('payment_proof');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_proof', 'delivery_included']);
        });
    }
};
