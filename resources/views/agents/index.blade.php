@extends('layouts.app')

@section('title', 'المندوبين')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">المندوبين</li>
@endsection

@section('content')

    {{-- Filter Bar --}}
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
                <form method="GET" action="{{ route('agents.index') }}">
                    <div class="row g-2 align-items-end">

                        <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label fw-semibold mb-1">الاسم</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text"
                                       name="filter[name]"
                                       class="form-control"
                                       placeholder="ابحث باسم المندوب..."
                                       value="{{ request('filter.name') }}">
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label fw-semibold mb-1">رقم الهاتف</label>
                            <input type="text"
                                   name="filter[phone]"
                                   class="form-control"
                                   placeholder="رقم الهاتف..."
                                   value="{{ request('filter.phone') }}">
                        </div>

                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="form-label fw-semibold mb-1">الحالة</label>
                            <select name="filter[is_active]" class="form-select">
                                <option value="">الكل</option>
                                <option value="1" {{ request('filter.is_active') === '1' ? 'selected' : '' }}>مفعّل</option>
                                <option value="0" {{ request('filter.is_active') === '0' ? 'selected' : '' }}>معطّل</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="form-label fw-semibold mb-1">الترتيب</label>
                            <select name="sort" class="form-select">
                                <option value="-created_at" {{ request('sort') === '-created_at' ? 'selected' : '' }}>الأحدث</option>
                                <option value="created_at"  {{ request('sort') === 'created_at'  ? 'selected' : '' }}>الأقدم</option>
                                <option value="name"        {{ request('sort') === 'name'        ? 'selected' : '' }}>الاسم أ-ي</option>
                                <option value="-name"       {{ request('sort') === '-name'       ? 'selected' : '' }}>الاسم ي-أ</option>
                            </select>
                        </div>

                        <div class="col-12 d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-filter me-1"></i> تصفية
                            </button>
                            <a href="{{ route('agents.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-refresh me-1"></i> إعادة تعيين
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div class="text-muted fs-14">
            إجمالي النتائج: <span class="fw-bold text-dark">{{ $agents->total() }}</span>
        </div>
        <a href="{{ route('agents.create') }}" class="btn btn-success">
            <i class="ti ti-plus me-1"></i> إضافة مندوب
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
            <i class="ti ti-circle-check fs-5"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Desktop Table --}}
    <div class="card d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>اسم المندوب</th>
                            <th>رقم الهاتف</th>
                            <th>الهاتف الاحتياطي</th>
                            <th class="text-center">العهدة</th>
                            <th class="text-center">الحالة</th>
                            <th>تاريخ الإضافة</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agents as $agent)
                            <tr>
                                <td class="text-muted">{{ $agent->id }}</td>
                                <td class="fw-semibold">{{ $agent->name }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <span>{{ $agent->phone }}</span>
                                        <a href="https://wa.me/218{{ substr(preg_replace('/\D/', '', $agent->phone), -7) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-success py-0 px-1"
                                           title="واتساب">
                                            <i class="ti ti-brand-whatsapp"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $agent->phone2 ?? '—' }}</td>
                                <td class="text-center fw-bold" style="color:#4a2619">
                                    {{ number_format($agent->balance, 2) }} د.ل
                                </td>
                                <td class="text-center">
                                    @if($agent->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">مفعّل</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">معطّل</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ dt($agent->created_at, false) }}</td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('agents.show', $agent) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="عرض">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('agents.edit', $agent) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="تعديل">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm {{ $agent->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                                title="{{ $agent->is_active ? 'تعطيل' : 'تفعيل' }}"
                                                onclick="confirmToggle({{ $agent->id }}, '{{ $agent->name }}', {{ $agent->is_active ? 'true' : 'false' }})">
                                            <i class="ti {{ $agent->is_active ? 'ti-toggle-right' : 'ti-toggle-left' }}"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                title="حذف"
                                                onclick="confirmDelete({{ $agent->id }}, '{{ $agent->name }}')">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="ti ti-mood-empty fs-40 d-block mb-2"></i>
                                    لا يوجد مندوبون مطابقون للبحث
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="d-md-none">
        @forelse($agents as $agent)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <h6 class="fw-bold mb-0">{{ $agent->name }}</h6>
                        @if($agent->is_active)
                            <span class="badge bg-success-subtle text-success border border-success-subtle">مفعّل</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">معطّل</span>
                        @endif
                    </div>

                    <div class="d-flex flex-column gap-1 mb-3">
                        <div class="d-flex align-items-center gap-2 fs-14">
                            <i class="ti ti-phone text-muted"></i>
                            <span>{{ $agent->phone }}</span>
                            <a href="https://wa.me/218{{ substr(preg_replace('/\D/', '', $agent->phone), -7) }}"
                               target="_blank"
                               class="badge bg-success-subtle text-success text-decoration-none">
                                <i class="ti ti-brand-whatsapp"></i>
                            </a>
                        </div>
                        @if($agent->phone2)
                            <div class="d-flex align-items-center gap-2 fs-14">
                                <i class="ti ti-phone-plus text-muted"></i>
                                <span class="text-muted">{{ $agent->phone2 }}</span>
                            </div>
                        @endif
                        <div class="d-flex align-items-center gap-2 fs-13 text-muted">
                            <i class="ti ti-calendar"></i>
                            <span>{{ dt($agent->created_at, false) }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 fs-14 mt-1">
                            <i class="ti ti-wallet text-muted"></i>
                            <span class="fw-bold" style="color:#4a2619">{{ number_format($agent->balance, 2) }} د.ل</span>
                            <span class="text-muted small">عهدة</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('agents.show', $agent) }}" class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="ti ti-eye me-1"></i> عرض
                        </a>
                        <a href="{{ route('agents.edit', $agent) }}" class="btn btn-sm btn-outline-warning flex-fill">
                            <i class="ti ti-pencil me-1"></i> تعديل
                        </a>
                        <button type="button"
                                class="btn btn-sm {{ $agent->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }} flex-fill"
                                onclick="confirmToggle({{ $agent->id }}, '{{ $agent->name }}', {{ $agent->is_active ? 'true' : 'false' }})">
                            <i class="ti {{ $agent->is_active ? 'ti-toggle-right' : 'ti-toggle-left' }} me-1"></i>
                            {{ $agent->is_active ? 'تعطيل' : 'تفعيل' }}
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-outline-danger flex-fill"
                                onclick="confirmDelete({{ $agent->id }}, '{{ $agent->name }}')">
                            <i class="ti ti-trash me-1"></i> حذف
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="ti ti-mood-empty fs-40 d-block mb-2"></i>
                لا يوجد مندوبون مطابقون للبحث
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($agents->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $agents->withQueryString()->links() }}
        </div>
    @endif

    {{-- Hidden Forms --}}
    <form id="toggleForm" method="POST" style="display:none">
        @csrf @method('PATCH')
    </form>
    <form id="deleteForm" method="POST" style="display:none">
        @csrf @method('DELETE')
    </form>

