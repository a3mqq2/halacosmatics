<?php

namespace App\Console\Commands;

use App\DTOs\FailDeliveryData;
use App\Models\Order;
use App\Services\MosafirClient;
use App\Services\OrderService;
use Illuminate\Console\Command;

class SyncMosafirOrders extends Command
{
    protected $signature = 'mosafir:sync';

    protected $description = 'Sync order statuses from Mosafir API';

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

    public function __construct(
        private MosafirClient $mosafir,
        private OrderService $orderService,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $orders = Order::whereNotNull('mosafir_parcel_id')
            ->whereIn('status', ['with_agent', 'returning'])
            ->get();

        foreach ($orders as $order) {
            $parcel = $this->mosafir->showParcel($order->mosafir_parcel_id);

            if (! $parcel || empty($parcel['status'])) {
                continue;
            }

            $status = $parcel['status'];
            $label  = self::STATUS_LABELS[$status] ?? $status;

            if ($status === 'FinancialSettlementPending' && $order->status === 'with_agent') {
                $this->orderService->markDelivered($order);
                continue;
            }

            if (in_array($status, self::RETURNING_STATUSES) && $order->status === 'with_agent') {
                $this->orderService->markFailedDelivery($order, new FailDeliveryData('other', "المسافر: {$label}"));
                continue;
            }

            if ($status === 'ReturnedAndReceived' && $order->status === 'returning') {
                $this->orderService->markReturned($order);
                continue;
            }
        }
    }
}
