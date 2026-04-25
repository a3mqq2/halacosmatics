<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('delivery_type', ['local', 'mosafir'])->default('mosafir')->after('delivery_cost');
            $table->foreignId('local_area_id')->nullable()->constrained('delivery_areas')->nullOnDelete()->after('delivery_type');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['local_area_id']);
            $table->dropColumn(['delivery_type', 'local_area_id']);
        });
    }
};
