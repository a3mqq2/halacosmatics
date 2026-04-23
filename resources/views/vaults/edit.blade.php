@extends('layouts.app')

@section('title', 'تعديل الخزينة')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('vaults.index') }}">الخزائن المالية</a></li>
    <li class="breadcrumb-item active">تعديل — {{ $vault->name }}</li>
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-12">

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold mb-0" style="color:#4a2619">
                        <i class="ti ti-cash-banknote me-1"></i> تعديل الخزينة
                    </h5>
                </div>

                {{-- Current balances (read-only) --}}
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="rounded-3 p-3 text-center" style="background:#fdfaf9;border:1.5px solid #f0ebe8">
                            <div class="text-muted small fw-semibold mb-1">الرصيد الافتتاحي</div>
                            <div class="fw-bold">{{ number_format($vault->opening_balance, 2) }} <span class="text-muted small">د.ل</span></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="rounded-3 p-3 text-center" style="background:#fdf6f3;border:1.5px solid #e8d5cc">
                            <div class="text-muted small fw-semibold mb-1">الرصيد الحالي</div>
                            <div class="fw-bold" style="color:#4a2619">{{ number_format($vault->current_balance, 2) }} <span class="text-muted small">د.ل</span></div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('vaults.update', $vault) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">اسم الخزينة <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $vault->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">كود الخزينة <span class="text-muted small fw-normal">(اختياري)</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code', $vault->code) }}" placeholder="مثال: BNK للخزينة المصرفية" style="text-transform:uppercase">
                        <div class="form-text">يُستخدم الكود للتعرف على الخزينة برمجياً. مثال: BNK للخزينة المصرفية.</div>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i> حفظ التعديلات
                        </button>
                        <a href="{{ route('vaults.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
