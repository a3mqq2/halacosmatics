<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Marketer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vault;

class DashboardService
{
    public function getStats(): array
    {
        return [
            'totalMarketers'   => Marketer::count(),

            'totalProducts'    => Product::count(),
            'activeProducts'   => Product::where('is_active', true)->count(),
            'inactiveProducts' => Product::where('is_active', false)->count(),
            'totalQuantity'    => Product::sum('quantity'),

            'ordersPending'    => Order::where('status', 'pending')->count(),
            'ordersProcessing' => Order::where('status', 'processing')->count(),
            'ordersWithAgent'  => Order::where('status', 'with_agent')->count(),
            'ordersDelivered'  => Order::where('status', 'delivered')->count(),
            'ordersReturning'  => Order::where('status', 'returning')->count(),
            'ordersReturned'   => Order::where('status', 'returned')->count(),

            'totalAgents'      => Agent::count(),
            'totalUsers'       => User::count(),
            'totalVaults'      => Vault::count(),
        ];
    }
}
