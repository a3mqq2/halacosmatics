<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketers', function (Blueprint $table) {
            if (!Schema::hasColumn('marketers', 'username')) {
                $table->string('username')->unique()->after('is_active');
            }
            if (!Schema::hasColumn('marketers', 'password')) {
                $table->string('password')->after('username');
            }
        });
    }

    public function down(): void
    {
        Schema::table('marketers', function (Blueprint $table) {
            $table->dropColumn(['username', 'password']);
        });
    }
};
