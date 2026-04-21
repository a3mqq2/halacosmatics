@extends('layouts.app')

@section('title', 'إنشاء خزينة')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('vaults.index') }}">الخزائن المالية</a></li>
    <li class="breadcrumb-item active">خزينة جديدة</li>
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-12">

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4" style="color:#4a2619">
                    <i class="ti ti-cash-banknote me-1"></i> خزينة جديدة
                </h5>

                <form method="POST" action="{{ route('vaults.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">اسم الخزينة <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="مثال: الخزينة الرئيسية" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">الرصيد الافتتاحي</label>
                        <div class="input-group">
                            <input type="number" name="opening_balance" step="0.01" min="0"
                                   class="form-control @error('opening_balance') is-invalid @enderror"
                                   value="{{ old('opening_balance', 0) }}">
                            <span class="input-group-text">د.ل</span>
                        </div>
                        <div class="form-text">سيكون الرصيد الحالي مساوياً للرصيد الافتتاحي عند الإنشاء.</div>
                        @error('opening_balance') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i> إنشاء الخزينة
                        </button>
                        <a href="{{ route('vaults.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
