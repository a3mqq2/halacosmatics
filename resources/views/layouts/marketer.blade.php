<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>هالة كوزماتكس | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo-sm.png') }}">
    <link href="{{ asset('assets/css/vendors.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet">
    <style>
        body, * { font-family: 'Almarai', sans-serif !important; }
    body { padding-bottom: 80px; }
    .bottom-nav {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        height: 64px;
        background: #fff;
        border-top: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-around;
        z-index: 1000;
        box-shadow: 0 -4px 20px rgba(0,0,0,.06);
    }
    .bottom-nav a {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 3px;
        color: #9ca3af;
        text-decoration: none;
        font-size: .65rem;
        font-weight: 600;
        flex: 1;
        padding: 8px 0;
        transition: color .15s;
    }
    .bottom-nav a i { font-size: 22px; line-height: 1; }
    .bottom-nav a.active,
    .bottom-nav a:hover { color: #4a2619; }
    .bottom-nav .nav-center {
        position: relative;
        top: -18px;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        text-decoration: none;
    }
    .bottom-nav .nav-center .nav-center-btn {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4a2619, #2d1610);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 16px rgba(74,38,25,.4);
        transition: transform .15s, box-shadow .15s;
    }
    .bottom-nav .nav-center:hover .nav-center-btn,
    .bottom-nav .nav-center.active .nav-center-btn {
        transform: scale(1.08);
        box-shadow: 0 6px 20px rgba(74,38,25,.5);
    }
    .bottom-nav .nav-center i { font-size: 26px; color: #fff; }
    .bottom-nav .nav-center span {
        font-size: .65rem;
        font-weight: 700;
        color: #4a2619;
    }
        .marketer-hero {
            background: linear-gradient(135deg, #1a0c08 0%, #2d1610 50%, #4a2619 100%);
            border-radius: 16px;
            padding: 40px 32px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .marketer-hero::before {
            content: '';
            position: absolute;
            top: -60px; left: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: rgba(200, 80, 180, .12);
        }
        .marketer-hero .content { position: relative; z-index: 1; }
        .info-card {
            border-radius: 14px;
            padding: 20px 22px;
            display: flex;
            align-items: center;
            gap: 14px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }
        .info-card .icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            background: #fdeee8;
            color: #4a2619;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
    </style>
    @stack('styles')
</head>
<body dir="rtl">

{{-- Loading Overlay --}}
<div id="page-loading-overlay"
     style="display:none;position:fixed;inset:0;background:rgba(255,255,255,.7);backdrop-filter:blur(3px);z-index:9999;align-items:center;justify-content:center;">
    <div style="display:flex;flex-direction:column;align-items:center;gap:12px;">
        <div class="spinner-border" style="width:3rem;height:3rem;color:#4a2619;" role="status"></div>
        <span class="fw-semibold text-muted" style="font-size:.9rem">جاري التحميل...</span>
    </div>
</div>

<div class="container py-4" style="max-width: 680px">

    {{-- Header --}}
    @php
        $__m = Auth::guard('marketer')->user();
        $__initials = mb_strtoupper(mb_substr($__m->first_name, 0, 1) . mb_substr($__m->last_name, 0, 1));
    @endphp
    <div class="d-flex align-items-center justify-content-between mb-4">
        <img src="{{ asset('assets/images/logo-black.png') }}" alt="logo" style="height:72px">
        <div class="d-flex align-items-center gap-2">
            <div style="text-align:left">
                <div style="font-size:.72rem;color:#9ca3af;font-weight:600;line-height:1.2">مرحباً</div>
                <div style="font-size:.85rem;font-weight:800;color:#1a1a1a;line-height:1.2">{{ $__m->first_name }}</div>
            </div>
            <div style="position:relative">
                <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#4a2619,#7c3d28);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:800;flex-shrink:0;box-shadow:0 2px 8px rgba(74,38,25,.3);cursor:pointer"
                     data-bs-toggle="dropdown" aria-expanded="false">
                    {{ $__initials }}
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-1" style="min-width:160px">
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="px-2 py-1">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="ti ti-logout me-1"></i> تسجيل الخروج
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>



    @yield('content')

</div>
{{-- Bottom Navigation --}}
<nav class="bottom-nav">

    {{-- الرئيسية --}}
    <a href="{{ route('marketer.dashboard') }}" class="{{ request()->routeIs('marketer.dashboard') ? 'active' : '' }}">
        <i class="ti ti-home"></i>
        الرئيسية
    </a>

    {{-- المنتجات --}}
    <a href="{{ route('marketer.products') }}" class="{{ request()->routeIs('marketer.products*') ? 'active' : '' }}">
        <i class="ti ti-shopping-bag"></i>
        المنتجات
    </a>

    {{-- السلة — وسط بارز --}}
    @php $cartCount = app(\App\Services\CartService::class)->count(Auth::guard('marketer')->user()); @endphp
    <a href="{{ route('marketer.cart') }}"
       class="nav-center {{ request()->routeIs('marketer.cart*') ? 'active' : '' }}">
        <div class="nav-center-btn" style="position:relative">
            <i class="ti ti-shopping-cart"></i>
            @if($cartCount > 0)
                <span style="position:absolute;top:2px;left:2px;background:#e53e3e;color:#fff;font-size:.5rem;font-weight:800;border-radius:50%;width:16px;height:16px;display:flex;align-items:center;justify-content:center;line-height:1;border:2px solid #fff">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
            @endif
        </div>
        <span>السلة</span>
    </a>

    {{-- طلباتي --}}
    <a href="{{ route('marketer.orders.index') }}" class="{{ request()->routeIs('marketer.orders*') ? 'active' : '' }}">
        <i class="ti ti-clipboard-list"></i>
        طلباتي
    </a>

    {{-- معلوماتي --}}
    <a href="{{ route('marketer.profile') }}" class="{{ request()->routeIs('marketer.profile*') ? 'active' : '' }}">
        <i class="ti ti-user-circle"></i>
        معلوماتي
    </a>

</nav>

<script src="{{ asset('assets/js/vendors.min.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="{{ asset('assets/plugins/apexcharts/apexcharts.min.js') }}"></script>
@stack('scripts')
<script>
    (function () {
        const overlay = document.getElementById('page-loading-overlay');

        function show() { overlay.style.display = 'flex'; }
        function hide() { overlay.style.display = 'none'; }

        document.addEventListener('click', function (e) {
            const a = e.target.closest('a[href]');
            if (!a) return;
            const href = a.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript') || a.getAttribute('data-bs-toggle') || a.getAttribute('target') === '_blank') return;
            show();
        });

        document.addEventListener('submit', function (e) {
            if (e.target.tagName === 'FORM') show();
        });

        window.addEventListener('pageshow', hide);
        window.addEventListener('load', hide);
    })();
</script>
</body>
</html>
