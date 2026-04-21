@extends('layouts.app')

@section('title', 'تعديل المندوب')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agents.index') }}">المندوبين</a></li>
    <li class="breadcrumb-item active">تعديل — {{ $agent->name }}</li>
@endsection

@section('content')

<form method="POST" action="{{ route('agents.update', $agent) }}" novalidate>
    @csrf @method('PUT')

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
                <i class="ti ti-pencil me-2"></i>تعديل بيانات المندوب
            </h5>
            @if($agent->is_active)
                <span class="badge bg-success-subtle text-success border border-success-subtle">مفعّل</span>
            @else
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">معطّل</span>
            @endif
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3">

                <div class="col-12 col-sm-6">
                    <label class="form-label">اسم المندوب <span class="text-danger">*</span></label>
                    <input type="text"
                           name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $agent->name) }}"
                           placeholder="الاسم الكامل">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                    <input type="text"
                           name="phone"
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $agent->phone) }}"
                           placeholder="09xxxxxxx"
                           inputmode="numeric">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label">الهاتف الاحتياطي</label>
                    <input type="text"
                           name="phone2"
                           class="form-control @error('phone2') is-invalid @enderror"
                           value="{{ old('phone2', $agent->phone2) }}"
                           placeholder="اختياري"
                           inputmode="numeric">
                    @error('phone2')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


            </div>

        </div>
        <div class="card-footer d-flex gap-2 justify-content-end">
            <a href="{{ route('agents.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-right me-1"></i> رجوع
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy me-1"></i> حفظ التعديلات
            </button>
        </div>
    </div>

</form>

@endsection
