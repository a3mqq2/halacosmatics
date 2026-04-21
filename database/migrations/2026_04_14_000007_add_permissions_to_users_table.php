<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('perm_users')->default(false)->after('password');
            $table->boolean('perm_marketers')->default(false)->after('perm_users');
            $table->boolean('perm_products')->default(false)->after('perm_marketers');
            $table->boolean('is_super')->default(false)->after('perm_products');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['perm_users', 'perm_marketers', 'perm_products', 'is_super']);
        });
    }
};
