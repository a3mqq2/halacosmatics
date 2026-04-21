@extends('layouts.app')

@section('title', 'المنتجات')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">المنتجات</li>
@endsection

@push('styles')
<style>
    .product-img {
        width: 48px; height: 48px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
    }
    .product-img-placeholder {
        width: 48px; height: 48px;
        border-radius: 10px;
        background: #f3f4f6;
        display: flex; align-items: center; justify-content: center;
        color: #9ca3af; font-size: 20px;
        border: 1px solid #e5e7eb;
    }
</style>
@endpush

@section('content')

    {{-- ===== Filter Bar ===== --}}
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between py-2"
             style="cursor:pointer"
             data-bs-toggle="collapse"
             data-bs-target="#filterCollapse"
             aria-expanded="{{ request()->hasAny(['filter', 'sort']) ? 'true' : 'false' }}"
             aria-controls="filterCollapse">
            <span class="fw-semibold">
                <i class="ti ti-filter me-1"></i> خيارات البحث والتصفية
            </span>
            <i class="ti ti-chevron-down collapse-icon" style="transition: transform 0.2s"></i>
        </div>
        <div class="collapse {{ request()->hasAny(['filter', 'sort']) ? 'show' : '' }}" id="filterCollapse">
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}" id="filterForm">
                <div class="row g-2 align-items-end">

                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label small mb-1">اسم المنتج</label>
                        <input type="text" name="filter[name]" class="form-control form-control-sm"
                               value="{{ request('filter.name') }}">
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label small mb-1">الكود</label>
                        <input type="text" name="filter[code]" class="form-control form-control-sm"
                               value="{{ request('filter.code') }}">
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label small mb-1">الحالة</label>
                        <select name="filter[is_active]" class="form-select form-select-sm">
                            <option value="">الكل</option>
                            <option value="1" {{ request('filter.is_active') === '1' ? 'selected' : '' }}>مفعّل</option>
                            <option value="0" {{ request('filter.is_active') === '0' ? 'selected' : '' }}>موقوف</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="ti ti-search me-1"></i> بحث
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="ti ti-x"></i>
                        </a>
                    </div>

                </div>
            </form>
        </div>
        </div>
    </div>

    {{-- ===== Header ===== --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0 fw-bold">
            <i class="ti ti-box me-1"></i> المنتجات
            <span class="badge bg-secondary ms-1">{{ $products->total() }}</span>
        </h5>
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
            <i class="ti ti-plus me-1"></i> إضافة منتج
        </a>
    </div>

    {{-- ===== Desktop Table ===== --}}
    <div class="card d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>اسم المنتج</th>
                        <th>الكود</th>
                        <th>الكمية</th>
                        @if(Auth::user()->can_access('products.prices'))
                        <th>السعر</th>
                        @endif
                        <th>الحالة</th>
                        <th>تاريخ الإضافة</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="text-muted small">{{ $product->id }}</td>
                        <td>
                            @if($product->primaryImage)
                                <img src="{{ $product->primaryImage->url }}" alt="{{ $product->name }}" class="product-img">
                            @else
                                <div class="product-img-placeholder">
                                    <i class="ti ti-photo"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $product->name }}</td>
                        <td>
                            @if($product->code)
                                <code class="text-muted">{{ $product->code }}</code>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ number_format($product->quantity) }}</td>
                        @if(Auth::user()->can_access('products.prices'))
                        <td>{{ number_format($product->price) }} د.ل</td>
                        @endif
                        <td>
                            @if($product->is_active)
                                <span class="badge bg-success-subtle text-success">مفعّل</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">موقوف</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $product->created_at->format('Y/m/d') }}</td>
                        <td>
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-info" title="عرض">
                                    <i class="ti ti-eye"></i>
                                </a>
                                @if(Auth::user()->can_access('products.edit'))
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="تعديل">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-{{ $product->is_active ? 'warning' : 'success' }}"
                                        title="{{ $product->is_active ? 'إيقاف' : 'تفعيل' }}"
                                        onclick="confirmToggle({{ $product->id }}, '{{ $product->name }}', {{ $product->is_active ? 'true' : 'false' }})">
                                    <i class="ti ti-{{ $product->is_active ? 'player-pause' : 'player-play' }}"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="حذف"
                                        onclick="confirmDelete({{ $product->id }}, '{{ $product->name }}')">
                                    <i class="ti ti-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="ti ti-box-off fs-2 d-block mb-2"></i>
                            لا توجد منتجات
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== Mobile Cards ===== --}}
    <div class="d-md-none">
        @forelse($products as $product)
        <div class="card mb-3">
            <div class="card-body">

                {{-- Image centered --}}
                <div class="text-center mb-3">
                    @if($product->primaryImage)
                        <img src="{{ $product->primaryImage->url }}" alt="{{ $product->name }}"
                             style="width:110px;height:110px;object-fit:cover;border-radius:16px;border:1px solid #e5e7eb">
                    @else
                        <div style="width:110px;height:110px;border-radius:16px;background:#f3f4f6;display:inline-flex;align-items:center;justify-content:center;font-size:40px;color:#d1d5db;border:1px solid #e5e7eb">
                            <i class="ti ti-photo"></i>
                        </div>
                    @endif
                </div>

                {{-- Name + badge --}}
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div>
                        <div class="fw-bold">{{ $product->name }}</div>
                        @if($product->code)
                            <code class="text-muted small">{{ $product->code }}</code>
                        @endif
                    </div>
                    @if($product->is_active)
                        <span class="badge bg-success-subtle text-success">مفعّل</span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary">موقوف</span>
                    @endif
                </div>

                <div class="row g-2 small text-muted mb-3">
                    <div class="col-6">
                        <i class="ti ti-stack me-1"></i> الكمية: <strong class="text-dark">{{ number_format($product->quantity) }}</strong>
                    </div>
                    @if(Auth::user()->can_access('products.prices'))
                    <div class="col-6">
                        <i class="ti ti-coin me-1"></i> السعر: <strong class="text-dark">{{ number_format($product->price) }} د.ل</strong>
                    </div>
                    @endif
                    <div class="col-12">
                        <i class="ti ti-calendar me-1"></i> {{ $product->created_at->format('Y/m/d') }}
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-info flex-fill">
                        <i class="ti ti-eye me-1"></i> عرض
                    </a>
                    @if(Auth::user()->can_access('products.edit'))
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="ti ti-edit me-1"></i> تعديل
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-{{ $product->is_active ? 'warning' : 'success' }}"
                            onclick="confirmToggle({{ $product->id }}, '{{ $product->name }}', {{ $product->is_active ? 'true' : 'false' }})">
                        <i class="ti ti-{{ $product->is_active ? 'player-pause' : 'player-play' }}"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="confirmDelete({{ $product->id }}, '{{ $product->name }}')">
                        <i class="ti ti-trash"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">
            <i class="ti ti-box-off fs-2 d-block mb-2"></i>
            لا توجد منتجات
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $products->links() }}
        </div>
    @endif

    {{-- Hidden Forms --}}
    <form id="deleteForm" method="POST" style="display:none">
        @csrf @method('DELETE')
    </form>
    <form id="toggleForm" method="POST" style="display:none">
        @csrf @method('PATCH')
    </form>

