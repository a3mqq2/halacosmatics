
<!doctype html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8" />
        <title>   هالة كوزماتكس  | @yield('title') </title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="base-url" content="{{ rtrim(url('/'), '/') }}" />
        <meta name="description" content="هالة كوزماتكس  ٫ سجل معنا الان كمسوق واستفيدي من العمل اونلاين سواء وبسهولة عن طريق تسويقك لمنتجات هالة" />
        <meta name="keywords" content="هالة ٫ تسويق ٫ مسوقين ٫ كوزماتكس سوق ٫ ميكب" />
        <meta name="author" content="Hulul FOR IT" />
        <link rel="shortcut icon" href="{{ asset('assets/images/logo-sm.png') }}" />
        <link href="{{ asset('assets/plugins/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />

         <link rel="preconnect" href="https://fonts.googleapis.com">
         <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
         <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet">

        {{--  import almarai font --}}


        <style>
            body,h1,h2,h3,h4,h5,h6,p,span,td,th,button,a,label,input,option {
               font-family: "Almarai", sans-serif !important;
            }
            .sidenav-menu .logo {
                position: relative;
                z-index: 10;
            }
            .sidenav-menu .scrollbar[data-simplebar] {
                position: absolute;
                top: 65px;
                bottom: 0;
                left: 0;
                right: 0;
                height: auto;
            }
        </style>


        <script>
            (function() {
                var base = document.querySelector('meta[name="base-url"]').getAttribute('content');
                window.__baseUrl = base;
                var _fetch = window.fetch;
                window.fetch = function(url, opts) {
                    if (typeof url === 'string' && url.startsWith('/')) {
                        url = base + url;
                    }
                    return _fetch.call(this, url, opts);
                };
            })();
        </script>
        <script src="{{ asset('assets/js/config.js') }}"></script>

        <!-- Vendor css -->
        <link href="{{ asset('assets/css/vendors.min.css') }}" rel="stylesheet" type="text/css" />

        <!-- App css -->
        <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

        <style>
            * {
                font-family: 'IBM Plex Sans Arabic', sans-serif !important;
            }
            @media (max-width: 575.98px) {
                .card-body {
                    padding: 0.75rem;
                }
                .filter-section {
                    padding: 0.75rem;
                }
                .table th,
                .table td {
                    font-size: 0.78rem;
                    padding: 0.4rem 0.5rem;
                }
                .stats-card {
                    padding: 0.75rem;
                }
                .stats-card .stats-value {
                    font-size: 1.2rem;
                }
                .btn-sm {
                    font-size: 0.75rem;
                    padding: 0.2rem 0.5rem;
                }
                h5.card-title {
                    font-size: 1rem;
                }
                .table-responsive {
                    -webkit-overflow-scrolling: touch;
                }
            }
            .info-list { margin: 0; padding: 0; }
            .info-row {
                display: flex;
                align-items: center;
                gap: 1rem;
                padding: .9rem 1.5rem;
                border-bottom: 1px solid #f3efed;
            }
            .info-row:last-child { border-bottom: none; }
            .info-row:nth-child(even) { background: #fdfaf9; }
            .info-row dt {
                width: 160px;
                min-width: 140px;
                font-size: .82rem;
                font-weight: 700;
                color: #6b7280;
                margin: 0;
            }
            .info-row dd {
                flex: 1;
                font-size: .95rem;
                font-weight: 600;
                color: #111827;
                margin: 0;
            }
            .btn-wa {
                color: #16a34a;
                font-size: 1.1rem;
                line-height: 1;
                text-decoration: none;
                transition: color .15s;
            }
            .btn-wa:hover { color: #15803d; }
        </style>
        @stack('styles')
    </head>

    <body dir="rtl">

        {{-- Loading Overlay --}}
        <div id="page-loading-overlay"
             style="display:none;position:fixed;inset:0;background:rgba(255,255,255,.65);backdrop-filter:blur(2px);z-index:99999;align-items:center;justify-content:center;">
            <div style="display:flex;flex-direction:column;align-items:center;gap:12px;">
                <div class="spinner-border text-primary" style="width:3rem;height:3rem;" role="status"></div>
                <span class="fw-semibold text-muted" style="font-size:.9rem">جاري التحميل...</span>
            </div>
        </div>

        <!-- Begin page -->
        <div class="wrapper">
            <header class="app-topbar">
                <div class="container-fluid topbar-menu">
                    <div class="d-flex align-items-center gap-2">
                        <!-- Topbar Brand Logo -->
                        <div class="logo-topbar">
                            <!-- Logo light -->
                            <a href="{{asset('dashboard')}}" class="logo-light">
                                <span class="logo-lg">
                                    <img src="{{asset('HULUL ERP.png')}}" alt="logo" />
                                </span>
                                <span class="logo-sm">
                                    <img src="{{ asset('assets/images/logo-sm.png') }}" alt="small logo" />
                                </span>
                            </a>

                            <!-- Logo Dark -->
                            <a href="{{asset('dashboard')}}" class="logo-dark">
                                <span class="logo-lg">
                                    <img src="{{asset('HULUL ERP.png')}}" alt="dark logo" style="height: 100px !important;" />
                                </span>
                                <span class="logo-sm">
                                    <img src="{{ asset('assets/images/logo-sm.png') }}" alt="small logo" />
                                </span>
                            </a>
                        </div>

                        <!-- Sidebar Menu Toggle Button -->
                        <button class="sidenav-toggle-button btn btn-primary btn-icon">
                            <i class="ti ti-menu-4"></i>
                        </button>

                        <!-- Horizontal Menu Toggle Button -->
                        <button class="topnav-toggle-button px-2" data-bs-toggle="collapse" data-bs-target="#topnav-menu">
                            <i class="ti ti-menu-4"></i>
                        </button>

                        <div id="search-box-rounded" class="app-search d-none d-xl-flex">
                            <input type="search" class="form-control rounded-pill topbar-search" name="search" placeholder="ابحث عن رقم فاتورة" />
                            <i class="ti ti-search app-search-icon text-muted"></i>
                        </div>

                   
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <div id="theme-dropdown" class="topbar-item d-none d-sm-flex">
                            <div class="dropdown">
                                <button class="topbar-link" data-bs-toggle="dropdown" type="button" aria-haspopup="false" aria-expanded="false">
                                    <i class="ti ti-sun topbar-link-icon d-none" id="theme-icon-light"></i>
                                    <i class="ti ti-moon topbar-link-icon d-none" id="theme-icon-dark"></i>
                                    <i class="ti ti-sun-moon topbar-link-icon d-none" id="theme-icon-system"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" data-thememode="dropdown">
                                    <label class="dropdown-item cursor-pointer">
                                        <input class="form-check-input" type="radio" name="data-bs-theme" value="light" style="display: none" />
                                        <i class="ti ti-sun align-middle me-1 fs-16"></i>
                                        <span class="align-middle">Light</span>
                                    </label>
                                    <label class="dropdown-item cursor-pointer">
                                        <input class="form-check-input" type="radio" name="data-bs-theme" value="dark" style="display: none" />
                                        <i class="ti ti-moon align-middle me-1 fs-16"></i>
                                        <span class="align-middle">Dark</span>
                                    </label>
                                    <label class="dropdown-item cursor-pointer">
                                        <input class="form-check-input" type="radio" name="data-bs-theme" value="system" style="display: none" />
                                        <i class="ti ti-sun-moon align-middle me-1 fs-16"></i>
                                        <span class="align-middle">System</span>
                                    </label>
                                </div>
                                <!-- end dropdown-menu-->
                            </div>
                            <!-- end dropdown-->
                        </div>

                    


                        <div id="fullscreen-toggler" class="topbar-item d-none d-md-flex">
                            <button class="topbar-link" type="button" data-toggle="fullscreen">
                                <i class="ti ti-maximize topbar-link-icon"></i>
                                <i class="ti ti-minimize topbar-link-icon d-none"></i>
                            </button>
                        </div>

                        <div id="monochrome-toggler" class="topbar-item d-none d-xl-flex">
                            <button id="monochrome-mode" class="topbar-link" type="button" data-toggle="monochrome">
                                <i class="ti ti-palette topbar-link-icon"></i>
                            </button>
                        </div>


                        @if(config('desktop.mode'))
                        <div id="sync-status-indicator" class="topbar-item">
                            <div class="dropdown">
                                <button class="topbar-link position-relative" data-bs-toggle="dropdown" type="button">
                                    <i class="ti ti-cloud-upload topbar-link-icon" id="sync-icon"></i>
                                    <span id="sync-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning d-none">0</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 280px;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fw-semibold">حالة المزامنة</span>
                                        <span id="network-status-text" class="badge bg-success">متصل</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="mb-2">
                                        <small class="text-muted">آخر مزامنة:</small>
                                        <div id="last-sync-time" class="fw-medium">-</div>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">تغييرات معلقة:</small>
                                        <div id="pending-changes-count" class="fw-medium">0</div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" id="sync-pull-btn" class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="ti ti-cloud-download me-1"></i>
                                            سحب
                                        </button>
                                        <button type="button" id="sync-push-btn" class="btn btn-outline-success btn-sm flex-fill">
                                            <i class="ti ti-cloud-upload me-1"></i>
                                            رفع
                                        </button>
                                    </div>
                                    <button type="button" id="manual-sync-btn" class="btn btn-primary btn-sm w-100 mt-2">
                                        <i class="ti ti-refresh me-1"></i>
                                        مزامنة كاملة
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div id="user-dropdown-detailed" class="topbar-item nav-user">
                            <div class="dropdown">
                                <a class="topbar-link dropdown-toggle drop-arrow-none px-2 d-flex align-items-center gap-2" data-bs-toggle="dropdown" href="#!" aria-haspopup="false" aria-expanded="false">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-primary text-white rounded-circle fs-14 fw-bold">
                                            {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 2)) : 'م' }}
                                        </span>
                                    </div>
                                    <span class="d-none d-lg-inline fw-semibold">{{ Auth::check() ? Auth::user()->name : 'مستخدم' }}</span>
                                    <i class="ti ti-chevron-down align-middle d-none d-lg-inline"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                                        @csrf
                                        <button type="submit" class="dropdown-item fw-semibold" id="logoutBtn">
                                            <i class="ti ti-logout me-1 fs-lg align-middle"></i>
                                            <span class="align-middle">تسجيل الخروج</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- Topbar End -->