@endsection

@push('scripts')
<script>
    const filterCollapse = document.getElementById('filterCollapse');
    const collapseIcon   = document.querySelector('.collapse-icon');

    filterCollapse.addEventListener('show.bs.collapse', () => collapseIcon.style.transform = 'rotate(180deg)');
    filterCollapse.addEventListener('hide.bs.collapse', () => collapseIcon.style.transform = 'rotate(0deg)');

    if (filterCollapse.classList.contains('show')) {
        collapseIcon.style.transform = 'rotate(180deg)';
    }

    function confirmToggle(id, name, isActive) {
        Swal.fire({
            title: isActive ? 'تعطيل المندوب' : 'تفعيل المندوب',
            text: `هل أنت متأكد من ${isActive ? 'تعطيل' : 'تفعيل'} المندوب "${name}"؟`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isActive ? '#6c757d' : '#198754',
            cancelButtonColor: '#adb5bd',
            confirmButtonText: isActive ? 'نعم، عطّل' : 'نعم، فعّل',
            cancelButtonText: 'إلغاء',
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.getElementById('toggleForm');
                form.action = `/agents/${id}/toggle`;
                form.submit();
            }
        });
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'تأكيد الحذف',
            text: `هل أنت متأكد من حذف المندوب "${name}"؟`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء',
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = `/agents/${id}`;
                form.submit();
            }
        });
    }
</script>
@endpush
