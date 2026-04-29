@extends('layouts.marketer')

@section('title', 'طلب #{{ $order->id }}')

@use('Illuminate\Support\Facades\Storage')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="fw-bold mb-0" style="color:#4a2619;font-size:1.1rem">طلب #{{ $order->id }}</h2>
    @if($order->status === 'pending')
    <button type="button" class="btn btn-outline-danger btn-sm"
            data-bs-toggle="modal" data-bs-target="#cancelModal">
        <i class="ti ti-ban me-1"></i> إلغاء الطلب
    </button>
    @endif
</div>

@php
    $agentName  = null;
    $agentPhone = null;
    $agentType  = null;
    if (!empty($mosafirParcel['deliveryman']['phone'] ?? null)) {
        $agentName  = $mosafirParcel['deliveryman']['name'] ?? 'مندوب المسافر';
        $agentPhone = $mosafirParcel['deliveryman']['phone'];
        $agentType  = 'mosafir';
    } elseif ($order->agent && $order->agent->phone) {
        $agentName  = $order->agent->name;
        $agentPhone = $order->agent->phone;
        $agentType  = 'local';
    }
@endphp

@if($agentPhone)
<div class="d-flex align-items-center gap-3 mb-3 p-3 rounded-4"
     style="background:linear-gradient(135deg,{{ $agentType === 'mosafir' ? '#f0fdf4 0%, #dcfce7' : '#fef9f5 0%, #fdeee8' }} 100%);
            border:2px solid {{ $agentType === 'mosafir' ? '#86efac' : '#e8c5b3' }};
            box-shadow:0 4px 14px {{ $agentType === 'mosafir' ? 'rgba(22,163,74,.12)' : 'rgba(74,38,25,.12)' }}">
    <div style="width:48px;height:48px;border-radius:50%;
                background:{{ $agentType === 'mosafir' ? '#16a34a' : '#4a2619' }};
                display:flex;align-items:center;justify-content:center;flex-shrink:0;
                box-shadow:0 4px 10px {{ $agentType === 'mosafir' ? 'rgba(22,163,74,.35)' : 'rgba(74,38,25,.35)' }}">
        <i class="ti ti-motorbike text-white" style="font-size:1.4rem"></i>
    </div>
    <div style="flex:1;min-width:0">
        <div style="font-size:.7rem;font-weight:700;color:{{ $agentType === 'mosafir' ? '#15803d' : '#4a2619' }};letter-spacing:.04em;line-height:1">
            {{ $agentType === 'mosafir' ? 'مندوب المسافر' : 'مندوب التوصيل' }}
        </div>
        <div class="fw-bold mt-1" style="font-size:.95rem;color:#1a1a1a;line-height:1.2">{{ $agentName }}</div>
        <div dir="ltr" class="fw-bold mt-1"
             style="font-size:1.05rem;color:{{ $agentType === 'mosafir' ? '#15803d' : '#4a2619' }};letter-spacing:.03em;line-height:1">
            {{ $agentPhone }}
        </div>
    </div>
    <a href="tel:{{ $agentPhone }}"
       class="d-inline-flex align-items-center justify-content-center"
       style="width:46px;height:46px;border-radius:50%;
              background:{{ $agentType === 'mosafir' ? '#16a34a' : '#4a2619' }};
              color:#fff;flex-shrink:0;text-decoration:none;
              box-shadow:0 4px 10px {{ $agentType === 'mosafir' ? 'rgba(22,163,74,.35)' : 'rgba(74,38,25,.35)' }}">
        <i class="ti ti-phone-call" style="font-size:1.2rem"></i>
    </a>
