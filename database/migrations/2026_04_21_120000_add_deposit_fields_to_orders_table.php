<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('has_deposit')->default(false)->after('status');
            $table->unsignedSmallInteger('deposit_amount')->nullable()->after('has_deposit');
            $table->enum('deposit_payer', ['marketer', 'company'])->nullable()->after('deposit_amount');
            $table->string('deposit_proof')->nullable()->after('deposit_payer');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['has_deposit', 'deposit_amount', 'deposit_payer', 'deposit_proof']);
        });
    }
};
