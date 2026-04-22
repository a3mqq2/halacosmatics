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

    /* card variants */
    .stat-total   { background: #f0f4ff; border-color: #e0e7ff; }
    .stat-total   .icon-wrap { background: #e0e7ff; color: #8d5342; }
    .stat-total   .stat-label, .stat-total .stat-count { color: #3730a3; }

.stat-active  { background: #f0fdf4; border-color: #bbf7d0; }
    .stat-active  .icon-wrap { background: #dcfce7; color: #16a34a; }
    .stat-active  .stat-label, .stat-active  .stat-count { color: #14532d; }

    .stat-product  { background: #fdf4ff; border-color: #f0abfc; }
    .stat-product  .icon-wrap { background: #fae8ff; color: #a21caf; }
    .stat-product  .stat-label, .stat-product  .stat-count { color: #701a75; }

    .stat-inactive { background: #f9fafb; border-color: #e5e7eb; }
    .stat-inactive .icon-wrap { background: #f3f4f6; color: #6b7280; }
    .stat-inactive .stat-label, .stat-inactive .stat-count { color: #374151; }

    .stat-qty      { background: #eff6ff; border-color: #bfdbfe; }
    .stat-qty      .icon-wrap { background: #dbeafe; color: #1d4ed8; }
    .stat-qty      .stat-label, .stat-qty  .stat-count { color: #1e3a8a; }

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

{{-- ===== Stats Widgets ===== --}}
<p class="section-title"><i class="ti ti-users me-1"></i> المسوقين</p>
<div class="row g-3">

    <div class="col-12 col-sm-6 col-xl-4">
        <a href="{{ route('marketers.index') }}" class="stat-card stat-total d-flex">
            <div class="icon-wrap">
                <i class="ti ti-users"></i>
            </div>
            <div>
                <p class="stat-label mb-0">إجمالي المسوقين</p>
                <div class="stat-count">{{ $totalMarketers }}</div>
                <p class="stat-sub mb-0">جميع السجلات</p>
            </div>
        </a>
    </div>

<div class="col-12 col-sm-6 col-xl-4">
        <a href="{{ route('marketers.index', ['filter[is_active]' => '1']) }}"
           class="stat-card stat-active d-flex">
            <div class="icon-wrap">
                <i class="ti ti-user-check"></i>
            </div>
            <div>
                <p class="stat-label mb-0">المسوقون المفعّلون</p>
                <div class="stat-count">{{ $activeMarketers }}</div>
                <p class="stat-sub mb-0">مقبولون ونشطون</p>
            </div>
        </a>
    </div>

</div>

{{-- ===== Products Widgets ===== --}}
<p class="section-title"><i class="ti ti-box me-1"></i> المنتجات</p>
<div class="row g-3">

    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('products.index') }}" class="stat-card stat-product d-flex">
            <div class="icon-wrap">
                <i class="ti ti-box"></i>
            </div>
            <div>
                <p class="stat-label mb-0">إجمالي المنتجات</p>
                <div class="stat-count">{{ $totalProducts }}</div>
                <p class="stat-sub mb-0">جميع السجلات</p>
            </div>
        </a>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('products.index', ['filter[is_active]' => '1']) }}" class="stat-card stat-active d-flex">
            <div class="icon-wrap">
                <i class="ti ti-box-seam"></i>
            </div>
            <div>
                <p class="stat-label mb-0">المنتجات المفعّلة</p>
                <div class="stat-count">{{ $activeProducts }}</div>
                <p class="stat-sub mb-0">نشطة ومتاحة</p>
            </div>
        </a>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('products.index', ['filter[is_active]' => '0']) }}" class="stat-card stat-inactive d-flex">
            <div class="icon-wrap">
                <i class="ti ti-box-off"></i>
            </div>
            <div>
                <p class="stat-label mb-0">المنتجات الموقوفة</p>
                <div class="stat-count">{{ $inactiveProducts }}</div>
                <p class="stat-sub mb-0">غير نشطة</p>
            </div>
        </a>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('products.index') }}" class="stat-card stat-qty d-flex">
            <div class="icon-wrap">
                <i class="ti ti-stack"></i>
            </div>
            <div>
                <p class="stat-label mb-0">إجمالي الكميات</p>
                <div class="stat-count">{{ number_format($totalQuantity) }}</div>
                <p class="stat-sub mb-0">مجموع المخزون</p>
            </div>
        </a>
    </div>

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