</div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header p-0 border-bottom">
        <ul class="nav nav-tabs card-header-tabs px-2" role="tablist">

            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-customer" type="button">
                    <i class="ti ti-user me-1"></i> الزبون
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-products" type="button">
                    <i class="ti ti-box me-1"></i> المنتجات
                    <span class="badge bg-secondary ms-1">{{ $order->items->count() }}</span>
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-summary" type="button">
                    <i class="ti ti-receipt me-1"></i> الملخص
                </button>
            </li>

            @if($mosafirParcel)
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-mosafir" type="button">
                    <img src="{{ asset('mosafer.svg') }}" style="height:16px;margin-left:4px"> المسافر
                </button>
            </li>
            @endif

            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-logs" type="button">
                    <i class="ti ti-history me-1"></i> السجل
                </button>
            </li>

        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content">

            {{-- Tab: Customer --}}
            <div class="tab-pane fade show active" id="tab-customer" role="tabpanel">
                <table class="table table-sm table-borderless mb-0 px-1">
                    <tr>
                        <th class="text-muted fw-normal ps-3" width="38%">الاسم</th>
                        <td class="fw-semibold pe-3">{{ $order->customer_name }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal ps-3">الهاتف</th>
                        <td class="pe-3">{{ $order->customer_phone }}</td>
                    </tr>
                    @if($order->customer_phone2)
                    <tr>
                        <th class="text-muted fw-normal ps-3">هاتف احتياطي</th>
                        <td class="pe-3">{{ $order->customer_phone2 }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th class="text-muted fw-normal ps-3">المدينة</th>
                        <td class="pe-3">{{ $order->city_name }}</td>
                    </tr>
                    @if($order->sub_city_name)
                    <tr>
                        <th class="text-muted fw-normal ps-3">المنطقة</th>
                        <td class="pe-3">{{ $order->sub_city_name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th class="text-muted fw-normal ps-3">العنوان</th>
                        <td class="pe-3">{{ $order->address }}</td>
                    </tr>
                    @if($order->notes)
                    <tr>
                        <th class="text-muted fw-normal ps-3">ملاحظات</th>
                        <td class="text-muted pe-3">{{ $order->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            {{-- Tab: Products --}}
            <div class="tab-pane fade" id="tab-products" role="tabpanel">
                <div class="d-flex flex-column gap-3 p-3">
                    @foreach($order->items as $item)
                    @php $img = $item->product?->primaryImage?->path ?? null; @endphp
                    <div class="d-flex gap-3 p-3 rounded-3" style="border:1.5px solid #f0ebe8;background:#fdfaf9">
                        @if($img)
                            <img src="{{ Storage::url($img) }}" alt="{{ $item->product_name }}"
                                 style="width:68px;height:68px;object-fit:cover;border-radius:12px;flex-shrink:0">
                        @else
                            <div style="width:68px;height:68px;border-radius:12px;background:#f5ede9;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#c8a898;font-size:1.5rem">
                                <i class="ti ti-photo"></i>
                            </div>
                        @endif
                        <div style="flex:1;min-width:0">
                            <div class="fw-bold text-truncate mb-2" style="font-size:.93rem;color:#1a1a1a">{{ $item->product_name }}</div>
                            <div class="d-flex flex-wrap gap-2">
                                <span style="font-size:.75rem;background:#fff;border:1px solid #e8e0dc;border-radius:20px;padding:.15rem .6rem;color:#555;font-weight:600">
                                    × {{ $item->quantity }}
                                </span>
                                <span style="font-size:.75rem;background:#fff;border:1px solid #e8e0dc;border-radius:20px;padding:.15rem .6rem;color:#555;font-weight:600">
                                    {{ number_format($item->product_price) }} د.ل / وحدة
                                </span>
                            </div>
                            <div class="mt-2 fw-bold" style="font-size:.98rem;color:#4a2619">
                                {{ number_format($item->total) }} <span style="font-size:.75rem;font-weight:600;color:#aaa">د.ل</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Tab: Summary --}}
            <div class="tab-pane fade" id="tab-summary" role="tabpanel">
                <div class="p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">الحالة</span>
                        <span class="badge bg-{{ $order->status_color }}-subtle text-{{ $order->status_color }} border border-{{ $order->status_color }}-subtle">
                            {{ $order->status_label }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted">طريقة الدفع</span>
                        <div class="text-end">
                            @if($order->payment_method === 'bank_transfer')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">تحويل مصرفي</span>
                                <div class="small text-muted mt-1">
                                    {{ $order->delivery_included ? 'شامل التوصيل' : 'منتجات فقط' }}
                                </div>
                                @if($order->payment_proof)
                                <div class="mt-1" style="font-size:.82rem">
                                    <a href="{{ Storage::url($order->payment_proof) }}" target="_blank" class="text-decoration-none text-primary">
                                        <i class="ti ti-file-check me-1"></i> إيصال التحويل
                                    </a>
                                </div>
                                @endif
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">كاش</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">إجمالي المنتجات</span>
                        <span>{{ number_format($order->products_total) }} د.ل</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted">نوع التوصيل</span>
                        <div class="text-end">
                            @if($order->delivery_type === 'local')
                                <span class="badge bg-info-subtle text-info border border-info-subtle">
                                    <i class="ti ti-map-pin me-1"></i> محلي — بنغازي
                                </span>
                                @if($order->localArea)
                                <div class="small text-muted mt-1">{{ $order->localArea->name }}</div>
                                @endif
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                    <i class="ti ti-truck-delivery me-1"></i> شركة المسافر
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($order->agent)
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted">المندوب</span>
                        <div class="text-end">
                            <div class="fw-semibold" style="font-size:.9rem">{{ $order->agent->name }}</div>
                            <a href="tel:{{ $order->agent->phone }}" class="small text-decoration-none text-success">
                                <i class="ti ti-phone me-1"></i>{{ $order->agent->phone }}
                            </a>
                            @if($order->agent->phone2)
                            <div>
                                <a href="tel:{{ $order->agent->phone2 }}" class="small text-decoration-none text-secondary">
                                    <i class="ti ti-phone me-1"></i>{{ $order->agent->phone2 }}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">رسوم التوصيل</span>
                        <span>{{ number_format($order->delivery_cost) }} د.ل</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fw-bold">
                        <span>الإجمالي الكلي</span>
                        <span style="color:#4a2619">{{ number_format($order->grand_total) }} د.ل</span>
                    </div>
                    @if($order->payment_method === 'bank_transfer')
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fw-bold">
                        <span style="color:#16a34a">يُستلم عند التسليم</span>
                        <span style="color:#16a34a">{{ number_format($order->collection_amount) }} د.ل</span>
                    </div>
                    @if($order->delivery_included)
                    <div class="small text-muted mt-1">التوصيل مشمول في التحويل — لا يُستلم شيء من الزبون</div>
                    @else
                    <div class="small text-muted mt-1">يُستلم رسم التوصيل فقط من الزبون</div>
                    @endif
                    @endif
                    @if($order->has_deposit && $order->deposit_amount)
                    <div class="d-flex justify-content-between mt-2 text-muted" style="font-size:.9rem">
                        <span>العربون المدفوع
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-normal ms-1" style="font-size:.72rem">
                                {{ $order->deposit_payer === 'company' ? 'على حساب الشركة' : 'على حسابي' }}
                            </span>
                        </span>
                        <span>− {{ number_format($order->deposit_amount) }} د.ل</span>
                    </div>
                    @if($order->deposit_proof)
                    <div class="mt-1" style="font-size:.82rem">
                        <a href="{{ Storage::url($order->deposit_proof) }}" target="_blank" class="text-decoration-none text-secondary">
                            <i class="ti ti-file-check me-1"></i> إثبات التحويل
                        </a>
                    </div>
                    @endif
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fw-bold">
                        <span style="color:#16a34a">يُستلم عند التسليم</span>
                        <span style="color:#16a34a">{{ number_format($order->collection_amount) }} د.ل</span>
                    </div>
                    @endif
                    @if($order->approved_at)
                    <hr class="my-2">
                    <div class="d-flex justify-content-between small text-success">
                        <span><i class="ti ti-check me-1"></i> تمت الموافقة</span>
                        <span>{{ dt($order->approved_at) }}</span>
                    </div>
                    @endif
                    @if($order->rejected_at)
                    <hr class="my-2">
                    <div class="small text-danger mb-1">
                        <i class="ti ti-x me-1"></i> تم الرفض — {{ dt($order->rejected_at) }}
                    </div>
                    <div class="small text-muted bg-danger-subtle rounded p-2">{{ $order->rejected_reason }}</div>
                    @endif
                    @if($order->cancelled_at)
                    <hr class="my-2">
                    <div class="small text-danger mb-1">
                        <i class="ti ti-ban me-1"></i> تم الإلغاء — {{ dt($order->cancelled_at) }}
                    </div>
                    @if($order->cancelled_reason)
                    <div class="small text-muted bg-danger-subtle rounded p-2">{{ $order->cancelled_reason }}</div>
                    @endif
                    @endif
                </div>
            </div>

            {{-- Tab: Mosafir --}}
            @if($mosafirParcel)
            <div class="tab-pane fade" id="tab-mosafir" role="tabpanel">
                <div class="p-3">
                    @include('orders.partials._mosafir')
                </div>
            </div>
            @endif

            {{-- Tab: Logs --}}
            <div class="tab-pane fade" id="tab-logs" role="tabpanel">
                <div class="p-3">
                    @include('orders.partials._logs')
                </div>
            </div>

        </div>
    </div>
</div>

@if($order->status === 'pending')
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('marketer.orders.cancel', $order) }}">
                @csrf @method('DELETE')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger" style="font-size:1rem">
                        <i class="ti ti-ban me-1"></i> إلغاء الطلب #{{ $order->id }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted" style="font-size:.9rem">هل أنت متأكدة من إلغاء هذا الطلب؟ لا يمكن التراجع عن هذا الإجراء.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">رجوع</button>
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="ti ti-ban me-1"></i> تأكيد الإلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
