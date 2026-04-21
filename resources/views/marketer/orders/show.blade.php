@extends('layouts.marketer')

@section('title', 'طلب #{{ $order->id }}')

@use('Illuminate\Support\Facades\Storage')

@section('content')

<h2 class="fw-bold mb-3" style="color:#4a2619;font-size:1.1rem">طلب #{{ $order->id }}</h2>

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
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">إجمالي المنتجات</span>
                        <span>{{ number_format($order->products_total) }} د.ل</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">رسوم التوصيل</span>
                        <span>{{ number_format($order->delivery_cost) }} د.ل</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fw-bold">
                        <span>الإجمالي الكلي</span>
                        <span style="color:#4a2619">{{ number_format($order->grand_total) }} د.ل</span>
                    </div>
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

@endsection
