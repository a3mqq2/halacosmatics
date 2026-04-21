@extends('layouts.app')

@section('title', 'تعديل مستخدم')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">المستخدمين</a></li>
    <li class="breadcrumb-item active">تعديل: {{ $user->name }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-12">
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 fw-bold"><i class="ti ti-user-edit me-1"></i> تعديل: {{ $user->name }}</h5>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf @method('PUT')

            <div class="row g-3">

                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">الاسم <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">اسم المستخدم <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username', $user->username) }}" required>
                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">كلمة المرور الجديدة <span class="text-muted small">(اتركها فارغة للإبقاء على الحالية)</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold mb-2">الصلاحيات</label>
                    <div class="card border-0 bg-light p-3" id="permissionsCard">

                        {{-- Super Admin --}}
                        <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="is_super" id="isSuper" value="1"
                                       {{ old('is_super', $user->is_super) ? 'checked' : '' }}
                                       onchange="togglePerms(this)">
                                <label class="form-check-label fw-semibold text-warning fs-6" for="isSuper">
                                    <i class="ti ti-crown me-1"></i> مدير عام — صلاحيات كاملة بدون قيود
                                </label>
                            </div>
                        </div>

                        <div id="granularPerms">

                            {{-- Users --}}
                            <div class="mb-3">
                                <div class="fw-semibold text-muted small mb-2 text-uppercase">
                                    <i class="ti ti-user-shield me-1"></i> المستخدمين
                                </div>
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input perm-toggle" type="checkbox" name="perm_users" id="permUsers" value="1"
                                               {{ old('perm_users', $user->perm_users) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permUsers">إدارة المستخدمين</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Orders --}}
                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="fw-semibold text-muted small text-uppercase"><i class="ti ti-clipboard-list me-1"></i> الطلبات</span>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1" style="font-size:.7rem" onclick="toggleGroup('orders')">تحديد الكل</button>
                                </div>
                                <div class="row g-2">
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle orders-perm" type="checkbox" name="perm_orders_pending" id="permOrdersPending" value="1"
                                                   {{ old('perm_orders_pending', $user->perm_orders_pending) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permOrdersPending">عرض الطلبات الجديدة</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle orders-perm" type="checkbox" name="perm_orders_active" id="permOrdersActive" value="1"
                                                   {{ old('perm_orders_active', $user->perm_orders_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permOrdersActive">عرض قيد التجهيز والمندوب</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle orders-perm" type="checkbox" name="perm_orders_delivered" id="permOrdersDelivered" value="1"
                                                   {{ old('perm_orders_delivered', $user->perm_orders_delivered) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permOrdersDelivered">عرض الطلبات المستلمة</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle orders-perm" type="checkbox" name="perm_orders_returned" id="permOrdersReturned" value="1"
                                                   {{ old('perm_orders_returned', $user->perm_orders_returned) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permOrdersReturned">عرض الطلبات المستردة</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle orders-perm" type="checkbox" name="perm_orders_approve" id="permOrdersApprove" value="1"
                                                   {{ old('perm_orders_approve', $user->perm_orders_approve) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permOrdersApprove">قبول / رفض الطلبات</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle orders-perm" type="checkbox" name="perm_orders_deliver" id="permOrdersDeliver" value="1"
                                                   {{ old('perm_orders_deliver', $user->perm_orders_deliver) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permOrdersDeliver">تسليم الطلبات</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Agents & Vaults --}}
                            <div class="mb-3">
                                <div class="fw-semibold text-muted small mb-2 text-uppercase">
                                    <i class="ti ti-motorbike me-1"></i> المندوبين والخزائن
                                </div>
                                <div class="row g-2">
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle" type="checkbox" name="perm_agents" id="permAgents" value="1"
                                                   {{ old('perm_agents', $user->perm_agents) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permAgents">إدارة المندوبين</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle" type="checkbox" name="perm_vaults" id="permVaults" value="1"
                                                   {{ old('perm_vaults', $user->perm_vaults) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permVaults">الخزائن المالية</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Products --}}
                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="fw-semibold text-muted small text-uppercase"><i class="ti ti-box me-1"></i> المنتجات</span>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1" style="font-size:.7rem" onclick="toggleGroup('products')">تحديد الكل</button>
                                </div>
                                <div class="row g-2">
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle products-perm" type="checkbox" name="perm_products_view" id="permProductsView" value="1"
                                                   {{ old('perm_products_view', $user->perm_products_view) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permProductsView">عرض المنتجات</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle products-perm" type="checkbox" name="perm_products_prices" id="permProductsPrices" value="1"
                                                   {{ old('perm_products_prices', $user->perm_products_prices) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permProductsPrices">رؤية الأسعار</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle products-perm" type="checkbox" name="perm_products_costs" id="permProductsCosts" value="1"
                                                   {{ old('perm_products_costs', $user->perm_products_costs) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permProductsCosts">رؤية التكاليف</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle products-perm" type="checkbox" name="perm_products_edit" id="permProductsEdit" value="1"
                                                   {{ old('perm_products_edit', $user->perm_products_edit) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permProductsEdit">إضافة وتعديل المنتجات</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle products-perm" type="checkbox" name="perm_products_stock" id="permProductsStock" value="1"
                                                   {{ old('perm_products_stock', $user->perm_products_stock) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permProductsStock">إدارة المخزون</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Marketers --}}
                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="fw-semibold text-muted small text-uppercase"><i class="ti ti-users me-1"></i> المسوقين</span>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1" style="font-size:.7rem" onclick="toggleGroup('marketers')">تحديد الكل</button>
                                </div>
                                <div class="row g-2">
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle marketers-perm" type="checkbox" name="perm_marketers_view" id="permMarketersView" value="1"
                                                   {{ old('perm_marketers_view', $user->perm_marketers_view) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permMarketersView">عرض المسوقين</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle marketers-perm" type="checkbox" name="perm_marketers_manage" id="permMarketersManage" value="1"
                                                   {{ old('perm_marketers_manage', $user->perm_marketers_manage) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permMarketersManage">قبول ورفض وتعديل</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input perm-toggle marketers-perm" type="checkbox" name="perm_marketers_finance" id="permMarketersFinance" value="1"
                                                   {{ old('perm_marketers_finance', $user->perm_marketers_finance) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permMarketersFinance">المعاملات المالية</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Reports --}}
                            <div class="mb-0">
                                <div class="fw-semibold text-muted small mb-2 text-uppercase">
                                    <i class="ti ti-chart-bar me-1"></i> التقارير
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input perm-toggle" type="checkbox" name="perm_reports" id="permReports" value="1"
                                           {{ old('perm_reports', $user->perm_reports) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permReports">عرض التقارير</label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i> حفظ التعديلات
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>

        </form>
    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
    function togglePerms(el) {
        const disabled = el.checked;
        document.querySelectorAll('.perm-toggle').forEach(input => {
            input.disabled = disabled;
            if (disabled) input.checked = false;
        });
        document.getElementById('granularPerms').style.opacity = disabled ? '0.4' : '1';
    }

    function toggleGroup(group) {
        const inputs = document.querySelectorAll('.' + group + '-perm');
        const allChecked = Array.from(inputs).every(i => i.checked);
        inputs.forEach(i => i.checked = !allChecked);
    }

    togglePerms(document.getElementById('isSuper'));
</script>
@endpush
