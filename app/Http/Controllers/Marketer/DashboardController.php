<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\OrderLog;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $marketer = Auth::guard('marketer')->user();

        $recentLogs = OrderLog::whereHas('order', fn($q) => $q->where('marketer_id', $marketer->id))
            ->with('order')
            ->latest()
            ->limit(5)
            ->get();

        $orderCounts = $marketer->orders()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('marketer.dashboard', compact('marketer', 'recentLogs', 'orderCounts'));
    }
}
