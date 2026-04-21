<?php

namespace App\Http\ViewComposers;

use App\Models\Order;
use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view): void
    {
        $orderCounts = Order::query()
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        $view->with('orderCounts', $orderCounts);
    }
}