@include('layouts.sidebar')
<!-- Sidenav Menu End -->


            <!-- ========================================== -->
            <!-- Start Main Content -->
            <!-- ========================================== -->

            <div class="content-page">
                <div class="container-fluid">
                    <div class="page-title-head d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="page-main-title m-0">@yield('title')</h4>
                        </div>

                        <div class="text-end">
                            <ol class="breadcrumb m-0 py-0">
                                {{-- <li class="breadcrumb-item"><a href="javascript: void(0);">Paces</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active">eCommerce</li> --}}

                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>

                    {{-- ===== Flash Messages ===== --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                            <i class="ti ti-circle-check fs-5"></i>
                            <span>{{ session('success') }}</span>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                            <i class="ti ti-alert-circle fs-5"></i>
                            <span>{{ session('error') }}</span>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                            <i class="ti ti-alert-triangle fs-5"></i>
                            <span>{{ session('warning') }}</span>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')

                </div>
                <!-- container -->

                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6 text-center text-md-start">
                                ©
                                <script>
                                    document.write(new Date().getFullYear())
                                </script>
                                <span class="fw-semibold">جميع الحقوق محفوظة</span>
                            </div>
                            <div class="col-md-6 text-center text-md-end">
                                <span class="text-muted">تنفيذ</span>
                                <span class="fw-semibold">شركة حلول لتقنية المعلومات</span>
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->

            </div>

            <!-- ========================================== -->
            <!-- End of Main Content -->
            <!-- ========================================== -->
        </div>
        <!-- END wrapper -->

 <!-- Vendor js -->
<script src="{{ asset('assets/js/vendors.min.js') }}"></script>

<!-- App js -->
<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

        <!-- Apex Chart js -->
        <script src="{{ asset('assets/plugins/apexcharts/apexcharts.min.js') }}"></script>

        <!-- Vector Map Js -->
        <script src="{{ asset('assets/plugins/jsvectormap/jsvectormap.min.js') }}"></script>
        <script src="{{ asset('assets/js/maps/world-merc.js') }}"></script>
        <script src="{{ asset('assets/js/maps/world.js') }}"></script>

        <!-- Custom table -->
        <script src="{{ asset('assets/js/pages/custom-table.js') }}"></script>

        @stack('page-scripts')

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

        <script>
            document.getElementById('logoutForm').addEventListener('submit', function() {
                var btn = document.getElementById('logoutBtn');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span><span class="align-middle">جاري الخروج...</span>';
            });
        </script>


        <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        </style>
    </body>
</html>
