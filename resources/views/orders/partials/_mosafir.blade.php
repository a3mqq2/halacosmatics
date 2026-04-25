@php
    $statusMap = [
        'UnderPreparation'           => ['label' => 'قيد التجهيز',          'color' => 'warning'],
        'charging'                   => ['label' => 'في الطريق',             'color' => 'info'],
        'ConnectingNow'              => ['label' => 'جارٍ الاتصال بالمستلم', 'color' => 'primary'],
        'Delivered'                  => ['label' => 'تم التسليم',            'color' => 'success'],
        'FinancialSettlementPending' => ['label' => 'بانتظار التسوية المالية','color' => 'warning'],
        'Returned'                   => ['label' => 'مسترد',                 'color' => 'secondary'],
        'Cancelled'                  => ['label' => 'ملغي',                  'color' => 'danger'],
    ];
    $statusInfo = $statusMap[$mosafirParcel['status'] ?? ''] ?? ['label' => $mosafirParcel['status'] ?? '—', 'color' => 'secondary'];
@endphp

{{-- Status + Info ──────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <div class="text-muted small mb-1">رقم الشحنة</div>
        <div class="fw-bold fs-5">#{{ $mosafirParcel['id'] }}</div>
    </div>
    <span class="badge bg-{{ $statusInfo['color'] }}-subtle text-{{ $statusInfo['color'] }} border border-{{ $statusInfo['color'] }}-subtle px-3 py-2 fs-6">
        {{ $statusInfo['label'] }}
    </span>
</div>

<div class="row g-2 mb-4">
    @if(!empty($mosafirParcel['tocity']['name']))
    <div class="col-6 col-md-3">
        <div class="rounded-3 p-2 text-center" style="background:#f8f9fa">
            <div class="text-muted small">المدينة</div>
            <div class="fw-semibold">{{ $mosafirParcel['tocity']['name'] }}</div>
        </div>
    </div>
    @endif
    <div class="col-6 col-md-3">
        <div class="rounded-3 p-2 text-center" style="background:#f8f9fa">
            <div class="text-muted small">الكمية</div>
            <div class="fw-semibold">{{ $mosafirParcel['qty'] ?? '—' }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="rounded-3 p-2 text-center" style="background:#f8f9fa">
            <div class="text-muted small">سعر المنتج</div>
            <div class="fw-semibold">{{ number_format($mosafirParcel['product_price'] ?? 0) }} د.ل</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="rounded-3 p-2 text-center" style="background:#f8f9fa">
            <div class="text-muted small">رسوم الشحن</div>
            <div class="fw-semibold">{{ number_format($mosafirParcel['shipping_price'] ?? 0) }} د.ل</div>
        </div>
    </div>
</div>

{{-- Delivery Man ───────────────────────────────── --}}
@if(isset($order) && $order->agent)
<div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background:#fdf8f6;border:1px solid #e8d5cc">
    <div style="width:40px;height:40px;border-radius:50%;background:#4a2619;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <i class="ti ti-user text-white" style="font-size:1.1rem"></i>
    </div>
    <div class="flex-grow-1">
        <div class="text-muted small mb-1">المندوب الداخلي</div>
        <div class="fw-bold" style="color:#4a2619">{{ $order->agent->name }}</div>
        @if($order->agent->phone)
        <div class="text-muted small">{{ $order->agent->phone }}</div>
        @endif
    </div>
    @if($order->agent->phone)
    <a href="tel:{{ $order->agent->phone }}" class="btn btn-sm btn-outline-secondary rounded-3">
        <i class="ti ti-phone me-1"></i> اتصال
    </a>
    @endif
</div>
@endif

@if(!empty($mosafirParcel['deliveryman']))
<div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-4" style="background:#f0fdf4;border:1px solid #bbf7d0">
    <div style="width:40px;height:40px;border-radius:50%;background:#16a34a;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <i class="ti ti-motorbike text-white" style="font-size:1.1rem"></i>
    </div>
    <div class="flex-grow-1">
        <div class="text-muted small mb-1">مندوب المسافر</div>
        <div class="fw-bold" style="color:#15803d">{{ $mosafirParcel['deliveryman']['name'] }}</div>
        <div class="text-muted small">{{ $mosafirParcel['deliveryman']['phone'] }}</div>
    </div>
    <a href="tel:{{ $mosafirParcel['deliveryman']['phone'] }}" class="btn btn-sm btn-outline-success rounded-3">
        <i class="ti ti-phone me-1"></i> اتصال
    </a>
</div>
@endif

{{-- Records Timeline ────────────────────────────── --}}
<div class="fw-semibold mb-2" style="font-size:.85rem;color:#6b7280">
    <i class="ti ti-list me-1"></i> سجل تتبع الشحنة
</div>

@if(!empty($mosafirParcel['records']))
<div style="position:relative;padding-right:20px">
    <div style="position:absolute;right:7px;top:6px;bottom:6px;width:2px;background:#e5e7eb;border-radius:2px"></div>
    @foreach($mosafirParcel['records'] as $record)
    <div style="position:relative;margin-bottom:1rem">
        <div style="position:absolute;right:-20px;top:4px;width:10px;height:10px;border-radius:50%;background:#4a2619;border:2px solid #fff;box-shadow:0 0 0 2px #4a2619"></div>
        <div class="fw-semibold" style="font-size:.88rem;color:#1a1a1a">{{ $record['details'] }}</div>
        <div class="text-muted" style="font-size:.75rem">
            {{ \Carbon\Carbon::parse($record['created_at'])->format('Y/m/d — H:i') }}
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-muted small text-center py-3">لا توجد سجلات متاحة</div>
@endif
