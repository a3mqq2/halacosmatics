<?php

namespace App\Http\Controllers;

use App\DTOs\FailDeliveryData;
use App\Http\Requests\AcceptReturnRequest;
use App\Http\Requests\ApproveOrderRequest;
use App\Http\Requests\CancelOrderRequest;
use App\Http\Requests\DeliverOrderRequest;
use App\Http\Requests\FailDeliveryRequest;
use App\Models\Agent;
use App\Models\Order;
use App\Models\Vault;
use App\Services\MosafirClient;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderController extends Controller
{
    private function resolveAllowedStatuses($user): array
    {
        if ($user->is_super) {
            return ['pending', 'processing', 'with_agent', 'delivered', 'returning', 'returned', 'rejected', 'cancelled'];
        }

        $statuses = [];
        if ($user->can_access('orders.pending'))   $statuses[] = 'pending';
        if ($user->can_access('orders.active'))    array_push($statuses, 'processing', 'with_agent');
        if ($user->can_access('orders.delivered')) $statuses[] = 'delivered';
        if ($user->can_access('orders.returned'))  array_push($statuses, 'returning', 'returned', 'rejected', 'cancelled');

        return $statuses;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $allowedStatuses = $this->resolveAllowedStatuses($user);

        if (empty($allowedStatuses)) abort(403);

        $orders = QueryBuilder::for(Order::class)
            ->allowedFilters(
                AllowedFilter::partial('customer_name'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('marketer_id'),
                AllowedFilter::callback('delivery_type', function ($query, $value) {
                    if ($value === 'mosafir') {
                        $query->whereNotNull('mosafir_parcel_id');
                    } elseif ($value === 'agent') {
                        $query->whereNotNull('agent_id')->whereNull('mosafir_parcel_id');
                    }
                }),
            )
            ->whereIn('status', $allowedStatuses)
            ->with(['marketer'])
            ->withCount('items')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('orders.index', compact('orders', 'allowedStatuses'));
    }

    public function show(Order $order, MosafirClient $mosafir)
    {
        $user = Auth::user();
        if (!$user->is_super) {
            $allowedStatuses = $this->resolveAllowedStatuses($user);
            if (!in_array($order->status, $allowedStatuses)) {
                abort(403);
            }
        }

        $order->load(['marketer', 'items.product', 'logs', 'approvedBy', 'rejectedBy', 'agent', 'localArea']);
        $agents = Agent::where('is_active', true)->orderBy('name')->get();
        $cities = $mosafir->getPrices() ?? [];

        $mosafirParcel = $order->mosafir_parcel_id
            ? $mosafir->showParcel($order->mosafir_parcel_id)
            : null;

        $vaults = Vault::orderBy('name')->get();

        return view('orders.show', compact('order', 'agents', 'cities', 'mosafirParcel', 'vaults'));
    }

    public function dispatch(Request $request, Order $order, MosafirClient $mosafir)
    {
        if ($order->status != 'processing') {
            return back()->with('error', 'لا يمكن إحالة هذا الطلب.');
        }

        $type = $request->input('type');

        if ($type === 'mosafir') {
            if ($order->delivery_type === 'local') {
                return back()->with('error', 'طلبات التوصيل المحلي لا يمكن إحالتها لشركة المسافر.');
            }

            $request->validate([
                'customer_name'    => 'required|string|max:100',
                'recipient_number' => 'required|string|max:20',
                'to_city_id'       => 'required',
                'desc'             => 'required|string|max:500',
            ]);

            if ($order->payment_method === 'bank_transfer') {
                $mosafirProductPrice = $order->delivery_included ? 0 : (float) $order->delivery_cost;
                $mosafirDeliveryOn   = $order->delivery_included ? 'market' : 'customer';
            } else {
                $mosafirProductPrice = $order->collection_amount;
                $mosafirDeliveryOn   = 'customer';
            }

            $parcel = $mosafir->createParcel([
                'desc'             => $request->desc,
                'customer_name'    => $request->customer_name,
                'qty'              => $order->items->sum('quantity'),
                'recipient_number' => $request->recipient_number,
                'product_price'    => $mosafirProductPrice,
                'address'          => $order->address,
                'delivery_on'      => $mosafirDeliveryOn,
                'to_city_id'       => $request->to_city_id,
                'is_payment_down'  => true,
            ]);

            if (! $parcel) {
                return back()->with('error', 'فشل الاتصال بشركة المسافر، تحقق من الربط في الإعدادات.');
            }

            $order->update([
                'status'            => 'with_agent',
                'mosafir_parcel_id' => $parcel['parcel_id'] ?? null,
            ]);

            $order->logs()->create([
                'action'      => 'dispatched',
                'description' => "تمت إحالة الطلب لشركة المسافر — رقم الشحنة: " . ($parcel['parcel_id'] ?? '—'),
            ]);

            return back()->with('success', 'تمت إحالة الطلب لشركة المسافر بنجاح.');
        }

        if ($type === 'agent') {
            $request->validate(['agent_id' => 'required|exists:agents,id']);

            $agent = Agent::findOrFail($request->agent_id);

            $order->update([
                'status'   => 'with_agent',
                'agent_id' => $agent->id,
            ]);

            $order->logs()->create([
                'action'      => 'dispatched',
                'description' => "تمت إحالة الطلب للمندوب: {$agent->name}",
            ]);

            return back()->with('success', "تمت إحالة الطلب للمندوب {$agent->name} بنجاح.");
        }

        return back()->with('error', 'نوع الإحالة غير صالح.');
    }

    public function approve(ApproveOrderRequest $request, Order $order, OrderService $service)
    {
        if ($order->status != 'pending') {
            return back()->with('error', 'لا يمكن الموافقة على هذا الطلب.');
        }

        $service->approve($order, $request->input('vault_id') ? (int) $request->input('vault_id') : null);

        return back()->with('success', 'تمت الموافقة على الطلب بنجاح.');
    }

    public function acceptReturn(AcceptReturnRequest $_, Order $order, OrderService $service)
    {
        if ($order->status != 'returning' || ! $order->agent_id) {
            return back()->with('error', 'لا يمكن تنفيذ هذه العملية على هذا الطلب.');
        }

        $service->markReturned($order);

        return back()->with('success', 'تم استلام الطلب المسترد وإعادة الكميات للمخزون.');
    }

    public function deliver(DeliverOrderRequest $_, Order $order, OrderService $service)
    {
        if ($order->status != 'with_agent' || ! $order->agent_id) {
            return back()->with('error', 'لا يمكن تنفيذ هذه العملية على هذا الطلب.');
        }

        $service->markDelivered($order);

        return back()->with('success', 'تم تسليم الطلب بنجاح.');
    }

    public function failDelivery(FailDeliveryRequest $request, Order $order, OrderService $service)
    {
        if ($order->status != 'with_agent' || ! $order->agent_id) {
            return back()->with('error', 'لا يمكن تنفيذ هذه العملية على هذا الطلب.');
        }

        $data = new FailDeliveryData($request->validated('reason'), $request->validated('notes'));
        $service->markFailedDelivery($order, $data);

        return back()->with('success', 'تم تسجيل تعذر التسليم وتحديث حالة الطلب إلى قيد الاسترداد.');
    }

    public function cancel(CancelOrderRequest $request, Order $order, OrderService $service)
    {
        if ($order->status != 'processing') {
            return back()->with('error', 'لا يمكن إلغاء هذا الطلب.');
        }

        $service->cancelByAdmin($order, $request->validated('cancelled_reason'));

        return back()->with('success', 'تم إلغاء الطلب بنجاح وإعادة الكميات للمخزون.');
    }

    public function reject(Request $request, Order $order)
    {
        $request->validate([
            'rejected_reason' => 'required|string|max:500',
        ]);

        if ($order->status != 'pending') {
            return back()->with('error', 'لا يمكن رفض هذا الطلب.');
        }

        $admin = Auth::guard('web')->user();

        $order->update([
            'status'          => 'rejected',
            'rejected_by'     => $admin->id,
            'rejected_at'     => now(),
            'rejected_reason' => $request->rejected_reason,
        ]);

        $order->logs()->create([
            'action'      => 'rejected',
            'description' => "تم رفض الطلب بواسطة {$admin->name} — السبب: {$request->rejected_reason}",
        ]);

        return back()->with('success', 'تم رفض الطلب.');
    }
}
