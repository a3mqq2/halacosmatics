<?php

namespace App\Http\Controllers\Webhook;

use App\DTOs\FailDeliveryData;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MosafirWebhookController extends Controller
{
    private const STATUS_LABELS = [
        'UnderPreparation'              => 'عند العميل',
        'charging'                      => 'قيد التجهيز',
        'preparedInWarehouse'           => 'في الشركة',
        'UnderCollection'               => 'بانتظار التجميع',
        'WaitingForDelivery'            => 'قيد استلام المندوب',
        'ConnectingNow'                 => 'قيد التوصيل',
        'FinancialSettlementPending'    => 'تم التسليم',
        'RewindInProgress'              => 'عدم اكتمال عملية الشحن',
        'ReturnToCompany'               => 'قيد الاسترداد للشركة',
        'UnderAudit'                    => 'قيد المراجعة في المستودع',
        'Delay'                         => 'قيد التسليم',
        'ReturnedInWarehouse'           => 'مسترد بالمخزن',
        'Returned'                      => 'مسترد بالشركة',
        'ReturnedAndReceived'           => 'مسترد للمتجر',
        'DeliveryToBranch'              => 'قيد التوصيل للفرع',
        'UnderBranchSettlement'         => 'بانتظار تسوية الفرع',
        'onGoingUnderBranchSettlement'  => 'قيد التسوية مع الفرع',
        'DeliveryReturnToBranch'        => 'قيد الاسترداد للفرع',
        'Delivered'                     => 'تحت التسوية بالشركة',
        'Closed'                        => 'تم الاقفال',
        'UnderChecking'                 => 'قيد المراجعة',
    ];

    private const RETURNING_STATUSES = [
        'RewindInProgress',
        'ReturnToCompany',
        'DeliveryReturnToBranch',
    ];

    public function __construct(private OrderService $orderService) {}

    public function handle(Request $request): JsonResponse
    {
        $parcelId = $request->input('parcel_id');
        $status   = $request->input('status');

        if (! $parcelId || ! $status) {
            return response()->json(['ok' => false], 400);
        }

        $order = Order::where('mosafir_parcel_id', $parcelId)->first();

        if (! $order) {
            return response()->json(['ok' => true]);
        }

        $label = self::STATUS_LABELS[$status] ?? $status;

        if ($status === 'FinancialSettlementPending' && $order->status === 'with_agent') {
            $this->orderService->markDelivered($order);
            return response()->json(['ok' => true]);
        }

        if (in_array($status, self::RETURNING_STATUSES) && $order->status === 'with_agent') {
            $this->orderService->markFailedDelivery($order, new FailDeliveryData('other', "المسافر: {$label}"));
            return response()->json(['ok' => true]);
        }

        if ($status === 'ReturnedAndReceived' && $order->status === 'returning') {
            $this->orderService->markReturned($order);
            return response()->json(['ok' => true]);
        }

        $order->logs()->create([
            'action'      => 'mosafir_update',
            'description' => "تحديث المسافر — الحالة: {$label}",
        ]);

        return response()->json(['ok' => true]);
    }
}