@endsection

@push('scripts')
<script>
    // Chevron animation
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(el) {
        var target = document.querySelector(el.getAttribute('data-bs-target'));
        if (!target) return;
        target.addEventListener('show.bs.collapse', function() {
            el.querySelector('.collapse-icon').style.transform = 'rotate(180deg)';
        });
        target.addEventListener('hide.bs.collapse', function() {
            el.querySelector('.collapse-icon').style.transform = 'rotate(0deg)';
        });
        if (target.classList.contains('show')) {
            el.querySelector('.collapse-icon').style.transform = 'rotate(180deg)';
        }
    });

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'حذف المنتج',
            html: `هل أنت متأكد من حذف <strong>${name}</strong>؟`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء',
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = `/products/${id}`;
                form.submit();
            }
        });
    }

    function confirmToggle(id, name, isActive) {
        const action = isActive ? 'إيقاف' : 'تفعيل';
        const icon   = isActive ? 'warning' : 'question';
        Swal.fire({
            title: `${action} المنتج`,
            html: `هل تريد <strong>${action}</strong> المنتج: <strong>${name}</strong>؟`,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: isActive ? '#f59e0b' : '#16a34a',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `نعم، ${action}`,
            cancelButtonText: 'إلغاء',
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.getElementById('toggleForm');
                form.action = `/products/${id}/toggle`;
                form.submit();
            }
        });
    }
</script>
@endpush
