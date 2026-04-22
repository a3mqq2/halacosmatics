<?php

namespace App\Services;

use App\Models\Marketer;
use App\Models\Product;

class DashboardService
{
    public function getStats(): array
    {
        return [
            'totalMarketers'   => Marketer::count(),
            'activeMarketers'  => Marketer::where('status', 'approved')->where('is_active', true)->count(),

            'totalProducts'    => Product::count(),
            'activeProducts'   => Product::where('is_active', true)->count(),
            'inactiveProducts' => Product::where('is_active', false)->count(),
            'totalQuantity'    => Product::sum('quantity'),
        ];
    }
}