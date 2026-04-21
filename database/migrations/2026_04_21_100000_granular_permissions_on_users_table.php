<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('perm_orders_pending')->default(false)->after('is_super');
            $table->boolean('perm_orders_active')->default(false)->after('perm_orders_pending');
            $table->boolean('perm_orders_delivered')->default(false)->after('perm_orders_active');
            $table->boolean('perm_orders_returned')->default(false)->after('perm_orders_delivered');
            $table->boolean('perm_orders_approve')->default(false)->after('perm_orders_returned');
            $table->boolean('perm_orders_deliver')->default(false)->after('perm_orders_approve');
            $table->boolean('perm_products_view')->default(false)->after('perm_orders_deliver');
            $table->boolean('perm_products_prices')->default(false)->after('perm_products_view');
            $table->boolean('perm_products_costs')->default(false)->after('perm_products_prices');
            $table->boolean('perm_products_edit')->default(false)->after('perm_products_costs');
            $table->boolean('perm_products_stock')->default(false)->after('perm_products_edit');
            $table->boolean('perm_marketers_view')->default(false)->after('perm_products_stock');
            $table->boolean('perm_marketers_manage')->default(false)->after('perm_marketers_view');
            $table->boolean('perm_marketers_finance')->default(false)->after('perm_marketers_manage');
            $table->boolean('perm_reports')->default(false)->after('perm_marketers_finance');

            $table->dropColumn(['perm_products', 'perm_marketers']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'perm_orders_pending', 'perm_orders_active', 'perm_orders_delivered',
                'perm_orders_returned', 'perm_orders_approve', 'perm_orders_deliver',
                'perm_products_view', 'perm_products_prices', 'perm_products_costs',
                'perm_products_edit', 'perm_products_stock',
                'perm_marketers_view', 'perm_marketers_manage', 'perm_marketers_finance',
                'perm_reports',
            ]);

            $table->boolean('perm_products')->default(false);
            $table->boolean('perm_marketers')->default(false);
        });
    }
};
