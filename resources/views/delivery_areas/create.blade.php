@extends('layouts.app')

@section('title', 'إضافة منطقة توصيل')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('delivery_areas.index') }}">مناطق التوصيل</a></li>
    <li class="breadcrumb-item active">منطقة جديدة</li>
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4" style="color:#4a2619">
                    <i class="ti ti-map-pin me-1"></i> منطقة توصيل جديدة
                </h5>

                <form method="POST" action="{{ route('delivery_areas.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">اسم المنطقة <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="مثال: السلماني الشرقي" required autofocus>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">سعر التوصيل <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="price" min="0" max="9999"
                                   class="form-control @error('price') is-invalid @enderror"
                                   value="{{ old('price', 15) }}" required>
                            <span class="input-group-text">د.ل</span>
                        </div>
                        @error('price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i> إضافة المنطقة
                        </button>
                        <a href="{{ route('delivery_areas.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
