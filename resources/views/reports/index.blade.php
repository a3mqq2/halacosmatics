@extends('layouts.app')

@section('title', 'التقارير')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">التقارير</li>
@endsection

@push('styles')
<style>
    .rpt-filter { border-radius: 14px; border: none; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
    .kpi-card   { border-radius: 14px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,.06); }
    .kpi-icon   { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0; }
    .kpi-value  { font-size:1.5rem;font-weight:800;line-height:1.1; }
    .kpi-label  { font-size:.74rem;font-weight:600;color:#9ca3af; }
    .hala-hero  { border-radius:18px;background:linear-gradient(135deg,#4a2619 0%,#7a3e26 100%);color:#fff;padding:2rem 2.5rem; }
    .hala-hero .hero-val { font-size:2.8rem;font-weight:900;line-height:1; }
    .hala-hero .hero-lbl { font-size:.85rem;opacity:.75;margin-bottom:.25rem; }
    .hala-hero .hero-sub { font-size:.82rem;opacity:.6; }
    .rpt-tabs .nav-link   { font-size:.88rem;font-weight:700;color:#6b7280;border:none;border-bottom:3px solid transparent;padding:.85rem 1.2rem; }
    .rpt-tabs .nav-link.active { color:#4a2619;border-bottom-color:#4a2619;background:transparent; }
    .rpt-tabs .nav-link:hover:not(.active) { color:#4a2619;background:#fdf6f3; }
    .tab-card   { border-radius:0 0 14px 14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,.06); }
    .status-pill { font-size:.7rem;font-weight:700;padding:.2rem .55rem;border-radius:20px;white-space:nowrap; }
    .tbl-sm th, .tbl-sm td { font-size:.82rem;padding:.55rem .75rem; }
    .progress-bar-custom { height:6px;border-radius:99px;background:#f3f4f6;overflow:hidden; }
    .progress-bar-fill   { height:100%;border-radius:99px; }
    .rank-num { width:26px;height:26px;border-radius:50%;font-size:.72rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
    .chip-status { display:inline-flex;align-items:center;padding:.3rem .8rem;border-radius:20px;font-size:.78rem;font-weight:700;cursor:pointer;border:1.5px solid #e5e7eb;color:#6b7280;text-decoration:none;transition:all .15s; }
    .chip-status:hover,.chip-status.active { border-color:#4a2619;color:#4a2619;background:#fdf6f3; }
    .chip-status.active { background:#4a2619;color:#fff;border-color:#4a2619; }
    @media(max-width:576px){
        .hala-hero { padding:1.25rem 1.25rem; }
        .hala-hero .hero-val { font-size:2rem; }
        .rpt-tabs .nav-link { font-size:.78rem;padding:.7rem .75rem; }
    }
</style>
@endpush

@section('content')

{{-- ── Date Filter ─────────────────────────────────────────────── --}}
<div class="card rpt-filter mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('reports.index') }}" id="filterForm" class="row g-2 align-items-end">
            <input type="hidden" name="tab" id="activeTabInput" value="{{ request('tab','orders') }}">
            <input type="hidden" name="order_status" id="orderStatusInput" value="{{ $orderStatusFilter }}">
            <div class="col-12 col-md-auto">
                <span class="fw-bold" style="font-size:.85rem"><i class="ti ti-filter me-1 text-muted"></i>تصفية التقارير</span>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size:.78rem">من تاريخ</label>
                <input type="date" name="from" class="form-control form-control-sm rounded-3" value="{{ $fromStr }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size:.78rem">إلى تاريخ</label>
                <input type="date" name="to" class="form-control form-control-sm rounded-3" value="{{ $toStr }}">
            </div>
            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-4 rounded-3">
                    <i class="ti ti-search me-1"></i> عرض
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">إعادة تعيين</a>
            </div>
            <div class="col-12 col-md-auto ms-auto d-flex gap-1 flex-wrap">
                @foreach([
                    ['label'=>'هذا الشهر',  'from'=>now()->startOfMonth()->toDateString(), 'to'=>now()->toDateString()],
                    ['label'=>'آخر 3 أشهر', 'from'=>now()->subMonths(2)->startOfMonth()->toDateString(), 'to'=>now()->toDateString()],
                    ['label'=>'هذه السنة',  'from'=>now()->startOfYear()->toDateString(), 'to'=>now()->toDateString()],
                ] as $pre)
                <a href="{{ route('reports.index', ['from'=>$pre['from'],'to'=>$pre['to'],'tab'=>request('tab','orders')]) }}"
                   class="btn btn-sm rounded-3 btn-outline-secondary" style="font-size:.75rem">{{ $pre['label'] }}</a>
                @endforeach
            </div>
        </form>
    </div>
</div>

{{-- ── Global KPI Row ──────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    @php
    $kpis = [
        ['label'=>'إجمالي مبيعات المسوقات', 'value'=>number_format($totalCustomerSales).' د.ل', 'icon'=>'ti-trending-up',   'bg'=>'bg-primary-subtle',  'color'=>'text-primary'],
        ['label'=>'ربح هالة كوزماتكس',      'value'=>number_format($halaProfit).' د.ل',        'icon'=>'ti-coin',           'bg'=>'bg-success-subtle',  'color'=>'text-success'],
        ['label'=>'ربح المسوقات الإجمالي',   'value'=>number_format($totalMarketerProfit).' د.ل','icon'=>'ti-award',          'bg'=>'bg-warning-subtle',  'color'=>'text-warning'],
        ['label'=>'طلبات مسلمة',             'value'=>number_format($totalDelivered),            'icon'=>'ti-circle-check',   'bg'=>'bg-success-subtle',  'color'=>'text-success'],
        ['label'=>'طلبات مستردة',            'value'=>number_format($totalReturned).' ('.$returnRate.'%)', 'icon'=>'ti-arrow-back','bg'=>'bg-danger-subtle','color'=>'text-danger'],
        ['label'=>'إجمالي الطلبات',          'value'=>number_format($totalAll),                  'icon'=>'ti-clipboard-list', 'bg'=>'bg-info-subtle',     'color'=>'text-info'],
    ];
    @endphp
    @foreach($kpis as $k)
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="kpi-icon {{ $k['bg'] }} {{ $k['color'] }}">
                    <i class="ti {{ $k['icon'] }}"></i>
                </div>
                <div>
                    <div class="kpi-label">{{ $k['label'] }}</div>
                    <div class="kpi-value {{ $k['color'] }}">{{ $k['value'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Tabs ────────────────────────────────────────────────────── --}}
<div class="card tab-card">
    <div class="card-header p-0 border-bottom bg-white" style="border-radius:14px 14px 0 0">
        <ul class="nav rpt-tabs overflow-auto flex-nowrap" id="reportTabs" role="tablist">
            @foreach([
                ['id'=>'orders',    'icon'=>'ti-clipboard-list', 'label'=>'الطلبات'],
                ['id'=>'products',  'icon'=>'ti-box',            'label'=>'المنتجات'],
                ['id'=>'marketers', 'icon'=>'ti-users',          'label'=>'ربح المسوقات'],
                ['id'=>'hala',      'icon'=>'ti-building-store', 'label'=>'ربح هالة'],
                ['id'=>'purchases', 'icon'=>'ti-receipt',        'label'=>'تقرير المشتريات'],
            ] as $tab)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ request('tab','orders') === $tab['id'] ? 'active' : '' }}"
                        id="tab-{{ $tab['id'] }}-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-{{ $tab['id'] }}"
                        data-tab="{{ $tab['id'] }}" type="button" role="tab">
                    <i class="ti {{ $tab['icon'] }} me-1"></i>{{ $tab['label'] }}
                </button>
            </li>
            @endforeach
        </ul>
    </div>

    <div class="tab-content" id="reportTabsContent">

        {{-- ══════════════════════════════════════════════════════════
             TAB 1 — الطلبات
        ══════════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade {{ request('tab','orders') === 'orders' ? 'show active' : '' }}"
             id="tab-orders" role="tabpanel">
            <div class="p-3 p-md-4">

                {{-- Status distribution + return rate charts --}}
                <div class="row g-3 mb-4">
                    <div class="col-12 col-lg-5">
                        <div class="card border shadow-none h-100">
                            <div class="card-body">
                                <div class="fw-bold mb-1" style="font-size:.9rem"><i class="ti ti-chart-donut me-1 text-info"></i>توزيع الطلبات حسب الحالة</div>
                                <div class="text-muted small mb-2">الفترة المحددة — {{ number_format($totalAll) }} طلب إجمالي</div>
                                <div id="chart-status" style="min-height:220px"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-7">
                        <div class="card border shadow-none h-100">
                            <div class="card-body">
                                <div class="fw-bold mb-1" style="font-size:.9rem"><i class="ti ti-chart-bar me-1 text-warning"></i>مؤشرات التسليم والاسترداد</div>
                                <div class="text-muted small mb-2">مقارنة الطلبات المسلمة والمستردة والمرفوضة</div>
                                <div id="chart-delivery" style="min-height:220px"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick stats strip --}}
                <div class="row g-2 mb-3">
                    @php
                    $strips = [
                        ['label'=>'نسبة التسليم', 'value'=>$deliveryRate.'%', 'color'=>'#22c55e'],
                        ['label'=>'نسبة الاسترداد','value'=>$returnRate.'%', 'color'=>'#f59e0b'],
                        ['label'=>'متوسط قيمة الطلب','value'=>($totalDelivered>0?number_format($totalCustomerSales/$totalDelivered,0):0).' د.ل','color'=>'#4f46e5'],
                        ['label'=>'إجمالي رسوم التوصيل','value'=>number_format($totalDeliveryFees).' د.ل','color'=>'#06b6d4'],
                    ];
                    @endphp
                    @foreach($strips as $s)
                    <div class="col-6 col-md-3">
                        <div class="rounded-3 p-3 text-center" style="background:#f8f9fa;border:1.5px solid #f0f0f0">
                            <div style="font-size:1.35rem;font-weight:800;color:{{ $s['color'] }}">{{ $s['value'] }}</div>
                            <div class="text-muted" style="font-size:.73rem;font-weight:600">{{ $s['label'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Status filter chips --}}
                <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
                    <span class="text-muted small fw-semibold">فلترة:</span>
                    <a href="#" class="chip-status {{ !$orderStatusFilter ? 'active' : '' }}" onclick="filterOrders('')">الكل ({{ $totalAll }})</a>
                    @php
                    $statusMeta = [
                        'pending'    => ['label'=>'قيد الموافقة', 'color'=>'#a855f7'],
                        'processing' => ['label'=>'قيد التجهيز',  'color'=>'#3b82f6'],
                        'with_agent' => ['label'=>'مع المندوب',   'color'=>'#06b6d4'],
                        'delivered'  => ['label'=>'مسلمة',        'color'=>'#22c55e'],
                        'returning'  => ['label'=>'قيد الاسترداد','color'=>'#f97316'],
                        'returned'   => ['label'=>'مستردة',        'color'=>'#f59e0b'],
                        'rejected'   => ['label'=>'مرفوضة',        'color'=>'#ef4444'],
                    ];
                    @endphp
                    @foreach($statusMeta as $val => $meta)
                    @if(($statusCounts[$val] ?? 0) > 0)
                    <a href="#" class="chip-status {{ $orderStatusFilter === $val ? 'active' : '' }}"
                       onclick="filterOrders('{{ $val }}')">
                        {{ $meta['label'] }} ({{ $statusCounts[$val] ?? 0 }})
                    </a>
                    @endif
                    @endforeach
                </div>

                @if($ordersTotal > 400)
                <div class="alert alert-info py-2 small mb-3">
                    <i class="ti ti-info-circle me-1"></i>
                    يوجد {{ number_format($ordersTotal) }} طلب — يعرض النظام أحدث 400. ضيّق نطاق التاريخ لعرض جميع الطلبات.
                </div>
                @endif

                {{-- Orders Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle tbl-sm mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width:60px">#</th>
                                <th>الحالة</th>
                                <th>المسوقة</th>
                                <th>الزبون</th>
                                <th class="d-none d-md-table-cell">المدينة</th>
                                <th class="text-center d-none d-sm-table-cell">أصناف</th>
                                <th class="text-end">المنتجات</th>
                                <th class="text-end d-none d-lg-table-cell">توصيل</th>
                                <th class="text-end">الإجمالي</th>
                                <th class="d-none d-xl-table-cell">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            @php
                            $sc = ['pending'=>['قيد الموافقة','warning'],'processing'=>['قيد التجهيز','primary'],'with_agent'=>['مع المندوب','info'],'delivered'=>['مسلمة','success'],'returning'=>['قيد الاسترداد','orange'],'returned'=>['مستردة','secondary'],'rejected'=>['مرفوضة','danger']];
                            [$slabel,$scolor] = $sc[$order->status] ?? [$order->status,'secondary'];
                            @endphp
                            <tr>
                                <td class="text-muted fw-semibold">#{{ $order->id }}</td>
                                <td>
                                    <span class="status-pill bg-{{ $scolor }}-subtle text-{{ $scolor }} border border-{{ $scolor }}-subtle">
                                        {{ $slabel }}
                                    </span>
                                </td>
                                <td class="fw-semibold" style="max-width:130px">
                                    <span class="d-inline-block text-truncate" style="max-width:120px">
                                        {{ $order->marketer?->first_name }} {{ $order->marketer?->last_name }}
                                    </span>
                                </td>
                                <td style="max-width:120px">
                                    <span class="d-inline-block text-truncate" style="max-width:110px">{{ $order->customer_name }}</span>
                                </td>
                                <td class="text-muted d-none d-md-table-cell">{{ $order->sub_city_name ?? $order->city_name }}</td>
                                <td class="text-center d-none d-sm-table-cell">{{ $order->items_count }}</td>
                                <td class="text-end fw-semibold">{{ number_format($order->products_total) }}</td>
                                <td class="text-end text-muted d-none d-lg-table-cell">{{ number_format($order->delivery_cost) }}</td>
                                <td class="text-end fw-bold" style="color:#4a2619">{{ number_format($order->grand_total) }}</td>
                                <td class="text-muted d-none d-xl-table-cell" style="white-space:nowrap;font-size:.78rem">{{ dt($order->created_at) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="ti ti-clipboard-list d-block fs-3 mb-2 opacity-25"></i>
                                    لا توجد طلبات في هذه الفترة
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($orders->isNotEmpty())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="6" class="text-muted" style="font-size:.8rem">{{ $orders->count() }} طلب معروض</td>
                                <td class="text-end text-primary">{{ number_format($orders->sum('products_total')) }}</td>
                                <td class="text-end text-muted d-none d-lg-table-cell">{{ number_format($orders->sum('delivery_cost')) }}</td>
                                <td class="text-end" style="color:#4a2619">{{ number_format($orders->sum('grand_total')) }}</td>
                                <td class="d-none d-xl-table-cell"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             TAB 2 — المنتجات
        ══════════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade {{ request('tab') === 'products' ? 'show active' : '' }}"
             id="tab-products" role="tabpanel">
            <div class="p-3 p-md-4">

                <div class="row g-3 mb-4">
                    <div class="col-12 col-lg-7">
                        <div class="card border shadow-none h-100">
                            <div class="card-body">
                                <div class="fw-bold mb-1" style="font-size:.9rem"><i class="ti ti-chart-bar me-1 text-primary"></i>أعلى المنتجات إيراداً</div>
                                <div id="chart-top-products" style="min-height:280px"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="card border shadow-none h-100">
                            <div class="card-body">
                                <div class="fw-bold mb-1" style="font-size:.9rem"><i class="ti ti-chart-donut me-1 text-warning"></i>نسب المنتجات من الإجمالي</div>
                                <div id="chart-products-pie" style="min-height:280px"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle tbl-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:36px">#</th>
                                <th>المنتج</th>
                                <th class="text-center">الكمية المباعة</th>
                                <th class="text-end">إجمالي الإيراد</th>
                                <th class="text-end d-none d-md-table-cell">ما دفعته المسوقات</th>
                                <th class="text-end d-none d-lg-table-cell">ربح المسوقات</th>
                                <th style="width:160px">النسبة من الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $i => $p)
                            @php $pct = $totalProductsRevenue > 0 ? round($p->total_revenue / $totalProductsRevenue * 100, 1) : 0; @endphp
                            <tr>
                                <td>
                                    <span class="rank-num {{ $i===0?'bg-warning-subtle text-warning':($i===1?'bg-secondary-subtle text-secondary':($i===2?'bg-danger-subtle text-danger':'bg-light text-muted')) }}">
                                        {{ $i+1 }}
                                    </span>
                                </td>
                                <td class="fw-semibold">{{ $p->name }}</td>
                                <td class="text-center">{{ number_format($p->total_qty) }}</td>
                                <td class="text-end fw-bold text-primary">{{ number_format($p->total_revenue) }} <span class="text-muted fw-normal" style="font-size:.75rem">د.ل</span></td>
                                <td class="text-end d-none d-md-table-cell">{{ number_format($p->hala_revenue) }} <span class="text-muted" style="font-size:.75rem">د.ل</span></td>
                                <td class="text-end d-none d-lg-table-cell text-success fw-semibold">{{ number_format($p->marketer_profit) }} <span class="text-muted fw-normal" style="font-size:.75rem">د.ل</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress-bar-custom flex-grow-1">
                                            <div class="progress-bar-fill bg-primary" style="width:{{ $pct }}%"></div>
                                        </div>
                                        <span style="font-size:.75rem;font-weight:700;min-width:35px">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-5">لا توجد بيانات مبيعات في هذه الفترة</td></tr>
                            @endforelse
                        </tbody>
                        @if($topProducts->isNotEmpty())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3" class="text-muted small">الإجمالي</td>
                                <td class="text-end text-primary">{{ number_format($topProducts->sum('total_revenue')) }} <span class="fw-normal text-muted" style="font-size:.75rem">د.ل</span></td>
                                <td class="text-end d-none d-md-table-cell">{{ number_format($topProducts->sum('hala_revenue')) }} <span class="fw-normal text-muted" style="font-size:.75rem">د.ل</span></td>
                                <td class="text-end d-none d-lg-table-cell text-success">{{ number_format($topProducts->sum('marketer_profit')) }} <span class="fw-normal text-muted" style="font-size:.75rem">د.ل</span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             TAB 3 — ربح المسوقات
        ══════════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade {{ request('tab') === 'marketers' ? 'show active' : '' }}"
             id="tab-marketers" role="tabpanel">
            <div class="p-3 p-md-4">

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <div class="rounded-3 p-3 text-center" style="background:#fdf6f3;border:1.5px solid #f0ebe8">
                            <div style="font-size:1.6rem;font-weight:900;color:#4a2619">{{ number_format($totalMarketerProfit) }} <span style="font-size:.9rem">د.ل</span></div>
                            <div class="text-muted small fw-semibold mt-1">إجمالي أرباح المسوقات</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="rounded-3 p-3 text-center" style="background:#f0fdf4;border:1.5px solid #d1fae5">
                            <div style="font-size:1.6rem;font-weight:900;color:#16a34a">{{ $marketerProfits->count() }}</div>
                            <div class="text-muted small fw-semibold mt-1">عدد المسوقات النشطات</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="rounded-3 p-3 text-center" style="background:#f0f9ff;border:1.5px solid #bae6fd">
                            <div style="font-size:1.6rem;font-weight:900;color:#0284c7">
                                {{ $marketerProfits->count() > 0 ? number_format($totalMarketerProfit / $marketerProfits->count()) : 0 }} <span style="font-size:.9rem">د.ل</span>
                            </div>
                            <div class="text-muted small fw-semibold mt-1">متوسط ربح المسوقة</div>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-none mb-4">
                    <div class="card-body">
                        <div class="fw-bold mb-2" style="font-size:.9rem"><i class="ti ti-chart-bar me-1 text-warning"></i>ترتيب المسوقات حسب الربح</div>
                        <div id="chart-marketer-profit" style="min-height:300px"></div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle tbl-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:36px">#</th>
                                <th>المسوقة</th>
                                <th class="text-center">الطلبات</th>
                                <th class="text-center d-none d-sm-table-cell">الأصناف</th>
                                <th class="text-end">إجمالي مبيعاتها</th>
                                <th class="text-end d-none d-md-table-cell">ما دفعته لهالة</th>
                                <th class="text-end">ربح المسوقة</th>
                                <th style="width:130px" class="d-none d-lg-table-cell">نسبة الربح</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $maxMProfit = (float)($marketerProfits->first()?->profit ?: 1); @endphp
                            @forelse($marketerProfits as $i => $m)
                            @php $profitPct = $m->total_sold > 0 ? round($m->profit / $m->total_sold * 100, 1) : 0; @endphp
                            <tr>
                                <td>
                                    <span class="rank-num {{ $i===0?'bg-warning-subtle text-warning':($i===1?'bg-secondary-subtle text-secondary':($i===2?'bg-danger-subtle text-danger':'bg-light text-muted')) }}">
                                        {{ $i+1 }}
                                    </span>
                                </td>
                                <td class="fw-semibold">{{ $m->name }}</td>
                                <td class="text-center">{{ $m->orders_count }}</td>
                                <td class="text-center d-none d-sm-table-cell text-muted">{{ number_format($m->total_items) }}</td>
                                <td class="text-end">{{ number_format($m->total_sold) }} <span class="text-muted" style="font-size:.74rem">د.ل</span></td>
                                <td class="text-end d-none d-md-table-cell text-muted">{{ number_format($m->paid_to_hala) }} <span style="font-size:.74rem">د.ل</span></td>
                                <td class="text-end fw-bold text-success">{{ number_format($m->profit) }} <span class="fw-normal text-muted" style="font-size:.74rem">د.ل</span></td>
                                <td class="d-none d-lg-table-cell">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress-bar-custom flex-grow-1">
                                            <div class="progress-bar-fill bg-success" style="width:{{ min(100,$profitPct) }}%"></div>
                                        </div>
                                        <span style="font-size:.74rem;font-weight:700;min-width:35px">{{ $profitPct }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-5">لا توجد بيانات في هذه الفترة</td></tr>
                            @endforelse
                        </tbody>
                        @if($marketerProfits->isNotEmpty())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-muted small">الإجمالي</td>
                                <td class="text-end">{{ number_format($marketerProfits->sum('total_sold')) }} <span class="fw-normal text-muted" style="font-size:.74rem">د.ل</span></td>
                                <td class="text-end d-none d-md-table-cell">{{ number_format($marketerProfits->sum('paid_to_hala')) }} <span class="fw-normal text-muted" style="font-size:.74rem">د.ل</span></td>
                                <td class="text-end text-success">{{ number_format($marketerProfits->sum('profit')) }} <span class="fw-normal text-muted" style="font-size:.74rem">د.ل</span></td>
                                <td class="d-none d-lg-table-cell"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             TAB 4 — ربح هالة كوزماتكس
        ══════════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade {{ request('tab') === 'hala' ? 'show active' : '' }}"
             id="tab-hala" role="tabpanel">
            <div class="p-3 p-md-4">

                {{-- Hero profit card --}}
                <div class="hala-hero mb-4">
                    <div class="row align-items-center g-3">
                        <div class="col-12 col-md-5">
                            <div class="hero-lbl">صافي ربح هالة كوزماتكس</div>
                            <div class="hero-val">{{ number_format($halaProfit) }} <span style="font-size:1.3rem;font-weight:600">د.ل</span></div>
                            <div class="hero-sub mt-2">
                                على {{ number_format($totalDelivered) }} طلبية مسلمة
                                في الفترة {{ $fromStr }} — {{ $toStr }}
                            </div>
                        </div>
                        <div class="col-12 col-md-7">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div style="background:rgba(255,255,255,.12);border-radius:12px;padding:1rem">
                                        <div style="font-size:.75rem;opacity:.7;margin-bottom:.2rem">إيرادات من المسوقات</div>
                                        <div style="font-size:1.35rem;font-weight:800">{{ number_format($totalHalaRevenue) }} <span style="font-size:.85rem;font-weight:500">د.ل</span></div>
                                        <div style="font-size:.72rem;opacity:.55">(سعر البيع للمسوقين × الكميات)</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div style="background:rgba(255,255,255,.12);border-radius:12px;padding:1rem">
                                        <div style="font-size:.75rem;opacity:.7;margin-bottom:.2rem">تكلفة المشتريات من الموردين</div>
                                        <div style="font-size:1.35rem;font-weight:800">{{ number_format($totalSupplierCost) }} <span style="font-size:.85rem;font-weight:500">د.ل</span></div>
                                        <div style="font-size:.72rem;opacity:.55">(سعر المورد × الكميات)</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div style="background:rgba(255,255,255,.12);border-radius:12px;padding:1rem">
                                        <div style="font-size:.75rem;opacity:.7;margin-bottom:.2rem">هامش الربح %</div>
                                        <div style="font-size:1.35rem;font-weight:800">
                                            {{ $totalHalaRevenue > 0 ? number_format($halaProfit / $totalHalaRevenue * 100, 1) : 0 }}%
                                        </div>
                                        <div style="font-size:.72rem;opacity:.55">من إيرادات هالة</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div style="background:rgba(255,255,255,.12);border-radius:12px;padding:1rem">
                                        <div style="font-size:.75rem;opacity:.7;margin-bottom:.2rem">متوسط ربح الطلبية</div>
                                        <div style="font-size:1.35rem;font-weight:800">
                                            {{ $totalDelivered > 0 ? number_format($halaProfit / $totalDelivered, 1) : 0 }} <span style="font-size:.85rem;font-weight:500">د.ل</span>
                                        </div>
                                        <div style="font-size:.72rem;opacity:.55">ربح لكل طلبية مسلمة</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hala profit chart --}}
                <div class="card border shadow-none mb-4">
                    <div class="card-body">
                        <div class="fw-bold mb-1" style="font-size:.9rem"><i class="ti ti-chart-area me-1 text-success"></i>تطور ربح هالة كوزماتكس عبر الزمن</div>
                        <div class="text-muted small mb-2">الإيرادات من المسوقات — تكلفة الموردين — صافي الربح</div>
                        <div id="chart-hala-profit" style="min-height:280px"></div>
                    </div>
                </div>

                {{-- Hala profit by product --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle tbl-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:36px">#</th>
                                <th>المنتج</th>
                                <th class="text-center">الكمية</th>
                                <th class="text-end d-none d-sm-table-cell">سعر المورد/وحدة</th>
                                <th class="text-end d-none d-sm-table-cell">سعر البيع للمسوقين/وحدة</th>
                                <th class="text-end d-none d-md-table-cell">تكلفة الموردين</th>
                                <th class="text-end d-none d-md-table-cell">إيرادات هالة</th>
                                <th class="text-end">ربح هالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($halaProfitByProduct as $i => $p)
                            <tr>
                                <td>
                                    <span class="rank-num {{ $i===0?'bg-warning-subtle text-warning':($i===1?'bg-secondary-subtle text-secondary':($i===2?'bg-danger-subtle text-danger':'bg-light text-muted')) }}">
                                        {{ $i+1 }}
                                    </span>
                                </td>
                                <td class="fw-semibold">{{ $p->name }}</td>
                                <td class="text-center">{{ number_format($p->total_qty) }}</td>
                                <td class="text-end d-none d-sm-table-cell text-muted">{{ number_format($p->avg_supplier_cost) }} <span style="font-size:.74rem">د.ل</span></td>
                                <td class="text-end d-none d-sm-table-cell">{{ number_format($p->avg_hala_price) }} <span class="text-muted" style="font-size:.74rem">د.ل</span></td>
                                <td class="text-end d-none d-md-table-cell text-danger">{{ number_format($p->total_supplier_cost) }} <span class="text-muted fw-normal" style="font-size:.74rem">د.ل</span></td>
                                <td class="text-end d-none d-md-table-cell">{{ number_format($p->total_hala_revenue) }} <span class="text-muted fw-normal" style="font-size:.74rem">د.ل</span></td>
                                <td class="text-end fw-bold text-success">{{ number_format($p->hala_profit) }} <span class="fw-normal text-muted" style="font-size:.74rem">د.ل</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-5">لا توجد بيانات في هذه الفترة</td></tr>
                            @endforelse
                        </tbody>
                        @if($halaProfitByProduct->isNotEmpty())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="5" class="text-muted small">الإجمالي</td>
                                <td class="text-end d-none d-md-table-cell text-danger">{{ number_format($halaProfitByProduct->sum('total_supplier_cost')) }} <span class="fw-normal text-muted" style="font-size:.74rem">د.ل</span></td>
                                <td class="text-end d-none d-md-table-cell">{{ number_format($halaProfitByProduct->sum('total_hala_revenue')) }} <span class="fw-normal text-muted" style="font-size:.74rem">د.ل</span></td>
                                <td class="text-end text-success">{{ number_format($halaProfitByProduct->sum('hala_profit')) }} <span class="fw-normal text-muted" style="font-size:.74rem">د.ل</span></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             TAB 5 — تقرير المشتريات
        ══════════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade {{ request('tab') === 'purchases' ? 'show active' : '' }}"
             id="tab-purchases" role="tabpanel">
            <div class="p-3 p-md-4">

                {{-- Summary strip --}}
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <div class="rounded-3 p-3 text-center" style="background:#fef2f2;border:1.5px solid #fecaca">
                            <div style="font-size:1.6rem;font-weight:900;color:#dc2626">{{ number_format($totalSupplierCost) }} <span style="font-size:.9rem">د.ل</span></div>
                            <div class="text-muted small fw-semibold mt-1">إجمالي تكاليف المشتريات</div>
                            <div style="font-size:.73rem;color:#9ca3af">بناءً على الطلبيات المسلمة فقط</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="rounded-3 p-3 text-center" style="background:#f8f9fa;border:1.5px solid #f0f0f0">
                            <div style="font-size:1.6rem;font-weight:900;color:#374151">{{ $purchaseReport->count() }}</div>
                            <div class="text-muted small fw-semibold mt-1">عدد المنتجات المستهلكة</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="rounded-3 p-3 text-center" style="background:#fdf6f3;border:1.5px solid #f0ebe8">
                            <div style="font-size:1.6rem;font-weight:900;color:#4a2619">
                                {{ $totalDelivered > 0 ? number_format($totalSupplierCost / $totalDelivered, 1) : 0 }} <span style="font-size:.9rem">د.ل</span>
                            </div>
                            <div class="text-muted small fw-semibold mt-1">متوسط تكلفة الطلبية</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-secondary py-2 small mb-3">
                    <i class="ti ti-info-circle me-1"></i>
                    هذا التقرير يعرض التكلفة الفعلية للموردين <strong>للطلبيات المسلمة فقط</strong>.
                    الطلبيات المستردة أو المرفوضة غير مشمولة.
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle tbl-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:36px">#</th>
                                <th>المنتج</th>
                                <th class="text-center">الكمية المستهلكة</th>
                                <th class="text-end">سعر المورد / وحدة</th>
                                <th class="text-end">إجمالي التكلفة</th>
                                <th style="width:140px">النسبة من الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalPC = (float)($purchaseReport->sum('total_supplier_cost') ?: 1); @endphp
                            @forelse($purchaseReport as $i => $p)
                            @php $pPct = round($p->total_supplier_cost / $totalPC * 100, 1); @endphp
                            <tr>
                                <td>
                                    <span class="rank-num {{ $i===0?'bg-warning-subtle text-warning':($i===1?'bg-secondary-subtle text-secondary':($i===2?'bg-danger-subtle text-danger':'bg-light text-muted')) }}">
                                        {{ $i+1 }}
                                    </span>
                                </td>
                                <td class="fw-semibold">{{ $p->name }}</td>
                                <td class="text-center">{{ number_format($p->total_qty) }}</td>
                                <td class="text-end text-muted">{{ number_format($p->avg_supplier_cost) }} <span style="font-size:.74rem">د.ل</span></td>
                                <td class="text-end fw-bold text-danger">{{ number_format($p->total_supplier_cost) }} <span class="fw-normal text-muted" style="font-size:.74rem">د.ل</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress-bar-custom flex-grow-1">
                                            <div class="progress-bar-fill bg-danger" style="width:{{ $pPct }}%"></div>
                                        </div>
                                        <span style="font-size:.74rem;font-weight:700;min-width:35px">{{ $pPct }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-5">لا توجد بيانات في هذه الفترة</td></tr>
                            @endforelse
                        </tbody>
                        @if($purchaseReport->isNotEmpty())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-muted small">الإجمالي</td>
                                <td class="text-end text-danger">{{ number_format($purchaseReport->sum('total_supplier_cost')) }} <span class="fw-normal text-muted" style="font-size:.74rem">د.ل</span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

            </div>
        </div>

    </div>{{-- /tab-content --}}
</div>{{-- /tab-card --}}

@endsection

@push('scripts')
<script>
const labels           = @json($labels);
const salesData        = @json($salesData);
const halaRevenueData  = @json($halaRevenueData);
const halaSupData      = @json($halaSupplierCostData);
const halaProfitData   = @json($halaProfitData);
const mktProfitData    = @json($marketerProfitData);

const sc = @json($statusCounts);
const statusMap = {
    delivered:'مسلمة', returned:'مستردة', rejected:'مرفوضة',
    pending:'قيد الموافقة', processing:'قيد التجهيز',
    with_agent:'مع المندوب', returning:'قيد الاسترداد',
};
const statusColors = {
    delivered:'#22c55e', returned:'#f59e0b', rejected:'#ef4444',
    pending:'#a855f7', processing:'#3b82f6', with_agent:'#06b6d4', returning:'#f97316',
};

const font  = { fontFamily: 'Almarai, sans-serif' };
const noBar = { toolbar: { show: false } };
const fmt   = v => v.toLocaleString('ar-LY') + ' د.ل';
const fmtN  = v => Math.round(v).toLocaleString('ar-LY');

const topPrNames = @json($topProducts->pluck('name')->take(10));
const topPrRev   = @json($topProducts->pluck('total_revenue')->take(10));
const topPrPie   = @json($topProducts->take(8)->map(fn($p) => ['name'=>$p->name,'val'=>round((float)$p->total_revenue,2)]));

const mktNames   = @json($marketerProfits->pluck('name')->take(12));
const mktProfit  = @json($marketerProfits->pluck('profit')->take(12)->map(fn($v) => round((float)$v,2)));

let chartsRendered = {};

function renderChart(id, opts) {
    if (chartsRendered[id]) return;
    const el = document.getElementById(id);
    if (!el) return;
    chartsRendered[id] = true;
    new ApexCharts(el, opts).render();
}

function renderTabCharts(tabId) {
    if (tabId === 'tab-orders') {
        renderChart('chart-status', {
            chart: { ...font, ...noBar, type:'donut', height:240 },
            series: Object.keys(sc).map(k => sc[k]),
            labels: Object.keys(sc).map(k => statusMap[k] ?? k),
            colors: Object.keys(sc).map(k => statusColors[k] ?? '#9ca3af'),
            legend: { position:'bottom', fontSize:'11px' },
            plotOptions: { pie: { donut: { size:'60%' } } },
            dataLabels: { style: { fontSize:'10px' } },
        });
        renderChart('chart-delivery', {
            chart: { ...font, ...noBar, type:'bar', height:240 },
            series: [{ data: [sc['delivered']??0, sc['returned']??0, sc['rejected']??0, sc['returning']??0, sc['pending']??0, sc['processing']??0] }],
            xaxis: { categories: ['مسلمة','مستردة','مرفوضة','قيد الاسترداد','قيد الموافقة','قيد التجهيز'], labels:{style:{fontSize:'11px'}} },
            colors: ['#22c55e','#f59e0b','#ef4444','#f97316','#a855f7','#3b82f6'],
            plotOptions: { bar: { borderRadius:6, columnWidth:'40%', distributed:true } },
            legend: { show:false },
            dataLabels: { enabled:true, style:{fontSize:'11px'} },
            yaxis: { labels: { formatter: fmtN } },
            tooltip: { y: { formatter: v => v + ' طلب' } },
            grid: { borderColor:'#f0f0f0' },
        });
    }

    if (tabId === 'tab-products') {
        renderChart('chart-top-products', {
            chart: { ...font, ...noBar, type:'bar', height:280 },
            series: [{ name:'الإيراد', data: topPrRev.slice(0,10).map(v => Math.round(v)) }],
            xaxis: { categories: topPrNames, labels:{style:{fontSize:'10px'}} },
            yaxis: { labels: { formatter: fmtN } },
            plotOptions: { bar: { borderRadius:5, columnWidth:'55%' } },
            colors: ['#4f46e5'],
            dataLabels: { enabled:false },
            tooltip: { y: { formatter: fmt } },
            grid: { borderColor:'#f0f0f0' },
        });
        renderChart('chart-products-pie', {
            chart: { ...font, ...noBar, type:'donut', height:280 },
            series: topPrPie.map(p => p.val),
            labels: topPrPie.map(p => p.name),
            legend: { position:'bottom', fontSize:'10px' },
            plotOptions: { pie: { donut: { size:'55%' } } },
            dataLabels: { style:{fontSize:'10px'} },
            tooltip: { y: { formatter: fmt } },
        });
    }

    if (tabId === 'tab-marketers') {
        renderChart('chart-marketer-profit', {
            chart: { ...font, ...noBar, type:'bar', height: Math.max(240, mktNames.length * 40) },
            series: [{ name:'الربح', data: mktProfit }],
            xaxis: { categories: mktNames, labels:{style:{fontSize:'11px'}} },
            yaxis: { labels: { formatter: fmtN } },
            plotOptions: { bar: { borderRadius:5, horizontal:true, barHeight:'55%' } },
            colors: ['#22c55e'],
            dataLabels: { enabled:false },
            tooltip: { y: { formatter: fmt } },
            grid: { borderColor:'#f0f0f0' },
        });
    }

    if (tabId === 'tab-hala') {
        renderChart('chart-hala-profit', {
            chart: { ...font, ...noBar, type:'area', height:280 },
            series: [
                { name:'إيرادات من المسوقات', data: halaRevenueData },
                { name:'تكلفة الموردين',           data: halaSupData },
                { name:'صافي ربح هالة',            data: halaProfitData },
            ],
            xaxis: { categories: labels, labels:{style:{fontSize:'11px'}, rotate: labels.length > 15 ? -30 : 0} },
            yaxis: { labels: { formatter: fmtN } },
            colors: ['#4f46e5','#ef4444','#22c55e'],
            stroke: { curve:'smooth', width:2.5 },
            fill:   { type:'gradient', gradient:{shadeIntensity:1,opacityFrom:.25,opacityTo:.02} },
            dataLabels: { enabled:false },
            tooltip: { y: { formatter: fmt } },
            legend: { position:'top', fontSize:'11px' },
            grid: { borderColor:'#f0f0f0' },
            annotations: { yaxis: [{ y:0, borderColor:'#9ca3af', borderWidth:1, strokeDashArray:4 }] },
        });
    }
}

// Tab persistence
document.querySelectorAll('#reportTabs [data-bs-toggle="tab"]').forEach(btn => {
    btn.addEventListener('shown.bs.tab', e => {
        const tabId = e.target.dataset.tab;
        document.getElementById('activeTabInput').value = tabId;
        renderTabCharts(e.target.dataset.bsTarget.substring(1));
    });
});

// Render charts for the initially active tab
const activeTab = document.querySelector('#reportTabs .nav-link.active');
if (activeTab) renderTabCharts(activeTab.dataset.bsTarget.substring(1));

// Orders status filter
function filterOrders(status) {
    document.getElementById('orderStatusInput').value = status;
    document.getElementById('activeTabInput').value = 'orders';
    document.getElementById('filterForm').submit();
}
</script>
@endpush
