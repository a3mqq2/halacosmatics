@extends('layouts.app')

@section('title', 'الطلبات')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">الطلبات</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0">الطلبات</h4>
    <span class="badge bg-secondary fs-6">{{ $orders->total() }} طلب</span>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">

            <div class="col-12 col-sm-4">
                <input type="text" name="filter[customer_name]"
                       class="form-control"
                       placeholder="بحث باسم الزبون..."
                       value="{{ request('filter.customer_name') }}">
            </div>

            <div class="col-12 col-sm-3">
                @php
                    $statusLabels = [
                        'pending'    => 'قيد الموافقة',
                        'processing' => 'قيد التجهيز',
                        'with_agent' => 'بحوزة المندوب',
                        'delivered'  => 'تم التسليم',
                        'returning'  => 'قيد الاسترداد',
                        'returned'   => 'مسترد',
                        'rejected'   => 'مرفوض',
                    ];
                @endphp
                <select name="filter[status]" class="form-select" id="statusFilter" onchange="toggleDeliveryFilter(this.value)">
                    <option value="">كل الحالات</option>
                    @foreach($allowedStatuses as $statusKey)
                        <option value="{{ $statusKey }}" {{ request('filter.status') === $statusKey ? 'selected' : '' }}>
                            {{ $statusLabels[$statusKey] ?? $statusKey }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Delivery Type Filter — visible only when status = with_agent --}}
            <div class="col-12 col-sm-auto" id="deliveryTypeWrap"
                 style="{{ request('filter.status') === 'with_agent' ? '' : 'display:none' }}">
                <div class="d-flex gap-2">

                    <a href="{{ route('orders.index', array_merge(request()->except(['filter[delivery_type]', 'page']), ['filter[status]' => 'with_agent'])) }}"
                       class="delivery-filter-btn {{ request('filter.status') === 'with_agent' && !request('filter.delivery_type') ? 'active' : '' }}">
                        <i class="ti ti-list-check"></i>
                        <span>الكل</span>
                    </a>

                    <a href="{{ route('orders.index', array_merge(request()->except(['filter[delivery_type]', 'page']), ['filter[status]' => 'with_agent', 'filter[delivery_type]' => 'mosafir'])) }}"
                       class="delivery-filter-btn {{ request('filter.delivery_type') === 'mosafir' ? 'active' : '' }}">
                        <img src="{{ asset('mosafer.svg') }}" alt="المسافر" style="height:18px;object-fit:contain">
                        <span>المسافر</span>
                    </a>

                    <a href="{{ route('orders.index', array_merge(request()->except(['filter[delivery_type]', 'page']), ['filter[status]' => 'with_agent', 'filter[delivery_type]' => 'agent'])) }}"
                       class="delivery-filter-btn {{ request('filter.delivery_type') === 'agent' ? 'active' : '' }}">
                        <i class="ti ti-motorbike"></i>
                        <span>توصيل داخلي</span>
                    </a>

                </div>
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-search me-1"></i> بحث
                </button>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary ms-1">
                    <i class="ti ti-x"></i>
                </a>
            </div>

        </form>
    </div>
</div>

@push('styles')
<style>
.delivery-filter-btn {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .38rem .85rem;
    border-radius: 20px;
    border: 1.5px solid #e0d6d0;
    background: #fff;
    color: #555;
    font-size: .82rem;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    transition: all .15s;
}
.delivery-filter-btn:hover {
    border-color: #4a2619;
    color: #4a2619;
}
.delivery-filter-btn.active {
    background: #4a2619;
    border-color: #4a2619;
    color: #fff;
}
.delivery-filter-btn.active img {
    filter: brightness(0) invert(1);
}
</style>
@endpush

@push('scripts')
<script>
function toggleDeliveryFilter(status) {
    document.getElementById('deliveryTypeWrap').style.display = status === 'with_agent' ? '' : 'none';
}
</script>
@endpush

@if($orders->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="ti ti-clipboard-list fs-1 d-block mb-2"></i>
        لا توجد طلبات
    </div>
@else

    {{-- Desktop Table --}}
    <div class="d-none d-md-block">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>المسوقة</th>
                            <th>الزبون</th>
                            <th>الهاتف</th>
                            <th>المدينة</th>
                            <th class="text-center">المنتجات</th>
                            <th class="text-center">الإجمالي</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">التاريخ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td class="text-muted small">{{ $order->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $order->marketer->first_name }} {{ $order->marketer->last_name }}</div>
                                <div class="text-muted small" dir="ltr">{{ $order->marketer->phone }}</div>
                            </td>
                            <td class="fw-semibold">{{ $order->customer_name }}</td>
                            <td dir="ltr" class="small">{{ $order->customer_phone }}</td>
                            <td class="small">
                                {{ $order->city_name }}
                                @if($order->sub_city_name)
                                    <div class="text-muted">{{ $order->sub_city_name }}</div>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $order->items_count ?? $order->items->count() }} صنف</span>
                            </td>
                            <td class="text-center fw-semibold" style="color:#4a2619">
                                {{ number_format($order->grand_total) }} د.ل
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $order->status_color }}-subtle text-{{ $order->status_color }} border border-{{ $order->status_color }}-subtle">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="text-center text-muted small">
                                {{ dt($order->created_at) }}
                            </td>
                            <td>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="d-md-none">
        <div class="d-flex flex-column gap-3">
            @foreach($orders as $order)
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">

                    {{-- Header: order id + status --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size:1rem;color:#4a2619"># {{ $order->id }}</span>
                        <span class="badge fs-6 bg-{{ $order->status_color }}-subtle text-{{ $order->status_color }} border border-{{ $order->status_color }}-subtle px-2 py-1">
                            {{ $order->status_label }}
                        </span>
                    </div>
                    {{-- Name + phone --}}
                    <div class="mb-3">
                        <div class="fw-bold fs-6 mb-1">{{ $order->customer_name }}</div>
                        <div class="text-muted" style="font-size:.9rem">{{ $order->customer_phone }}</div>
                    </div>

                    {{-- Info rows --}}
                    <div class="d-flex flex-column gap-2 mb-3">

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-semibold" style="font-size:.82rem">المسوقة</span>
                            <span class="fw-semibold" style="font-size:.9rem">{{ $order->marketer->first_name }} {{ $order->marketer->last_name }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-semibold" style="font-size:.82rem">المدينة</span>
                            <span style="font-size:.9rem">{{ $order->sub_city_name ?? $order->city_name }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-semibold" style="font-size:.82rem">المنتجات</span>
                            <span class="badge bg-light text-dark border" style="font-size:.82rem">{{ $order->items_count ?? $order->items->count() }} صنف</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-semibold" style="font-size:.82rem">التاريخ</span>
                            <span style="font-size:.85rem">{{ dt($order->created_at) }}</span>
                        </div>

                    </div>

                    {{-- Total + Button --}}
                    <div class="d-flex align-items-center justify-content-between gap-2 pt-2 border-top">
                        <div>
                            <span class="text-muted fw-semibold" style="font-size:.8rem">الإجمالي</span>
                            <div class="fw-bold fs-5" style="color:#4a2619; line-height:1.1">
                                {{ number_format($order->grand_total) }} <span class="fs-6">د.ل</span>
                            </div>
                        </div>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary btn-sm px-3">
                            <i class="ti ti-eye me-1"></i> عرض
                        </a>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>

@endif

@endsection
