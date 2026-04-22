@extends('layouts.app')

@section('title', 'المسوقين')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">المسوقين</li>
@endsection

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
            <form method="GET" action="{{ route('marketers.index') }}" id="filterForm">
                <div class="row g-2 align-items-end">

                    {{-- Search --}}
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label class="form-label fw-semibold mb-1">بحث شامل</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-search"></i></span>
                            <input type="text"
                                   name="filter[search]"
                                   class="form-control"
                                   placeholder="الاسم، الهاتف، البريد..."
                                   value="{{ request('filter.search') }}">
                        </div>
                    </div>

                    {{-- First Name --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label fw-semibold mb-1">الاسم الأول</label>
                        <input type="text"
                               name="filter[first_name]"
                               class="form-control"
                               placeholder="الاسم الأول"
                               value="{{ request('filter.first_name') }}">
                    </div>

                    {{-- Last Name --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label fw-semibold mb-1">الاسم الأخير</label>
                        <input type="text"
                               name="filter[last_name]"
                               class="form-control"
                               placeholder="الاسم الأخير"
                               value="{{ request('filter.last_name') }}">
                    </div>

                    {{-- Phone --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label fw-semibold mb-1">رقم الهاتف</label>
                        <input type="text"
                               name="filter[phone]"
                               class="form-control"
                               placeholder="رقم الهاتف"
                               value="{{ request('filter.phone') }}">
                    </div>

                    {{-- Status --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label fw-semibold mb-1">الحالة</label>
                        <select name="filter[is_active]" class="form-select">
                            <option value="">الكل</option>
                            <option value="1" {{ request('filter.is_active') === '1' ? 'selected' : '' }}>مفعّلة</option>
                            <option value="0" {{ request('filter.is_active') === '0' ? 'selected' : '' }}>موقوفة</option>
                        </select>
                    </div>

                    {{-- Sort --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label fw-semibold mb-1">الترتيب</label>
                        <select name="sort" class="form-select">
                            <option value="-created_at" {{ request('sort') === '-created_at' ? 'selected' : '' }}>الأحدث</option>
                            <option value="created_at"  {{ request('sort') === 'created_at'  ? 'selected' : '' }}>الأقدم</option>
                            <option value="first_name"  {{ request('sort') === 'first_name'  ? 'selected' : '' }}>الاسم أ-ي</option>
                            <option value="-first_name" {{ request('sort') === '-first_name' ? 'selected' : '' }}>الاسم ي-أ</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="col-12 d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-filter me-1"></i> تصفية
                        </button>
                        <a href="{{ route('marketers.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-refresh me-1"></i> إعادة تعيين
                        </a>
                    </div>

                </div>
            </form>
        </div>
        </div>
    </div>

    {{-- ===== Page Header (Count + Add Button) ===== --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div class="text-muted fs-14">
            إجمالي النتائج:
            <span class="fw-bold text-dark">{{ $marketers->total() }}</span>
        </div>
        <a href="{{ route('marketers.create') }}" class="btn btn-success">
            <i class="ti ti-plus me-1"></i> إضافة مسوق
        </a>
    </div>

    {{-- ===== Desktop Table (hidden on mobile) ===== --}}
    <div class="card d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم الكامل</th>
                            <th>الهاتف</th>
                            <th>الهاتف الاحتياطي</th>
                            <th>البريد الإلكتروني</th>
                            <th>الحالة</th>
                            <th>تاريخ الإضافة</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($marketers as $marketer)
                            <tr>
                                <td class="text-muted">{{ $marketer->id }}</td>
                                <td class="fw-semibold">{{ $marketer->first_name }} {{ $marketer->last_name }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <a href="tel:{{ $marketer->phone }}" class="text-decoration-none">
                                            <i class="ti ti-phone me-1 text-muted"></i>{{ $marketer->phone }}
                                        </a>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $marketer->phone) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-success py-0 px-1"
                                           title="واتساب">
                                            <i class="ti ti-brand-whatsapp"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    @if($marketer->backup_phone)
                                        <a href="tel:{{ $marketer->backup_phone }}" class="text-decoration-none text-muted">
                                            {{ $marketer->backup_phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($marketer->email)
                                        <a href="mailto:{{ $marketer->email }}" class="text-decoration-none">
                                            {{ $marketer->email }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($marketer->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning">قيد المراجعة</span>
                                    @elseif($marketer->status === 'approved')
                                        @if($marketer->is_active)
                                            <span class="badge bg-success-subtle text-success">مفعّل</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">موقوف</span>
                                        @endif
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">مرفوض</span>
                                    @endif
                                </td>
                                <td class="text-muted fs-13">{{ $marketer->created_at->format('Y/m/d') }}</td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('marketers.show', $marketer) }}"
                                           class="btn btn-sm btn-outline-info"
                                           title="عرض">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('marketers.edit', $marketer) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="تعديل">
                                            <i class="ti ti-pencil"></i>
                                        </a>

                                        @if($marketer->status === 'pending')
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-success"
                                                    title="قبول"
                                                    onclick="confirmApprove({{ $marketer->id }}, '{{ $marketer->first_name }} {{ $marketer->last_name }}')">
                                                <i class="ti ti-check"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="رفض"
                                                    onclick="confirmReject({{ $marketer->id }}, '{{ $marketer->first_name }} {{ $marketer->last_name }}')">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        @elseif($marketer->status === 'approved')
                                            <button type="button"
                                                    class="btn btn-sm {{ $marketer->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                                    title="{{ $marketer->is_active ? 'إيقاف' : 'تفعيل' }}"
                                                    onclick="confirmToggle({{ $marketer->id }}, '{{ $marketer->first_name }} {{ $marketer->last_name }}', {{ $marketer->is_active ? 'true' : 'false' }})">
                                                <i class="ti {{ $marketer->is_active ? 'ti-toggle-right' : 'ti-toggle-left' }}"></i>
                                            </button>
                                        @endif

                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                title="حذف"
                                                onclick="confirmDelete({{ $marketer->id }}, '{{ $marketer->first_name }} {{ $marketer->last_name }}')">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="ti ti-mood-empty fs-40 d-block mb-2"></i>
                                    لا توجد مسوقات مطابقة للبحث
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== Mobile Cards (hidden on desktop) ===== --}}
    <div class="d-md-none">
        @forelse($marketers as $marketer)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <h6 class="fw-bold mb-0">{{ $marketer->first_name }} {{ $marketer->last_name }}</h6>
                            <small class="text-muted">{{ $marketer->phone }}</small>
                        </div>
                        @if($marketer->status === 'pending')
                            <span class="badge bg-warning-subtle text-warning">قيد المراجعة</span>
                        @elseif($marketer->status === 'approved')
                            @if($marketer->is_active)
                                <span class="badge bg-success-subtle text-success">مفعّل</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">موقوف</span>
                            @endif
                        @else
                            <span class="badge bg-danger-subtle text-danger">مرفوض</span>
                        @endif
                    </div>

                    <div class="d-flex flex-column gap-1 mb-3">
                        <div class="d-flex align-items-center gap-2 fs-14">
                            <i class="ti ti-phone text-muted"></i>
                            <a href="tel:{{ $marketer->phone }}" class="text-decoration-none">{{ $marketer->phone }}</a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $marketer->phone) }}"
                               target="_blank"
                               class="badge bg-success-subtle text-success text-decoration-none">
                                <i class="ti ti-brand-whatsapp"></i>
                            </a>
                        </div>
                        @if($marketer->backup_phone)
                            <div class="d-flex align-items-center gap-2 fs-14">
                                <i class="ti ti-phone-plus text-muted"></i>
                                <a href="tel:{{ $marketer->backup_phone }}" class="text-decoration-none text-muted">{{ $marketer->backup_phone }}</a>
                            </div>
                        @endif
                        @if($marketer->email)
                            <div class="d-flex align-items-center gap-2 fs-14">
                                <i class="ti ti-mail text-muted"></i>
                                <a href="mailto:{{ $marketer->email }}" class="text-decoration-none text-muted">{{ $marketer->email }}</a>
                            </div>
                        @endif
                        <div class="d-flex align-items-center gap-2 fs-13 text-muted">
                            <i class="ti ti-calendar"></i>
                            <span>{{ $marketer->created_at->format('Y/m/d') }}</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('marketers.show', $marketer) }}" class="btn btn-sm btn-outline-info flex-fill">
                            <i class="ti ti-eye me-1"></i> عرض
                        </a>
                        <a href="{{ route('marketers.edit', $marketer) }}" class="btn btn-sm btn-outline-warning flex-fill">
                            <i class="ti ti-pencil me-1"></i> تعديل
                        </a>

                        @if($marketer->status === 'pending')
                            <button type="button"
                                    class="btn btn-sm btn-outline-success flex-fill"
                                    onclick="confirmApprove({{ $marketer->id }}, '{{ $marketer->first_name }} {{ $marketer->last_name }}')">
                                <i class="ti ti-check me-1"></i> قبول
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger flex-fill"
                                    onclick="confirmReject({{ $marketer->id }}, '{{ $marketer->first_name }} {{ $marketer->last_name }}')">
                                <i class="ti ti-x me-1"></i> رفض
                            </button>
                        @elseif($marketer->status === 'approved')
                            <button type="button"
                                    class="btn btn-sm {{ $marketer->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }} flex-fill"
                                    onclick="confirmToggle({{ $marketer->id }}, '{{ $marketer->first_name }} {{ $marketer->last_name }}', {{ $marketer->is_active ? 'true' : 'false' }})">
                                <i class="ti {{ $marketer->is_active ? 'ti-toggle-right' : 'ti-toggle-left' }} me-1"></i>
                                {{ $marketer->is_active ? 'إيقاف' : 'تفعيل' }}
                            </button>
                        @endif

                        <button type="button"
                                class="btn btn-sm btn-outline-danger flex-fill"
                                onclick="confirmDelete({{ $marketer->id }}, '{{ $marketer->first_name }} {{ $marketer->last_name }}')">
                            <i class="ti ti-trash me-1"></i> حذف
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="ti ti-mood-empty fs-40 d-block mb-2"></i>
                لا توجد مسوقات مطابقة للبحث
            </div>
        @endforelse
    </div>

    {{-- ===== Pagination ===== --}}
    @if($marketers->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $marketers->withQueryString()->links() }}
        </div>
    @endif

    {{-- Delete Form (hidden) --}}
    <form id="deleteForm" method="POST" style="display:none">
        @csrf
        @method('DELETE')
    </form>

    {{-- Toggle Form (hidden) --}}
    <form id="toggleForm" method="POST" style="display:none">
        @csrf
        @method('PATCH')
    </form>

    {{-- Approve / Reject Forms (hidden) --}}
    <form id="approveForm" method="POST" style="display:none">
        @csrf
        @method('PATCH')
    </form>
    <form id="rejectForm" method="POST" style="display:none">
        @csrf
        @method('PATCH')
    </form>

@endsection

@push('scripts')
<script>
    // تدوير أيقونة السهم عند فتح/إغلاق الفلتر
    const filterCollapse = document.getElementById('filterCollapse');
    const collapseIcon   = document.querySelector('.collapse-icon');

    filterCollapse.addEventListener('show.bs.collapse',  () => collapseIcon.style.transform = 'rotate(180deg)');
    filterCollapse.addEventListener('hide.bs.collapse',  () => collapseIcon.style.transform = 'rotate(0deg)');

    // إذا كان مفتوحاً من البداية (يوجد فلتر مفعّل)
    if (filterCollapse.classList.contains('show')) {
        collapseIcon.style.transform = 'rotate(180deg)';
    }

    function confirmApprove(id, name) {
        Swal.fire({
            title: 'قبول المسوق',
            html: `هل تريد قبول طلب <strong>${name}</strong> وتفعيل حسابه؟`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="ti ti-check me-1"></i> نعم، اقبل',
            cancelButtonText: 'إلغاء',
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.getElementById('approveForm');
                form.action = `/marketers/${id}/approve`;
                form.submit();
            }
        });
    }

    function confirmReject(id, name) {
        Swal.fire({
            title: 'رفض المسوق',
            html: `هل تريد رفض طلب <strong>${name}</strong>؟<br><small class="text-muted">يمكن قبوله لاحقاً</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="ti ti-x me-1"></i> نعم، ارفض',
            cancelButtonText: 'إلغاء',
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.getElementById('rejectForm');
                form.action = `/marketers/${id}/reject`;
                form.submit();
            }
        });
    }

    function confirmToggle(id, name, isActive) {
        Swal.fire({
            title: isActive ? 'إيقاف المسوق' : 'تفعيل المسوق',
            text: `هل أنت متأكد من ${isActive ? 'إيقاف' : 'تفعيل'} المسوق "${name}"؟`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isActive ? '#6c757d' : '#198754',
            cancelButtonColor: '#adb5bd',
            confirmButtonText: isActive ? 'نعم، أوقف' : 'نعم، فعّل',
            cancelButtonText: 'إلغاء',
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('toggleForm');
                form.action = `/marketers/${id}/toggle`;
                form.submit();
            }
        });
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'تأكيد الحذف',
            text: `هل أنت متأكد من حذف المسوق "${name}"؟`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء',
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = `/marketers/${id}`;
                form.submit();
            }
        });
    }
</script>
@endpush
