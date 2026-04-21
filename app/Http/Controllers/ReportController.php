<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        $fromStr = $from->toDateString();
        $toStr   = $to->toDateString();

        $diffDays = $from->diffInDays($to);
        $groupBy  = $diffDays > 31 ? 'month' : 'day';

        [$labels, $salesData, $halaRevenueData, $halaSupplierCostData, $halaProfitData, $marketerProfitData]
            = $this->buildChartSeries($from, $to, $groupBy);

        $deliveredBase = fn() => Order::where('status', 'delivered')->whereBetween('created_at', [$from, $to]);

        $totalCustomerSales = (float) $deliveredBase()->sum('products_total');
        $totalDelivered     = $deliveredBase()->count();
        $totalDeliveryFees  = (float) $deliveredBase()->sum('delivery_cost');

        $itemsDelivered = fn() => DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$from, $to]);

        $totalHalaRevenue  = (float) $itemsDelivered()->sum(DB::raw('order_items.quantity * order_items.product_cost'));
        $totalSupplierCost = (float) $itemsDelivered()->sum(DB::raw('order_items.quantity * order_items.product_supplier_cost'));
        $halaProfit        = $totalHalaRevenue - $totalSupplierCost;
        $totalMarketerProfit = $totalCustomerSales - $totalHalaRevenue;

        $totalReturned = Order::where('status', 'returned')->whereBetween('created_at', [$from, $to])->count();
        $totalRejected = Order::where('status', 'rejected')->whereBetween('created_at', [$from, $to])->count();
        $totalAll      = Order::whereBetween('created_at', [$from, $to])->count();
        $returnRate    = $totalAll > 0 ? round($totalReturned / $totalAll * 100, 1) : 0;
        $deliveryRate  = $totalAll > 0 ? round($totalDelivered / $totalAll * 100, 1) : 0;

        $statusCounts = Order::selectRaw('status, COUNT(*) as cnt')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $orderStatusFilter = $request->input('order_status');
        $ordersQuery = Order::with(['marketer'])
            ->withCount('items')
            ->whereBetween('created_at', [$from, $to])
            ->when($orderStatusFilter, fn($q) => $q->where('status', $orderStatusFilter))
            ->latest();

        $ordersTotal = $ordersQuery->count();
        $orders      = $ordersQuery->limit(400)->get();

        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.quantity * order_items.product_price) as total_revenue'),
                DB::raw('SUM(order_items.quantity * order_items.product_cost) as hala_revenue'),
                DB::raw('SUM(order_items.quantity * (order_items.product_price - order_items.product_cost)) as marketer_profit'),
            )
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$from, $to])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->get();

        $totalProductsRevenue = (float) ($topProducts->sum('total_revenue') ?: 1);

        $marketerProfits = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('marketers', 'marketers.id', '=', 'orders.marketer_id')
            ->select(
                DB::raw("CONCAT(marketers.first_name,' ',marketers.last_name) as name"),
                DB::raw('SUM(order_items.quantity * order_items.product_price) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.product_cost) as paid_to_hala'),
                DB::raw('SUM(order_items.quantity * (order_items.product_price - order_items.product_cost)) as profit'),
                DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
                DB::raw('SUM(order_items.quantity) as total_items')
            )
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$from, $to])
            ->groupBy('marketers.id', 'marketers.first_name', 'marketers.last_name')
            ->orderByDesc('profit')
            ->get();

        $halaProfitByProduct = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('ROUND(AVG(order_items.product_supplier_cost), 2) as avg_supplier_cost'),
                DB::raw('ROUND(AVG(order_items.product_cost), 2) as avg_hala_price'),
                DB::raw('SUM(order_items.quantity * order_items.product_supplier_cost) as total_supplier_cost'),
                DB::raw('SUM(order_items.quantity * order_items.product_cost) as total_hala_revenue'),
                DB::raw('SUM(order_items.quantity * (order_items.product_cost - order_items.product_supplier_cost)) as hala_profit')
            )
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$from, $to])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('hala_profit')
            ->get();

        $purchaseReport = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('ROUND(AVG(order_items.product_supplier_cost), 2) as avg_supplier_cost'),
                DB::raw('SUM(order_items.quantity * order_items.product_supplier_cost) as total_supplier_cost')
            )
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$from, $to])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_supplier_cost')
            ->get();

        return view('reports.index', compact(
            'fromStr', 'toStr',
            'labels', 'salesData', 'halaRevenueData', 'halaSupplierCostData', 'halaProfitData', 'marketerProfitData',
            'totalCustomerSales', 'totalDelivered', 'totalDeliveryFees',
            'totalHalaRevenue', 'totalSupplierCost', 'halaProfit', 'totalMarketerProfit',
            'totalReturned', 'totalRejected', 'totalAll', 'returnRate', 'deliveryRate',
            'statusCounts',
            'orders', 'ordersTotal', 'orderStatusFilter',
            'topProducts', 'totalProductsRevenue',
            'marketerProfits',
            'halaProfitByProduct',
            'purchaseReport'
        ));
    }

    private function buildChartSeries(Carbon $from, Carbon $to, string $groupBy): array
    {
        if ($groupBy === 'month') {
            $fmt = "DATE_FORMAT(orders.created_at,'%Y-%m')";
            $periods = [];
            $cursor  = $from->copy()->startOfMonth();
            while ($cursor->lte($to)) { $periods[] = $cursor->format('Y-m'); $cursor->addMonth(); }
            $am = ['01'=>'يناير','02'=>'فبراير','03'=>'مارس','04'=>'أبريل','05'=>'مايو','06'=>'يونيو',
                   '07'=>'يوليو','08'=>'أغسطس','09'=>'سبتمبر','10'=>'أكتوبر','11'=>'نوفمبر','12'=>'ديسمبر'];
            $labels = array_map(fn($p) => $am[substr($p, 5)] . ' ' . substr($p, 0, 4), $periods);
        } else {
            $fmt = 'DATE(orders.created_at)';
            $periods = [];
            $cursor  = $from->copy()->startOfDay();
            while ($cursor->lte($to)) { $periods[] = $cursor->format('Y-m-d'); $cursor->addDay(); }
            $labels = array_map(fn($p) => Carbon::parse($p)->format('d/m'), $periods);
        }

        $salesRaw = Order::selectRaw("$fmt as period, SUM(products_total) as val")
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('period')->orderBy('period')
            ->pluck('val', 'period');

        $halaRevRaw = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->selectRaw("$fmt as period, SUM(order_items.quantity * order_items.product_cost) as val")
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$from, $to])
            ->groupBy('period')->orderBy('period')
            ->pluck('val', 'period');

        $halaSupRaw = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->selectRaw("$fmt as period, SUM(order_items.quantity * order_items.product_supplier_cost) as val")
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$from, $to])
            ->groupBy('period')->orderBy('period')
            ->pluck('val', 'period');

        $salesData = $halaRevenueData = $halaSupplierCostData = $halaProfitData = $marketerProfitData = [];

        foreach ($periods as $p) {
            $s  = round((float) ($salesRaw[$p]   ?? 0), 2);
            $hr = round((float) ($halaRevRaw[$p]  ?? 0), 2);
            $hs = round((float) ($halaSupRaw[$p]  ?? 0), 2);

            $salesData[]            = $s;
            $halaRevenueData[]      = $hr;
            $halaSupplierCostData[] = $hs;
            $halaProfitData[]       = round($hr - $hs, 2);
            $marketerProfitData[]   = round($s - $hr, 2);
        }

        return [$labels, $salesData, $halaRevenueData, $halaSupplierCostData, $halaProfitData, $marketerProfitData];
    }
}
