<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\MosafirClient;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $marketer = Auth::guard('marketer')->user();

        $filters = request()->only(['status', 'customer_name', 'customer_phone', 'city_name', 'date_from', 'date_to']);

        $orders = $marketer->orders()
            ->with('items')
            ->when($filters['status'] ?? null,         fn($q, $v) => $q->where('status', $v))
            ->when($filters['customer_name'] ?? null,  fn($q, $v) => $q->where('customer_name', 'like', "%{$v}%"))
            ->when($filters['customer_phone'] ?? null, fn($q, $v) => $q->where('customer_phone', 'like', "%{$v}%"))
            ->when($filters['city_name'] ?? null,      fn($q, $v) => $q->where(fn($q2) => $q2->where('city_name', 'like', "%{$v}%")->orWhere('sub_city_name', 'like', "%{$v}%")))
            ->when($filters['date_from'] ?? null,      fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['date_to'] ?? null,        fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('marketer.orders.index', compact('orders', 'filters'));
    }

    public function show(Order $order, MosafirClient $mosafir)
    {
        $marketer = Auth::guard('marketer')->user();

        if ($order->marketer_id != $marketer->id) {
            abort(403);
        }

        $order->load(['items.product', 'logs', 'localArea']);

        $mosafirParcel = $order->mosafir_parcel_id
            ? $mosafir->showParcel($order->mosafir_parcel_id)
            : null;

        return view('marketer.orders.show', compact('order', 'mosafirParcel'));
    }

    public function cancel(Order $order, OrderService $service)
    {
        $marketer = Auth::guard('marketer')->user();

        if ($order->marketer_id != $marketer->id) {
            abort(403);
        }

        if ($order->status != 'pending') {
            return back()->with('error', 'لا يمكن إلغاء هذا الطلب.');
        }

        $service->cancelByMarketer($order);

        return back()->with('success', 'تم إلغاء الطلب بنجاح.');
    }
}
