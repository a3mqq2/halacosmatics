@extends('layouts.marketer')

@section('title', 'طلباتي')

@push('styles')
<style>
.filter-card {
    border-radius: 16px;
    border: 1.5px solid #f0ebe8;
    background: #fdfaf9;
    overflow: hidden;
}
.filter-toggle-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .85rem 1.1rem;
    background: none;
    border: none;
    font-family: inherit;
    font-size: .88rem;
    font-weight: 700;
    color: #4a2619;
    cursor: pointer;
}
.filter-toggle-btn i.arrow {
    transition: transform .2s;
}
.filter-toggle-btn[aria-expanded="true"] i.arrow {
    transform: rotate(180deg);
}
.filter-body {
    padding: 0 1.1rem 1rem;
    display: flex;
    flex-direction: column;
    gap: .75rem;
}
.filter-label {
    font-size: .75rem;
    font-weight: 700;
    color: #9ca3af;
    margin-bottom: .2rem;
}
.filter-input {
    width: 100%;
    border: 1.5px solid #e8e0dc;
    border-radius: 10px;
    padding: .5rem .75rem;
    font-family: inherit;
    font-size: .88rem;
    color: #111;
    background: #fff;
    outline: none;
    transition: border-color .15s;
}
.filter-input:focus { border-color: #4a2619; }
.status-chips {
    display: flex;
    flex-wrap: wrap;
    gap: .4rem;
}
.status-chip {
    padding: .3rem .75rem;
    border-radius: 20px;
    border: 1.5px solid #e8e0dc;
    background: #fff;
    font-size: .75rem;
    font-weight: 700;
    color: #6b7280;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
}
.status-chip.active,
.status-chip:hover { border-color: #4a2619; color: #4a2619; background: #fdf6f3; }
.status-chip.active { background: #4a2619; color: #fff; border-color: #4a2619; }
.order-card {
    border-radius: 18px;
    border: 1.5px solid #f0ebe8;
    background: #fff;
    box-shadow: 0 2px 10px rgba(74,38,25,.06);
    overflow: hidden;
    transition: box-shadow .2s;
}
.order-card:hover {
    box-shadow: 0 4px 18px rgba(74,38,25,.12);
}
.order-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .95rem 1.1rem .75rem;
    border-bottom: 1px solid #f5f0ee;
}
.order-id {
    font-size: 1rem;
    font-weight: 800;
    color: #4a2619;
    letter-spacing: .03em;
}
.order-card-body {
    padding: .85rem 1.1rem;
    display: flex;
    flex-direction: column;
    gap: .6rem;
}
.order-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: .88rem;
}
.order-info-label {
    color: #9ca3af;
    font-weight: 600;
    font-size: .8rem;
}
.order-info-value {
    font-weight: 700;
    color: #111;
}
.order-card-footer {
    padding: .75rem 1.1rem;
    border-top: 1px solid #f5f0ee;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
}
.order-total {
    font-size: 1.15rem;
    font-weight: 800;
    color: #4a2619;
    line-height: 1;
}
.order-total small {
    font-size: .75rem;
    font-weight: 600;
    color: #aaa;
}
.order-items-pill {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    background: #f5ede9;
    color: #4a2619;
    font-size: .75rem;
    font-weight: 700;
    padding: .2rem .55rem;
    border-radius: 20px;
}
</style>
@endpush

@section('content')

<h2 class="page-title">طلباتي</h2>

{{-- Filter --}}
@php
    $statuses = [
        'pending'    => 'قيد الموافقة',
        'processing' => 'قيد التجهيز',
        'with_agent' => 'مع المندوب',
        'delivered'  => 'تم التسليم',
        'returning'  => 'قيد الاسترداد',
        'returned'   => 'مسترد',
    ];
    $hasFilter = array_filter($filters);
@endphp

<div class="filter-card mb-4">
    <button class="filter-toggle-btn" type="button"
            data-bs-toggle="collapse" data-bs-target="#filterBody"
            aria-expanded="{{ $hasFilter ? 'true' : 'false' }}">
        <span>
            <i class="ti ti-filter me-1"></i> البحث والفلترة
            @if($hasFilter)
                <span style="font-size:.7rem;background:#4a2619;color:#fff;border-radius:20px;padding:.1rem .45rem;margin-right:.4rem">مفعّل</span>
            @endif
        </span>
        <i class="ti ti-chevron-down arrow"></i>
    </button>

    <div class="collapse {{ $hasFilter ? 'show' : '' }}" id="filterBody">
        <form method="GET" action="{{ route('marketer.orders.index') }}">
            <div class="filter-body">

                {{-- الحالة --}}
                <div>
                    <div class="filter-label">الحالة</div>
                    <div class="status-chips">
                        <a href="{{ route('marketer.orders.index', array_merge(request()->except(['status','page']), [])) }}"
                           class="status-chip {{ !($filters['status'] ?? null) ? 'active' : '' }}">الكل</a>
                        @foreach($statuses as $val => $lbl)
                            <a href="{{ route('marketer.orders.index', array_merge(request()->except(['page']), ['status' => $val])) }}"
                               class="status-chip {{ ($filters['status'] ?? null) === $val ? 'active' : '' }}">{{ $lbl }}</a>
                        @endforeach
                    </div>
                </div>

                {{-- اسم الزبون --}}
                <div>
                    <div class="filter-label">اسم الزبون</div>
                    <input type="text" name="customer_name" class="filter-input"
                           placeholder="ابحث باسم الزبون..."
                           value="{{ $filters['customer_name'] ?? '' }}">
                </div>

                {{-- رقم الهاتف --}}
                <div>
                    <div class="filter-label">رقم الهاتف</div>
                    <input type="text" name="customer_phone" class="filter-input"
                           placeholder="ابحث برقم الهاتف..."
                           value="{{ $filters['customer_phone'] ?? '' }}"
                           inputmode="numeric" dir="ltr">
                </div>

                {{-- رقم شحنة المسافر --}}
                <div>
                    <div class="filter-label">رقم شحنة المسافر</div>
                    <input type="text" name="mosafir_parcel_id" class="filter-input"
                           placeholder="ابحث برقم الشحنة..."
                           value="{{ $filters['mosafir_parcel_id'] ?? '' }}"
                           dir="ltr">
                </div>

                {{-- المدينة --}}
                <div>
                    <div class="filter-label">المدينة</div>
                    <input type="text" name="city_name" class="filter-input"
                           placeholder="ابحث باسم المدينة أو المنطقة..."
                           value="{{ $filters['city_name'] ?? '' }}">
                </div>

                {{-- من تاريخ — إلى تاريخ --}}
                <div class="d-flex gap-2">
                    <div style="flex:1">
                        <div class="filter-label">من تاريخ</div>
                        <input type="date" name="date_from" class="filter-input"
                               value="{{ $filters['date_from'] ?? '' }}">
                    </div>
                    <div style="flex:1">
                        <div class="filter-label">إلى تاريخ</div>
                        <input type="date" name="date_to" class="filter-input"
                               value="{{ $filters['date_to'] ?? '' }}">
                    </div>
                </div>

                {{-- أزرار --}}
                <div class="d-flex gap-2 pt-1">
                    <button type="submit" class="btn btn-primary btn-sm flex-1" style="flex:1">
                        <i class="ti ti-search me-1"></i> بحث
                    </button>
                    <a href="{{ route('marketer.orders.index') }}" class="btn btn-outline-secondary btn-sm flex-1" style="flex:1">
                        <i class="ti ti-x me-1"></i> مسح
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

@if($orders->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="ti ti-clipboard-list" style="font-size:3rem;display:block;margin-bottom:.75rem;opacity:.35"></i>
        <div class="fw-semibold mb-1">لا توجد طلبات بعد</div>
        <div class="small mb-3">ابدئي بتصفح المنتجات وإضافة طلبك الأول</div>
        <a href="{{ route('marketer.products') }}" class="btn btn-primary btn-sm px-4">
            <i class="ti ti-shopping-bag me-1"></i> تصفح المنتجات
        </a>
    </div>
@else
    <div class="d-flex flex-column gap-3 mb-4">
        @foreach($orders as $order)
        <div class="order-card">

            <div class="order-card-header">
                <span class="order-id"># {{ $order->id }}</span>
                <span class="badge bg-{{ $order->status_color }}-subtle text-{{ $order->status_color }} border border-{{ $order->status_color }}-subtle px-2 py-1" style="font-size:.78rem">
                    {{ $order->status_label }}
                </span>
            </div>

            <div class="order-card-body">

                <div class="order-info-row">
                    <span class="order-info-label">الزبون</span>
                    <span class="order-info-value">{{ $order->customer_name }}</span>
                </div>

                <div class="order-info-row">
                    <span class="order-info-label">الهاتف</span>
                    <span class="order-info-value" dir="ltr">{{ $order->customer_phone }}</span>
                </div>

                <div class="order-info-row">
                    <span class="order-info-label">المدينة</span>
                    <span class="order-info-value">{{ $order->sub_city_name ?? $order->city_name }}</span>
                </div>

                <div class="order-info-row">
                    <span class="order-info-label">التاريخ</span>
                    <span class="order-info-value" style="font-size:.82rem;color:#6b7280">{{ dt($order->created_at) }}</span>
                </div>

            </div>

            <div class="order-card-footer">
                <div>
                    <div style="font-size:.7rem;color:#aaa;font-weight:600;margin-bottom:.15rem">الإجمالي</div>
                    <div class="order-total">
                        {{ number_format($order->grand_total) }}
                        <small>د.ل</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="order-items-pill">
                        <i class="ti ti-box" style="font-size:.85rem"></i>
                        {{ $order->items->count() }} صنف
                    </span>
                    <a href="{{ route('marketer.orders.show', $order) }}" class="btn btn-primary btn-sm px-3">
                        عرض <i class="ti ti-arrow-left ms-1"></i>
                    </a>
                </div>
            </div>

        </div>
        @endforeach
    </div>

    {{ $orders->links() }}
@endif

@endsection
