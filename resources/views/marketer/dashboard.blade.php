@extends('layouts.marketer')

@section('title', 'لوحة المسوق')

@push('styles')
<style>
.log-row { transition: background .15s; }
.log-row:hover { background: #fdf6f3; }
</style>
@endpush

@section('content')

    <div class="marketer-hero mb-4">
        <div class="content">
            <p class="mb-1 opacity-75" style="font-size:.85rem">مرحباً بك في</p>
            <h3 class="fw-bold mb-1">هالة كوزماتكس 💄</h3>
            <p class="opacity-85 mb-0">{{ $marketer->first_name }} {{ $marketer->last_name }}</p>
        </div>
    </div>

 
    @php
        $widgets = [
            ['status' => 'pending',    'label' => 'قيد الموافقة', 'icon' => 'ti-clock',          'color' => '#f59e0b', 'bg' => '#fffbeb'],
            ['status' => 'processing', 'label' => 'قيد التجهيز',  'icon' => 'ti-settings',        'color' => '#3b82f6', 'bg' => '#eff6ff'],
            ['status' => 'with_agent', 'label' => 'مع المندوب',   'icon' => 'ti-motorbike',       'color' => '#06b6d4', 'bg' => '#ecfeff'],
            ['status' => 'delivered',  'label' => 'تم التسليم',   'icon' => 'ti-circle-check',    'color' => '#10b981', 'bg' => '#ecfdf5'],
            ['status' => 'returning',  'label' => 'قيد الاسترداد','icon' => 'ti-arrow-back-up',   'color' => '#f97316', 'bg' => '#fff7ed'],
            ['status' => 'returned',   'label' => 'مسترد',        'icon' => 'ti-package-import',  'color' => '#6b7280', 'bg' => '#f9fafb'],
            ['status' => 'cancelled',  'label' => 'ملغى',         'icon' => 'ti-ban',             'color' => '#ef4444', 'bg' => '#fff1f2'],
        ];
    @endphp

    <div class="row g-3 mb-4">
        @foreach($widgets as $w)
        <div class="col-6">
            <a href="{{ route('marketer.orders.index', ['status' => $w['status']]) }}"
               class="d-block text-decoration-none rounded-4 p-3"
               style="background:{{ $w['bg'] }};border:1.5px solid {{ $w['color'] }}22">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:{{ $w['color'] }}18;display:flex;align-items:center;justify-content:center">
                        <i class="ti {{ $w['icon'] }}" style="font-size:1.15rem;color:{{ $w['color'] }}"></i>
                    </div>
                    <span style="font-size:1.6rem;font-weight:800;color:{{ $w['color'] }};line-height:1">
                        {{ $orderCounts[$w['status']] ?? 0 }}
                    </span>
                </div>
                <div style="font-size:.78rem;font-weight:700;color:#374151">{{ $w['label'] }}</div>
            </a>
        </div>
        @endforeach
    </div>

    @if($recentLogs->isNotEmpty())
    <div class="card border-0 shadow-sm rounded-4 mt-2">
        <div class="card-body pb-0">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0" style="color:#4a2619">
                    <i class="ti ti-history me-1"></i> آخر التحديثات
                </h6>
                <a href="{{ route('marketer.orders.index') }}" class="small text-muted text-decoration-none">
                    كل الطلبات <i class="ti ti-arrow-left" style="font-size:.8rem"></i>
                </a>
            </div>
            <div class="d-flex flex-column">
                @foreach($recentLogs as $log)
                <a href="{{ route('marketer.orders.show', $log->order_id) }}"
                   class="d-flex align-items-center gap-3 text-decoration-none px-1 py-3 rounded-3 log-row {{ ! $loop->last ? 'border-bottom' : '' }}">
                    <div style="width:9px;height:9px;min-width:9px;border-radius:50%;background:#4a2619;flex-shrink:0"></div>
                    <div style="flex:1;min-width:0">
                        <div class="fw-semibold text-truncate" style="font-size:.88rem;color:#111;line-height:1.45">
                            {{ $log->description }}
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span style="font-size:.75rem;color:#4a2619;font-weight:700">طلب #{{ $log->order_id }}</span>
                            <span class="text-muted" style="font-size:.72rem">{{ dt($log->created_at) }}</span>
                        </div>
                    </div>
                    <i class="ti ti-arrow-left text-muted" style="font-size:.85rem;flex-shrink:0"></i>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <p class="text-center text-muted small mt-4">
        للتواصل مع الإدارة يرجى الاتصال عبر واتساب
    </p>

@endsection
