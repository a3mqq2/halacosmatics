@extends('layouts.app')
@section('title', 'الصفحة الرئيسية')

@push('styles')
<style>
    .welcome-banner {
        background: linear-gradient(135deg, #1a0c08 0%, #2d1610 50%, #4a2619 100%);
        border-radius: 16px;
        padding: 36px 40px;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 8px 32px rgba(34, 7, 31, .55);
    }
    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -60px; left: -60px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: rgba(200, 80, 180, .12);
    }
    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -80px; right: -40px;
        width: 280px; height: 280px;
        border-radius: 50%;
        background: rgba(180, 60, 160, .1);
    }
    .welcome-banner .content { position: relative; z-index: 1; }
    .welcome-banner h2 {
        font-size: 1.6rem;
        font-weight: 800;
        margin-bottom: 6px;
    }
    .welcome-banner p {
        font-size: 0.95rem;
        opacity: .85;
        margin: 0;
    }
    .welcome-banner .badge-time {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(200, 80, 180, .2);
        border: 1px solid rgba(220, 150, 200, .25);
        border-radius: 50px;
        padding: 5px 14px;
        font-size: 0.8rem;
        margin-bottom: 16px;
        backdrop-filter: blur(4px);
    }

    .stat-card {
        border-radius: 14px;
        padding: 22px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        text-decoration: none;
        transition: transform .2s, box-shadow .2s;
        border: 1px solid transparent;
        cursor: pointer;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0,0,0,.1);
        text-decoration: none;
    }
    .stat-card .icon-wrap {
        width: 54px; height: 54px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }
    .stat-card .stat-label {
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .stat-card .stat-count {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }
    .stat-card .stat-sub {
        font-size: 0.75rem;
        margin-top: 3px;
        opacity: .7;
    }

    .stat-total     { background: #f0f4ff; border-color: #e0e7ff; }
    .stat-total     .icon-wrap { background: #e0e7ff; color: #8d5342; }
    .stat-total     .stat-label, .stat-total .stat-count { color: #3730a3; }

    .stat-active    { background: #f0fdf4; border-color: #bbf7d0; }
    .stat-active    .icon-wrap { background: #dcfce7; color: #16a34a; }
    .stat-active    .stat-label, .stat-active .stat-count { color: #14532d; }

    .stat-product   { background: #fdf4ff; border-color: #f0abfc; }
    .stat-product   .icon-wrap { background: #fae8ff; color: #a21caf; }
    .stat-product   .stat-label, .stat-product .stat-count { color: #701a75; }

    .stat-inactive  { background: #f9fafb; border-color: #e5e7eb; }
    .stat-inactive  .icon-wrap { background: #f3f4f6; color: #6b7280; }
    .stat-inactive  .stat-label, .stat-inactive .stat-count { color: #374151; }

    .stat-qty       { background: #eff6ff; border-color: #bfdbfe; }
    .stat-qty       .icon-wrap { background: #dbeafe; color: #1d4ed8; }
    .stat-qty       .stat-label, .stat-qty .stat-count { color: #1e3a8a; }

    .stat-warning   { background: #fffbeb; border-color: #fde68a; }
    .stat-warning   .icon-wrap { background: #fef3c7; color: #d97706; }
    .stat-warning   .stat-label, .stat-warning .stat-count { color: #92400e; }

    .stat-info      { background: #f0f9ff; border-color: #bae6fd; }
    .stat-info      .icon-wrap { background: #e0f2fe; color: #0284c7; }
    .stat-info      .stat-label, .stat-info .stat-count { color: #0c4a6e; }

    .stat-orange    { background: #fff7ed; border-color: #fed7aa; }
    .stat-orange    .icon-wrap { background: #ffedd5; color: #ea580c; }
    .stat-orange    .stat-label, .stat-orange .stat-count { color: #7c2d12; }

    .stat-secondary { background: #f8fafc; border-color: #cbd5e1; }
    .stat-secondary .icon-wrap { background: #f1f5f9; color: #475569; }
    .stat-secondary .stat-label, .stat-secondary .stat-count { color: #1e293b; }

    .stat-agent     { background: #f5f3ff; border-color: #ddd6fe; }
    .stat-agent     .icon-wrap { background: #ede9fe; color: #7c3aed; }
    .stat-agent     .stat-label, .stat-agent .stat-count { color: #4c1d95; }

    .stat-vault     { background: #f0fdfa; border-color: #99f6e4; }
    .stat-vault     .icon-wrap { background: #ccfbf1; color: #0f766e; }
    .stat-vault     .stat-label, .stat-vault .stat-count { color: #134e4a; }

    .stat-users     { background: #fff1f2; border-color: #fecdd3; }
    .stat-users     .icon-wrap { background: #ffe4e6; color: #be123c; }
    .stat-users     .stat-label, .stat-users .stat-count { color: #881337; }

    .stat-reports   { background: #fefce8; border-color: #fef08a; }
    .stat-reports   .icon-wrap { background: #fef9c3; color: #ca8a04; }
    .stat-reports   .stat-label, .stat-reports .stat-count { color: #713f12; }

    .stat-settings  { background: #f8f7ff; border-color: #ddd6fe; }
    .stat-settings  .icon-wrap { background: #ede9fe; color: #6d28d9; }
    .stat-settings  .stat-label, .stat-settings .stat-count { color: #4c1d95; }

    .section-title {
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #9ca3af;
        margin: 20px 0 10px;
    }

    @media (max-width: 575px) {
        .welcome-banner { padding: 24px 20px; }
        .welcome-banner h2 { font-size: 1.25rem; }
        .stat-card .stat-count { font-size: 1.6rem; }
    }
</style>
@endpush

@section('content')

{{-- ===== Welcome Banner ===== --}}
<div class="welcome-banner">
    <div class="content">
        <div class="badge-time">
            <i class="ti ti-clock"></i>
            <span id="currentTime"></span>
        </div>
        <h2>مرحباً، {{ Auth::user()->name }} 👋</h2>
        <p>لوحة التحكم الخاصة بنظام هالة كوزماتكس — إدارة المسوقين والعمليات</p>
    </div>
</div>

{{-- ===== المسوقين ===== --}}
@if(Auth::user()->can_access('marketers.view'))
<p class="section-title"><i class="ti ti-users me-1"></i> المسوقين</p>
<div class="row g-3">
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('marketers.index') }}" class="stat-card stat-total d-flex">
            <div class="icon-wrap"><i class="ti ti-users"></i></div>
            <div>
                <p class="stat-label mb-0">إجمالي المسوقين</p>
                <div class="stat-count">{{ $totalMarketers }}</div>
                <p class="stat-sub mb-0">جميع السجلات</p>
            </div>
        </a>
    </div>
</div>
@endif

{{-- ===== المنتجات ===== --}}
@if(Auth::user()->can_access('products.view'))
<p class="section-title"><i class="ti ti-box me-1"></i> المنتجات</p>
<div class="row g-3">

    <div class="col-6 col-sm-4 col-xl-3">
        <a href="{{ route('products.index') }}" class="stat-card stat-product d-flex">
            <div class="icon-wrap"><i class="ti ti-box"></i></div>
            <div>
                <p class="stat-label mb-0">إجمالي المنتجات</p>
                <div class="stat-count">{{ $totalProducts }}</div>
                <p class="stat-sub mb-0">جميع السجلات</p>
            </div>
        </a>
    </div>

    <div class="col-6 col-sm-4 col-xl-3">
        <a href="{{ route('products.index', ['filter[is_active]' => '1']) }}" class="stat-card stat-active d-flex">
            <div class="icon-wrap"><i class="ti ti-box-seam"></i></div>
            <div>
                <p class="stat-label mb-0">المنتجات المفعّلة</p>
                <div class="stat-count">{{ $activeProducts }}</div>
                <p class="stat-sub mb-0">نشطة ومتاحة</p>
            </div>
        </a>
    </div>

    <div class="col-6 col-sm-4 col-xl-3">
        <a href="{{ route('products.index', ['filter[is_active]' => '0']) }}" class="stat-card stat-inactive d-flex">
            <div class="icon-wrap"><i class="ti ti-box-off"></i></div>
            <div>
                <p class="stat-label mb-0">الموقوفة</p>
                <div class="stat-count">{{ $inactiveProducts }}</div>
                <p class="stat-sub mb-0">غير نشطة</p>
            </div>
        </a>
    </div>

    <div class="col-6 col-sm-4 col-xl-3">
        <a href="{{ route('products.index') }}" class="stat-card stat-qty d-flex">
            <div class="icon-wrap"><i class="ti ti-stack"></i></div>
            <div>
                <p class="stat-label mb-0">إجمالي الكميات</p>
                <div class="stat-count">{{ number_format($totalQuantity) }}</div>
                <p class="stat-sub mb-0">مجموع المخزون</p>
            </div>
        </a>
    </div>

</div>
@endif

{{-- ===== الطلبات ===== --}}
@php
    $canSeeOrders = Auth::user()->is_super
        || Auth::user()->can_access('orders.pending')
        || Auth::user()->can_access('orders.active')
        || Auth::user()->can_access('orders.delivered')
        || Auth::user()->can_access('orders.returned');
@endphp
@if($canSeeOrders)
<p class="section-title"><i class="ti ti-clipboard-list me-1"></i> الطلبات</p>
<div class="row g-3">

    @if(Auth::user()->can_access('orders.pending'))
    <div class="col-6 col-sm-4 col-xl-2">
        <a href="{{ route('orders.index', ['filter[status]' => 'pending']) }}" class="stat-card stat-warning d-flex">
            <div class="icon-wrap"><i class="ti ti-clock"></i></div>
            <div>
                <p class="stat-label mb-0">جديدة</p>
                <div class="stat-count">{{ $ordersPending }}</div>
                <p class="stat-sub mb-0">قيد الموافقة</p>
            </div>
        </a>
    </div>
    @endif

    @if(Auth::user()->can_access('orders.active'))
    <div class="col-6 col-sm-4 col-xl-2">
        <a href="{{ route('orders.index', ['filter[status]' => 'processing']) }}" class="stat-card stat-info d-flex">
            <div class="icon-wrap"><i class="ti ti-settings"></i></div>
            <div>
                <p class="stat-label mb-0">قيد التجهيز</p>
                <div class="stat-count">{{ $ordersProcessing }}</div>
                <p class="stat-sub mb-0">تحت المعالجة</p>
            </div>
        </a>
    </div>

    <div class="col-6 col-sm-4 col-xl-2">
        <a href="{{ route('orders.index', ['filter[status]' => 'with_agent']) }}" class="stat-card stat-agent d-flex">
            <div class="icon-wrap"><i class="ti ti-motorbike"></i></div>
            <div>
                <p class="stat-label mb-0">مع المندوب</p>
                <div class="stat-count">{{ $ordersWithAgent }}</div>
                <p class="stat-sub mb-0">بحوزة المندوب</p>
            </div>
        </a>
    </div>
    @endif

    @if(Auth::user()->can_access('orders.delivered'))
    <div class="col-6 col-sm-4 col-xl-2">
        <a href="{{ route('orders.index', ['filter[status]' => 'delivered']) }}" class="stat-card stat-active d-flex">
            <div class="icon-wrap"><i class="ti ti-circle-check"></i></div>
            <div>
                <p class="stat-label mb-0">مستلمة</p>
                <div class="stat-count">{{ $ordersDelivered }}</div>
                <p class="stat-sub mb-0">تم التسليم</p>
            </div>
        </a>
    </div>
    @endif

    @if(Auth::user()->can_access('orders.returned'))
    <div class="col-6 col-sm-4 col-xl-2">
        <a href="{{ route('orders.index', ['filter[status]' => 'returning']) }}" class="stat-card stat-orange d-flex">
            <div class="icon-wrap"><i class="ti ti-refresh"></i></div>
            <div>
                <p class="stat-label mb-0">قيد الاسترداد</p>
                <div class="stat-count">{{ $ordersReturning }}</div>
                <p class="stat-sub mb-0">جارٍ الاسترداد</p>
            </div>
        </a>
    </div>

    <div class="col-6 col-sm-4 col-xl-2">
        <a href="{{ route('orders.index', ['filter[status]' => 'returned']) }}" class="stat-card stat-secondary d-flex">
            <div class="icon-wrap"><i class="ti ti-arrow-back"></i></div>
            <div>
                <p class="stat-label mb-0">مستردة</p>
                <div class="stat-count">{{ $ordersReturned }}</div>
                <p class="stat-sub mb-0">تم الاسترداد</p>
            </div>
        </a>
    </div>

    <div class="col-6 col-sm-4 col-xl-2">
        <a href="{{ route('orders.index', ['filter[status]' => 'cancelled']) }}" class="stat-card stat-inactive d-flex">
            <div class="icon-wrap"><i class="ti ti-ban"></i></div>
            <div>
                <p class="stat-label mb-0">ملغاة</p>
                <div class="stat-count">{{ $ordersCancelled }}</div>
                <p class="stat-sub mb-0">تم الإلغاء</p>
            </div>
        </a>
    </div>
    @endif

</div>
@endif



{{-- ===== روابط سريعة ===== --}}
<p class="section-title"><i class="ti ti-layout-grid me-1"></i> إدارة النظام</p>
<div class="row g-3">

    @if(Auth::user()->can_access('agents'))
    <div class="col-6 col-sm-4 col-xl-2">
        <a href="{{ route('agents.index') }}" class="stat-card stat-agent d-flex">
            <div class="icon-wrap"><i class="ti ti-motorbike"></i></div>
            <div>
                <p class="stat-label mb-0">المندوبين</p>
                <div class="stat-count">{{ $totalAgents }}</div>
                <p class="stat-sub mb-0">إجمالي المندوبين</p>
            </div>
        </a>
    </div>
    @endif

    @if(Auth::user()->can_access('vaults'))
    <div class="col-6 col-sm-4 col-xl-2">
        <a href="{{ route('vaults.index') }}" class="stat-card stat-vault d-flex">
            <div class="icon-wrap"><i class="ti ti-cash-banknote"></i></div>
            <div>
                <p class="stat-label mb-0">الخزائن</p>
                <div class="stat-count">{{ $totalVaults }}</div>
                <p class="stat-sub mb-0">إجمالي الخزائن</p>
            </div>
        </a>
    </div>
    @endif

    @if(Auth::user()->can_access('users'))
    <div class="col-6 col-sm-4 col-xl-2">
        <a href="{{ route('users.index') }}" class="stat-card stat-users d-flex">
            <div class="icon-wrap"><i class="ti ti-user-shield"></i></div>
            <div>
                <p class="stat-label mb-0">المستخدمين</p>
                <div class="stat-count">{{ $totalUsers }}</div>
                <p class="stat-sub mb-0">مستخدمو النظام</p>
            </div>
        </a>
    </div>
    @endif

    @if(Auth::user()->can_access('reports'))
    <div class="col-6 col-sm-4 col-xl-2">
        <a href="{{ route('reports.index') }}" class="stat-card stat-reports d-flex">
            <div class="icon-wrap"><i class="ti ti-chart-bar"></i></div>
            <div>
                <p class="stat-label mb-0">التقارير</p>
                <div class="stat-count"><i class="ti ti-arrow-left fs-4"></i></div>
                <p class="stat-sub mb-0">عرض التقارير</p>
            </div>
        </a>
    </div>
    @endif

    @if(Auth::user()->can_access('settings'))
    <div class="col-6 col-sm-4 col-xl-2">
        <a href="{{ route('settings.edit') }}" class="stat-card stat-settings d-flex">
            <div class="icon-wrap"><i class="ti ti-settings-2"></i></div>
            <div>
                <p class="stat-label mb-0">الإعدادات العامة</p>
                <div class="stat-count"><i class="ti ti-arrow-left fs-4"></i></div>
                <p class="stat-sub mb-0">إدارة النظام</p>
            </div>
        </a>
    </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
    function updateTime() {
        const now = new Date();
        const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric',
                       hour:'2-digit', minute:'2-digit', hour12:false };
        document.getElementById('currentTime').textContent =
            now.toLocaleDateString('ar-LY', opts);
    }
    updateTime();
    setInterval(updateTime, 60000);
</script>
@endpush
