<!doctype html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8" />
        <title>@yield('title') | Paces</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('assets/images/logo-sm.png') }}" />
        <!-- Theme Config Js -->
        <script src="{{ asset('assets/js/config.js') }}"></script>
        <!-- Vendor css -->
        <link href="{{ asset('assets/css/vendors.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App css -->
        <link id="app-style" href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- IBM Plex Sans Arabic (IBX) Font -->
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
        <style>
            body, * {
                font-family: 'IBM Plex Sans Arabic', sans-serif !important;
            }
        </style>
    </head>

    <body>

        {{-- Loading Overlay --}}
        <div id="page-loading-overlay"
             style="display:none;position:fixed;inset:0;background:rgba(255,255,255,.7);backdrop-filter:blur(2px);z-index:99999;align-items:center;justify-content:center;">
            <div style="display:flex;flex-direction:column;align-items:center;gap:12px;">
                <div class="spinner-border" style="width:3rem;height:3rem;color:#4a2619;" role="status"></div>
                <span class="fw-semibold text-muted" style="font-size:.9rem">جاري التحميل...</span>
            </div>
        </div>

        <div class="auth-box overflow-hidden align-items-center d-flex">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xxl-5 col-md-6 col-sm-8">
                        <div class="card p-4">
                            <div class="auth-brand text-center mb-2">
                                <a href="/" class="logo-dark">
                                    <img src="{{ asset('assets/images/logo-black.png') }}" style="height: 80px!important;" alt="dark logo" />
                                </a>
                                <a href="/" class="logo-light">
                                    <img src="{{ asset('assets/images/logo.png') }}" alt="logo" />
                                </a>
                                @yield('header')
                            </div>

                            @yield('content')
                        </div>

                        <p class="text-center text-muted mt-4 mb-0">
                            © {{ date('Y') }}
                            <span class="fw-semibold"> WF SYSTEM </span> — جميع الحقوق محفوظة
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor js -->
        <script src="{{ asset('assets/js/vendors.min.js') }}"></script>
        <!-- App js -->
        <script src="{{ asset('assets/js/app.js') }}"></script>
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
