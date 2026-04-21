@extends('layouts.marketer')

@section('title', 'تقاريري')

@push('styles')
<style>
    .rpt-stat {
        border-radius: 16px;
        border: none;
        padding: 1rem 1.25rem;
    }
    .rpt-stat .rpt-label {
        font-size: .72rem;
        font-weight: 700;
        color: #9ca3af;
        margin-bottom: .2rem;
    }
    .rpt-stat .rpt-value {
        font-size: 1.4rem;
        font-weight: 800;
        line-height: 1.1;
    }
    .rpt-stat .rpt-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .chart-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
    }
    .chart-lbl {
        font-size: .95rem;
        font-weight: 700;
        color: #2d2d2d;
    }
    .filter-card {
        border-radius: 16px;
        border: 1.5px solid #f0ebe8;
        background: #fff;
    }
</style>
@endpush

@section('content')

<div class="px-1 pt-3 pb-2">

    {{-- Header --}}
    <div class="mb-3">
        <h5 class="fw-bold mb-0" style="color:#4a2619">تقاريري</h5>
        <span class="text-muted small">{{ \Carbon\Carbon::parse($fromStr)->format('Y/m/d') }} — {{ \Carbon\Carbon::parse($toStr)->format('Y/m/d') }}</span>
    </div>

    {{-- Date Filter --}}
    <div class="card filter-card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('marketer.reports') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-5">
                        <label class="form-label fw-semibold mb-1" style="font-size:.78rem">من تاريخ</label>
                        <input type="date" name="from" class="form-control form-control-sm rounded-3"
                               value="{{ $fromStr }}">
                    </div>
                    <div class="col-5">
                        <label class="form-label fw-semibold mb-1" style="font-size:.78rem">إلى تاريخ</label>
                        <input type="date" name="to" class="form-control form-control-sm rounded-3"
                               value="{{ $toStr }}">
                    </div>
                    <div class="col-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-3" style="height:32px">
                            <i class="ti ti-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-2 mb-3">
        <div class="col-6">
            <div class="card rpt-stat shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-2">
                    <div class="rpt-icon bg-primary-subtle text-primary"><i class="ti ti-trending-up"></i></div>
                    <div>
                        <div class="rpt-label">إجمالي المبيعات</div>
                        <div class="rpt-value text-primary">{{ number_format($totalSales) }}</div>
                        <div class="text-muted" style="font-size:.7rem">د.ل</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card rpt-stat shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-2">
                    <div class="rpt-icon bg-danger-subtle text-danger"><i class="ti ti-receipt"></i></div>
                    <div>
                        <div class="rpt-label">إجمالي التكلفة</div>
                        <div class="rpt-value text-danger">{{ number_format($totalCost) }}</div>
                        <div class="text-muted" style="font-size:.7rem">د.ل</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card rpt-stat shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-2">
                    <div class="rpt-icon {{ $totalProfit >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                        <i class="ti ti-coin"></i>
                    </div>
                    <div>
                        <div class="rpt-label">صافي الربح</div>
                        <div class="rpt-value {{ $totalProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($totalProfit) }}</div>
                        <div class="text-muted" style="font-size:.7rem">د.ل</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card rpt-stat shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-2">
                    <div class="rpt-icon bg-info-subtle text-info"><i class="ti ti-package"></i></div>
                    <div>
                        <div class="rpt-label">طلبات مسلمة</div>
                        <div class="rpt-value text-info">{{ $totalOrders }}</div>
                        <div class="text-muted" style="font-size:.7rem">طلب</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart: Sales --}}
    <div class="card chart-card mb-3">
        <div class="card-body">
            <div class="chart-lbl mb-1"><i class="ti ti-chart-line me-2 text-primary"></i>المبيعات</div>
            <div id="chart-sales"></div>
        </div>
    </div>

    {{-- Chart: Cost vs Sales --}}
    <div class="card chart-card mb-3">
        <div class="card-body">
            <div class="chart-lbl mb-1"><i class="ti ti-chart-bar me-2 text-warning"></i>التكلفة مقابل البيع</div>
            <div id="chart-cost-vs-sales"></div>
        </div>
    </div>

    {{-- Chart: Profit --}}
    <div class="card chart-card mb-3">
        <div class="card-body">
            <div class="chart-lbl mb-1"><i class="ti ti-chart-area me-2 text-success"></i>الأرباح</div>
            <div id="chart-profit"></div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    const labels = @json($labels);
    const sales  = @json($salesData);
    const cost   = @json($costData);
    const profit = @json($profitData);

    const base = {
        chart:  { fontFamily: 'Almarai, sans-serif', toolbar: { show: false }, height: 200 },
        xaxis:  { categories: labels, labels: { style: { fontSize: '10px' }, rotate: -30 } },
        yaxis:  { labels: { formatter: v => v.toLocaleString('ar-LY'), style: { fontSize: '10px' } } },
        tooltip: { y: { formatter: v => v.toLocaleString('ar-LY') + ' د.ل' } },
        dataLabels: { enabled: false },
        grid: { borderColor: '#f3f4f6' },
    };

    new ApexCharts(document.getElementById('chart-sales'), {
        ...base,
        chart: { ...base.chart, type: 'area' },
        series: [{ name: 'المبيعات', data: sales }],
        colors: ['#4f46e5'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: .4, opacityTo: .05 } },
        stroke: { curve: 'smooth', width: 2.5 },
    }).render();

    new ApexCharts(document.getElementById('chart-cost-vs-sales'), {
        ...base,
        chart: { ...base.chart, type: 'bar' },
        series: [
            { name: 'المبيعات', data: sales },
            { name: 'التكلفة',  data: cost  },
        ],
        colors: ['#4f46e5', '#ef4444'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
    }).render();

    new ApexCharts(document.getElementById('chart-profit'), {
        ...base,
        chart: { ...base.chart, type: 'area' },
        series: [{ name: 'الربح', data: profit }],
        colors: ['#22c55e'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: .4, opacityTo: .05 } },
        stroke: { curve: 'smooth', width: 2.5 },
        annotations: {
            yaxis: [{ y: 0, borderColor: '#ef4444', borderWidth: 1.5, strokeDashArray: 4 }]
        },
    }).render();
</script>
@endpush
