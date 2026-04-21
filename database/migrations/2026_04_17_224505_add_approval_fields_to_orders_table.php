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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'processing', 'rejected', 'shipped', 'delivered', 'cancelled'])
                  ->default('pending')
                  ->after('grand_total');

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            $table->text('rejected_reason')->nullable()->after('rejected_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['approved_by', 'approved_at', 'rejected_by', 'rejected_at', 'rejected_reason', 'status']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'shipped', 'delivered', 'cancelled'])
                  ->default('pending')
                  ->after('grand_total');
        });
    }
};
