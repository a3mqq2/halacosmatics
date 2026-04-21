@extends('layouts.app')

@section('title', $product->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">المنتجات</a></li>
    <li class="breadcrumb-item active">{{ $product->name }}</li>
@endsection

@push('styles')
<style>
    .qty-badge {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1;
    }
    .log-row-add      { border-right: 3px solid #16a34a; }
    .log-row-subtract { border-right: 3px solid #dc3545; }
</style>
@endpush

@section('content')

    {{-- ===== Header ===== --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3">

                @if($product->primaryImage)
                    <img src="{{ $product->primaryImage->url }}" alt="{{ $product->name }}"
                         style="width:120px;height:120px;object-fit:cover;border-radius:16px;border:1px solid #e5e7eb;flex-shrink:0">
                @else
                    <div style="width:120px;height:120px;border-radius:16px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:44px;color:#d1d5db;border:1px solid #e5e7eb;flex-shrink:0">
                        <i class="ti ti-photo"></i>
                    </div>
                @endif

                <div class="flex-grow-1">
                    <h4 class="fw-bold mb-1">{{ $product->name }}</h4>
                    @if($product->code)
                        <code class="text-muted">{{ $product->code }}</code>
                    @endif
                    <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
                        @if($product->is_active)
                            <span class="badge bg-success-subtle text-success">مفعّل</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">موقوف</span>
                        @endif
                        <span class="text-muted small">الكمية الحالية:</span>
                        <span class="qty-badge text-primary">{{ number_format($product->quantity) }}</span>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-shrink-0 flex-wrap">
                    <button type="button" class="btn btn-success btn-sm"
                            data-bs-toggle="modal" data-bs-target="#addQtyModal">
                        <i class="ti ti-circle-plus me-1"></i> إضافة كمية
                    </button>
                    <button type="button" class="btn btn-danger btn-sm"
                            data-bs-toggle="modal" data-bs-target="#subtractQtyModal"
                            {{ $product->quantity === 0 ? 'disabled' : '' }}>
                        <i class="ti ti-circle-minus me-1"></i> خصم كمية
                    </button>
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-primary btn-sm">
                        <i class="ti ti-edit me-1"></i> تعديل
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-{{ $product->is_active ? 'warning' : 'success' }}"
                            onclick="confirmToggle({{ $product->id }}, '{{ $product->name }}', {{ $product->is_active ? 'true' : 'false' }})">
                        <i class="ti ti-{{ $product->is_active ? 'player-pause' : 'player-play' }} me-1"></i>
                        {{ $product->is_active ? 'إيقاف' : 'تفعيل' }}
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- ===== Product Info ===== --}}
        <div class="col-12 col-lg-5">
            <div class="card h-100">
                <div class="card-header fw-semibold">
                    <i class="ti ti-info-circle me-1"></i> معلومات المنتج
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">

                        <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                            <i class="ti ti-tag text-muted"></i>
                            <span class="text-muted small">الاسم</span>
                            <span class="ms-auto fw-semibold">{{ $product->name }}</span>
                        </li>

                        <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                            <i class="ti ti-barcode text-muted"></i>
                            <span class="text-muted small">الكود</span>
                            <span class="ms-auto">
                                @if($product->code) <code>{{ $product->code }}</code>
                                @else <span class="text-muted">—</span> @endif
                            </span>
                        </li>

                        <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                            <i class="ti ti-stack text-muted"></i>
                            <span class="text-muted small">الكمية الحالية</span>
                            <span class="ms-auto fw-bold text-primary">{{ number_format($product->quantity) }}</span>
                        </li>

                        <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                            <i class="ti ti-coin text-muted"></i>
                            <span class="text-muted small">سعر البيع</span>
                            <span class="ms-auto fw-semibold">{{ number_format($product->price) }} د.ل</span>
                        </li>

                        <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                            <i class="ti ti-receipt text-muted"></i>
                            <span class="text-muted small">سعر التكلفة</span>
                            <span class="ms-auto fw-semibold">{{ number_format($product->cost_price) }} د.ل</span>
                        </li>

                        @if($product->cost_price > 0)
                        <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                            <i class="ti ti-trending-up text-muted"></i>
                            <span class="text-muted small">هامش الربح</span>
                            <span class="ms-auto fw-semibold text-success">
                                {{ number_format($product->price - $product->cost_price) }} د.ل
                                <span class="text-muted small">
                                    ({{ number_format(($product->price - $product->cost_price) / $product->cost_price * 100, 1) }}%)
                                </span>
                            </span>
                        </li>
                        @endif

                        <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                            <i class="ti ti-toggle-right text-muted"></i>
                            <span class="text-muted small">الحالة</span>
                            <span class="ms-auto">
                                @if($product->is_active)
                                    <span class="badge bg-success-subtle text-success">مفعّل</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">موقوف</span>
                                @endif
                            </span>
                        </li>

                        <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                            <i class="ti ti-calendar-plus text-muted"></i>
                            <span class="text-muted small">تاريخ الإضافة</span>
                            <span class="ms-auto small">{{ dt($product->created_at) }}</span>
                        </li>

                        <li class="d-flex align-items-center gap-2 py-2 {{ $product->description ? 'border-bottom' : '' }}">
                            <i class="ti ti-calendar-event text-muted"></i>
                            <span class="text-muted small">آخر تحديث</span>
                            <span class="ms-auto small">{{ dt($product->updated_at) }}</span>
                        </li>

                        @if($product->description)
                        <li class="py-2">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="ti ti-align-left text-muted"></i>
                                <span class="text-muted small">الوصف</span>
                            </div>
                            <p class="mb-0 text-dark" style="white-space: pre-wrap">{{ $product->description }}</p>
                        </li>
                        @endif

                    </ul>
                </div>
            </div>
        </div>

        {{-- ===== Quantity Actions + Log ===== --}}
        <div class="col-12 col-lg-7">



            {{-- Quantity Log --}}
            <div class="card">
                <div class="card-header fw-semibold">
                    <i class="ti ti-history me-1"></i> آخر حركات الكمية
                    <span class="badge bg-secondary ms-1">{{ $quantityLogs->count() }}</span>
                </div>
                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>النوع</th>
                                <th>الكمية</th>
                                <th>الرصيد بعد</th>
                                <th>المستخدم</th>
                                <th>ملاحظة</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quantityLogs as $log)
                            <tr class="log-row-{{ $log->type }}">
                                <td>
                                    @if($log->type === 'add')
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="ti ti-arrow-up me-1"></i> إضافة
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">
                                            <i class="ti ti-arrow-down me-1"></i> خصم
                                        </span>
                                    @endif
                                </td>
                                <td class="fw-bold {{ $log->type === 'add' ? 'text-success' : 'text-danger' }}">
                                    {{ $log->type === 'add' ? '+' : '-' }}{{ number_format($log->quantity) }}
                                </td>
                                <td class="fw-semibold">{{ number_format($log->quantity_after) }}</td>
                                <td class="small text-muted">{{ $log->user->name ?? '—' }}</td>
                                <td class="small text-muted">{{ $log->notes ?? '—' }}</td>
                                <td class="small text-muted" style="white-space: nowrap">
                                    {{ dt($log->created_at) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="ti ti-history-off d-block fs-3 mb-1"></i>
                                    لا توجد حركات مسجلة
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    {{-- Hidden Forms --}}
    <form id="toggleForm" method="POST" style="display:none">
        @csrf @method('PATCH')
    </form>

    {{-- ===== Add Quantity Modal ===== --}}
    <div class="modal fade" id="addQtyModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('products.add-quantity', $product) }}">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-success">
                            <i class="ti ti-circle-plus me-1"></i> إضافة كمية
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            المنتج: <strong>{{ $product->name }}</strong> —
                            الكمية الحالية: <strong class="text-primary">{{ number_format($product->quantity) }}</strong>
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">الكمية المضافة <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" min="1"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   autofocus required>
                            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-semibold">ملاحظة <span class="text-muted small">(اختياري)</span></label>
                            <input type="text" name="notes" class="form-control" maxlength="255">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-plus me-1"></i> إضافة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== Subtract Quantity Modal ===== --}}
    <div class="modal fade" id="subtractQtyModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('products.subtract-quantity', $product) }}">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger">
                            <i class="ti ti-circle-minus me-1"></i> خصم كمية
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            المنتج: <strong>{{ $product->name }}</strong> —
                            الكمية الحالية: <strong class="text-primary">{{ number_format($product->quantity) }}</strong>
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">الكمية المخصومة <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" min="1" max="{{ $product->quantity }}"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   required>
                            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-semibold">ملاحظة <span class="text-muted small">(اختياري)</span></label>
                            <input type="text" name="notes" class="form-control" maxlength="255">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="ti ti-minus me-1"></i> خصم
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function confirmToggle(id, name, isActive) {
        const action = isActive ? 'إيقاف' : 'تفعيل';
        Swal.fire({
            title: `${action} المنتج`,
            html: `هل تريد <strong>${action}</strong> المنتج: <strong>${name}</strong>؟`,
            icon: isActive ? 'warning' : 'question',
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
