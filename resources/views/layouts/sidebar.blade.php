<div class="sidenav-menu">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="logo">
        <span class="logo logo-light">
            <span class="logo-lg"><img src="{{ asset('assets/images/logo-black.png') }}" style="height: 95px;" alt="logo" /></span>
            <span class="logo-sm"><img src="{{ asset('assets/images/logo-sm.png') }}" alt="small logo" /></span>
        </span>
        <span class="logo logo-dark">
            <span class="logo-lg"><img src="{{ asset('assets/images/logo-black.png') }}" style="height: 95px;" alt="dark logo" /></span>
            <span class="logo-sm"><img src="{{ asset('assets/images/logo-sm.png') }}" alt="small logo" /></span>
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <button class="button-on-hover">
        <span class="btn-on-hover-icon"></span>
    </button>

    <!-- Full Sidebar Menu Close Button -->
    <button class="button-close-offcanvas">
        <i class="ti ti-menu-4 align-middle"></i>
    </button>

    <div class="scrollbar" data-simplebar="">
        <!-- Sidebar User -->
        <div class="sidenav-user" style="background: url({{ asset('assets/images/user-bg-pattern.svg') }})">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="#!" class="link-reset">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random&color=fff&size=48" alt="user-image" class="rounded-circle mb-2 avatar-md" />
                        <span class="sidenav-user-name fw-bold">{{ Auth::user()->name }}</span>
                        <span class="fs-12 fw-semibold">{{ Auth::user()->phone }}</span>
                    </a>
                </div>
                <div>
                    <a class="dropdown-toggle drop-arrow-none link-reset sidenav-user-set-icon" data-bs-toggle="dropdown" data-bs-offset="0,12" href="#!">
                        <i class="ti ti-settings fs-24 align-middle ms-1"></i>
                    </a>
                    <div class="dropdown-menu">
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">مرحباً!</h6>
                        </div>
                        <a href="#!" class="dropdown-item">
                            <i class="ti ti-user-circle me-1 fs-lg align-middle"></i>
                            <span class="align-middle">الملف الشخصي</span>
                        </a>
                       
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger fw-semibold">
                                <i class="ti ti-logout me-1 fs-lg align-middle"></i>
                                <span class="align-middle">تسجيل الخروج</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!--- Sidenav Menu -->
        <div id="sidenav-menu">
            <ul class="side-nav">

                <li class="side-nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="side-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="ti ti-home"></i></span>
                        <span class="menu-text">الرئيسية</span>
                    </a>
                </li>


                @if(Auth::user()->can_access('marketers.view'))
                <li class="side-nav-item">
                    <a href="#marketersMenu"
                       data-bs-toggle="collapse"
                       class="side-nav-link {{ request()->routeIs('marketers.*') ? '' : 'collapsed' }}"
                       aria-expanded="{{ request()->routeIs('marketers.*') ? 'true' : 'false' }}">
                        <span class="menu-icon"><i class="ti ti-users"></i></span>
                        <span class="menu-text">المسوقين</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ request()->routeIs('marketers.*') ? 'show' : '' }}" id="marketersMenu">
                        <ul class="sub-menu">
                            @if(Auth::user()->can_access('marketers.manage'))
                            <li class="side-nav-item">
                                <a href="{{ route('marketers.create') }}"
                                   class="side-nav-link {{ request()->routeIs('marketers.create') ? 'active' : '' }}">
                                    <i class="ti ti-plus me-1"></i> إضافة مسوق جديد
                                </a>
                            </li>
                            @endif
                            <li class="side-nav-item">
                                <a href="{{ route('marketers.index') }}"
                                   class="side-nav-link {{ request()->routeIs('marketers.index') ? 'active' : '' }}">
                                    <i class="ti ti-list me-1"></i> عرض المسوقين
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                @if(Auth::user()->can_access('products.view'))
                <li class="side-nav-item">
                    <a href="#productsMenu"
                       data-bs-toggle="collapse"
                       class="side-nav-link {{ request()->routeIs('products.*') ? '' : 'collapsed' }}"
                       aria-expanded="{{ request()->routeIs('products.*') ? 'true' : 'false' }}">
                        <span class="menu-icon"><i class="ti ti-box"></i></span>
                        <span class="menu-text">المنتجات</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ request()->routeIs('products.*') ? 'show' : '' }}" id="productsMenu">
                        <ul class="sub-menu">
                            @if(Auth::user()->can_access('products.edit'))
                            <li class="side-nav-item">
                                <a href="{{ route('products.create') }}"
                                   class="side-nav-link {{ request()->routeIs('products.create') ? 'active' : '' }}">
                                    <i class="ti ti-plus me-1"></i> إضافة منتج جديد
                                </a>
                            </li>
                            @endif
                            <li class="side-nav-item">
                                <a href="{{ route('products.index') }}"
                                   class="side-nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}">
                                    <i class="ti ti-list me-1"></i> عرض المنتجات
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                @php
                    $canSeeOrders = Auth::user()->is_super
                        || Auth::user()->can_access('orders.pending')
                        || Auth::user()->can_access('orders.active')
                        || Auth::user()->can_access('orders.delivered')
                        || Auth::user()->can_access('orders.returned');
                @endphp
                @if($canSeeOrders)
                <li class="side-nav-item">
                    <a href="#ordersMenu"
                       data-bs-toggle="collapse"
                       class="side-nav-link {{ request()->routeIs('orders.*') ? '' : 'collapsed' }}"
                       aria-expanded="{{ request()->routeIs('orders.*') ? 'true' : 'false' }}">
                        <span class="menu-icon"><i class="ti ti-clipboard-list"></i></span>
                        <span class="menu-text">الطلبات</span>
                        @php
                            $totalOrders = 0;
                            if (Auth::user()->can_access('orders.pending'))   $totalOrders += ($orderCounts['pending'] ?? 0);
                            if (Auth::user()->can_access('orders.active'))    $totalOrders += ($orderCounts['processing'] ?? 0) + ($orderCounts['with_agent'] ?? 0);
                            if (Auth::user()->can_access('orders.returned'))  $totalOrders += ($orderCounts['returning'] ?? 0);
                        @endphp
                        @if($totalOrders > 0)
                            <span class="badge bg-danger ms-auto rounded-pill" style="font-size:.7rem">{{ $totalOrders }}</span>
                        @endif
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ request()->routeIs('orders.*') ? 'show' : '' }}" id="ordersMenu">
                        <ul class="sub-menu">

                            @if(Auth::user()->can_access('orders.pending'))
                            <li class="side-nav-item">
                                <a href="{{ route('orders.index', ['filter[status]' => 'pending']) }}"
                                   class="side-nav-link {{ request('filter.status') === 'pending' ? 'active' : '' }}">
                                    <i class="ti ti-clock me-1"></i>
                                    <span>طلبات جديدة</span>
                                    @if(($orderCounts['pending'] ?? 0) > 0)
                                        <span class="badge bg-warning text-dark ms-auto rounded-pill" style="font-size:.7rem">{{ $orderCounts['pending'] }}</span>
                                    @endif
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->can_access('orders.active'))
                            <li class="side-nav-item">
                                <a href="{{ route('orders.index', ['filter[status]' => 'processing']) }}"
                                   class="side-nav-link {{ request('filter.status') === 'processing' ? 'active' : '' }}">
                                    <i class="ti ti-settings me-1"></i>
                                    <span>قيد التجهيز</span>
                                    @if(($orderCounts['processing'] ?? 0) > 0)
                                        <span class="badge bg-primary ms-auto rounded-pill" style="font-size:.7rem">{{ $orderCounts['processing'] }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="side-nav-item">
                                <a href="{{ route('orders.index', ['filter[status]' => 'with_agent']) }}"
                                   class="side-nav-link {{ request('filter.status') === 'with_agent' ? 'active' : '' }}">
                                    <i class="ti ti-motorbike me-1"></i>
                                    <span>بحوزة المندوب</span>
                                    @if(($orderCounts['with_agent'] ?? 0) > 0)
                                        <span class="badge bg-info ms-auto rounded-pill" style="font-size:.7rem">{{ $orderCounts['with_agent'] }}</span>
                                    @endif
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->can_access('orders.delivered'))
                            <li class="side-nav-item">
                                <a href="{{ route('orders.index', ['filter[status]' => 'delivered']) }}"
                                   class="side-nav-link {{ request('filter.status') === 'delivered' ? 'active' : '' }}">
                                    <i class="ti ti-circle-check me-1"></i>
                                    <span>الطلبات المستلمة</span>
                                    @if(($orderCounts['delivered'] ?? 0) > 0)
                                        <span class="badge bg-success ms-auto rounded-pill" style="font-size:.7rem">{{ $orderCounts['delivered'] }}</span>
                                    @endif
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->can_access('orders.returned'))
                            <li class="side-nav-item">
                                <a href="{{ route('orders.index', ['filter[status]' => 'returning']) }}"
                                   class="side-nav-link {{ request('filter.status') === 'returning' ? 'active' : '' }}">
                                    <i class="ti ti-refresh me-1"></i>
                                    <span>قيد الاسترداد</span>
                                    @if(($orderCounts['returning'] ?? 0) > 0)
                                        <span class="badge bg-warning text-dark ms-auto rounded-pill" style="font-size:.7rem">{{ $orderCounts['returning'] }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="side-nav-item">
                                <a href="{{ route('orders.index', ['filter[status]' => 'returned']) }}"
                                   class="side-nav-link {{ request('filter.status') === 'returned' ? 'active' : '' }}">
                                    <i class="ti ti-arrow-back me-1"></i>
                                    <span>طلبات مستردة</span>
                                    @if(($orderCounts['returned'] ?? 0) > 0)
                                        <span class="badge bg-secondary ms-auto rounded-pill" style="font-size:.7rem">{{ $orderCounts['returned'] }}</span>
                                    @endif
                                </a>
                            </li>
                            @endif

                        </ul>
                    </div>
                </li>
                @endif

                @if(Auth::user()->can_access('agents'))
                <li class="side-nav-item">
                    <a href="#agentsMenu"
                       data-bs-toggle="collapse"
                       class="side-nav-link {{ request()->routeIs('agents.*') ? '' : 'collapsed' }}"
                       aria-expanded="{{ request()->routeIs('agents.*') ? 'true' : 'false' }}">
                        <span class="menu-icon"><i class="ti ti-motorbike"></i></span>
                        <span class="menu-text">المندوبين</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ request()->routeIs('agents.*') ? 'show' : '' }}" id="agentsMenu">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a href="{{ route('agents.create') }}"
                                   class="side-nav-link {{ request()->routeIs('agents.create') ? 'active' : '' }}">
                                    <i class="ti ti-plus me-1"></i> إضافة مندوب
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a href="{{ route('agents.index') }}"
                                   class="side-nav-link {{ request()->routeIs('agents.index') ? 'active' : '' }}">
                                    <i class="ti ti-list me-1"></i> عرض المندوبين
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                @if(Auth::user()->can_access('vaults'))
                <li class="side-nav-item">
                    <a href="#vaultsMenu"
                       data-bs-toggle="collapse"
                       class="side-nav-link {{ request()->routeIs('vaults.*') ? '' : 'collapsed' }}"
                       aria-expanded="{{ request()->routeIs('vaults.*') ? 'true' : 'false' }}">
                        <span class="menu-icon"><i class="ti ti-cash-banknote"></i></span>
                        <span class="menu-text">الخزائن المالية</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ request()->routeIs('vaults.*') ? 'show' : '' }}" id="vaultsMenu">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a href="{{ route('vaults.create') }}"
                                   class="side-nav-link {{ request()->routeIs('vaults.create') ? 'active' : '' }}">
                                    <i class="ti ti-plus me-1"></i> إضافة خزينة
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a href="{{ route('vaults.index') }}"
                                   class="side-nav-link {{ request()->routeIs('vaults.index') ? 'active' : '' }}">
                                    <i class="ti ti-list me-1"></i> عرض الخزائن
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                @if(Auth::user()->can_access('reports'))
                <li class="side-nav-item">
                    <a href="{{ route('reports.index') }}"
                       class="side-nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="ti ti-chart-bar"></i></span>
                        <span class="menu-text">التقارير</span>
                    </a>
                </li>
                @endif

                @if(Auth::user()->can_access('users'))
                <li class="side-nav-item">
                    <a href="{{ route('users.index') }}"
                       class="side-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="ti ti-user-shield"></i></span>
                        <span class="menu-text">إدارة المستخدمين</span>
                    </a>
                </li>
                @endif

                @if(Auth::user()->can_access('settings'))
                <li class="side-nav-item">
                    <a href="{{ route('settings.edit') }}"
                       class="side-nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="ti ti-settings-2"></i></span>
                        <span class="menu-text">الإعدادات العامة</span>
                    </a>
                </li>
                @endif

            </ul>
        </div>
    </div>
</div>
