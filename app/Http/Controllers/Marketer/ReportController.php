<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $marketer = Auth::guard('marketer')->user();

        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : now()->startOfYear();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        $diffMonths = $from->diffInMonths($to);
        $groupBy    = $diffMonths > 1 ? 'month' : 'day';

        if ($groupBy === 'month') {
            $salesRaw = $marketer->orders()
                ->select(
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m') as period"),
                    DB::raw('SUM(products_total) as total_sales'),
                    DB::raw('COUNT(*) as orders_count')
                )
                ->where('status', 'delivered')
                ->whereBetween('created_at', [$from, $to])
                ->groupBy('period')
                ->orderBy('period')
                ->get();

            $costRaw = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->select(
                    DB::raw("DATE_FORMAT(orders.created_at, '%Y-%m') as period"),
                    DB::raw('SUM(order_items.quantity * order_items.product_cost) as total_cost')
                )
                ->where('orders.marketer_id', $marketer->id)
                ->where('orders.status', 'delivered')
                ->whereBetween('orders.created_at', [$from, $to])
                ->groupBy('period')
                ->orderBy('period')
                ->get();

            $periods = [];
            $cursor  = $from->copy()->startOfMonth();
            while ($cursor->lte($to)) {
                $periods[] = $cursor->format('Y-m');
                $cursor->addMonth();
            }

            $arabicMonths = ['01'=>'يناير','02'=>'فبراير','03'=>'مارس','04'=>'أبريل','05'=>'مايو','06'=>'يونيو','07'=>'يوليو','08'=>'أغسطس','09'=>'سبتمبر','10'=>'أكتوبر','11'=>'نوفمبر','12'=>'ديسمبر'];
            $labels = array_map(fn($p) => $arabicMonths[substr($p, 5)] . ' ' . substr($p, 0, 4), $periods);
        } else {
            $salesRaw = $marketer->orders()
                ->select(
                    DB::raw("DATE(created_at) as period"),
                    DB::raw('SUM(products_total) as total_sales'),
                    DB::raw('COUNT(*) as orders_count')
                )
                ->where('status', 'delivered')
                ->whereBetween('created_at', [$from, $to])
                ->groupBy('period')
                ->orderBy('period')
                ->get();

            $costRaw = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->select(
                    DB::raw("DATE(orders.created_at) as period"),
                    DB::raw('SUM(order_items.quantity * order_items.product_cost) as total_cost')
                )
                ->where('orders.marketer_id', $marketer->id)
                ->where('orders.status', 'delivered')
                ->whereBetween('orders.created_at', [$from, $to])
                ->groupBy('period')
                ->orderBy('period')
                ->get();

            $periods = [];
            $cursor  = $from->copy()->startOfDay();
            while ($cursor->lte($to)) {
                $periods[] = $cursor->format('Y-m-d');
                $cursor->addDay();
            }

            $labels = array_map(fn($p) => Carbon::parse($p)->format('d/m'), $periods);
        }

        $salesKeyed = $salesRaw->keyBy('period');
        $costKeyed  = $costRaw->keyBy('period');

        $salesData  = [];
        $costData   = [];
        $profitData = [];
        $ordersData = [];

        foreach ($periods as $p) {
            $s            = (float) ($salesKeyed[$p]->total_sales ?? 0);
            $c            = (float) ($costKeyed[$p]->total_cost   ?? 0);
            $salesData[]  = round($s, 2);
            $costData[]   = round($c, 2);
            $profitData[] = round($s - $c, 2);
            $ordersData[] = (int) ($salesKeyed[$p]->orders_count ?? 0);
        }

        $totalSales  = array_sum($salesData);
        $totalCost   = array_sum($costData);
        $totalProfit = array_sum($profitData);
        $totalOrders = array_sum($ordersData);

        $fromStr = $from->toDateString();
        $toStr   = $to->toDateString();

        return view('marketer.reports', compact(
            'labels', 'salesData', 'costData', 'profitData', 'ordersData',
            'totalSales', 'totalCost', 'totalProfit', 'totalOrders',
            'fromStr', 'toStr'
        ));
    }
}
