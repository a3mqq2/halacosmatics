@extends('layouts.app')

@section('title', 'إضافة منتج')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">المنتجات</a></li>
    <li class="breadcrumb-item active">إضافة منتج</li>
@endsection

@push('styles')
<style>
    .drop-zone {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 28px 20px;
        text-align: center;
        cursor: pointer;
        transition: border-color .2s, background .2s;
        background: #fafafa;
        position: relative;
    }
    .drop-zone.dragover { border-color: #4a2619; background: #fdf4f1; }
    .drop-zone input[type="file"] {
        position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    .img-preview-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px; }
    .img-preview-item {
        position: relative; width: 90px; height: 90px; border-radius: 10px;
        overflow: hidden; border: 2px solid #e5e7eb; cursor: pointer;
        transition: border-color .2s;
    }
    .img-preview-item img { width: 100%; height: 100%; object-fit: cover; }
    .img-preview-item.primary { border-color: #4a2619; }
    .img-preview-item .primary-badge {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: rgba(74,38,25,.85); color: #fff;
        font-size: .6rem; text-align: center; padding: 2px 0;
    }
    .img-preview-item .remove-btn {
        position: absolute; top: 3px; right: 3px;
        background: rgba(220,38,38,.85); color: #fff;
        border: none; border-radius: 50%; width: 20px; height: 20px;
        font-size: 11px; line-height: 20px; cursor: pointer; padding: 0;
        display: flex; align-items: center; justify-content: center;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
<div class="col-12">
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 fw-bold"><i class="ti ti-plus me-1"></i> إضافة منتج جديد</h5>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form id="productForm" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">

                <div class="col-12 col-sm-8">
                    <label class="form-label fw-semibold">اسم المنتج <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-sm-4">
                    <label class="form-label fw-semibold">الكود <span class="text-muted small">(اختياري)</span></label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                           value="{{ old('code') }}">
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-sm-4">
                    <label class="form-label fw-semibold">الكمية المبدئية <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                           value="{{ old('quantity', 0) }}" min="0" required>
                    @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-sm-4">
                    <label class="form-label fw-semibold">سعر البيع (د.ل) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                           value="{{ old('price', 0) }}" min="0" required>
                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-sm-4">
                    <label class="form-label fw-semibold">سعر التكلفة (د.ل) <span class="text-danger">*</span></label>
                    <input type="number" name="cost_price" class="form-control @error('cost_price') is-invalid @enderror"
                           value="{{ old('cost_price', 0) }}" min="0" required>
                    @error('cost_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">الوصف <span class="text-muted small">(اختياري)</span></label>
                    <textarea name="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                               {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="isActive">مفعّل</label>
                    </div>
                </div>

                {{-- Multi Image Upload --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        صور المنتج <span class="text-muted small">(حتى 10 صور — اضغط على صورة لتعيينها كصورة رئيسية)</span>
                    </label>
                    <div class="drop-zone" id="dropZone">
                        <input type="file" id="imageInput" accept="image/*" multiple>
                        <i class="ti ti-photos fs-2 text-muted d-block mb-1"></i>
                        <p class="mb-0 text-muted small">اسحب الصور هنا أو اضغط للاختيار</p>
                        <p class="mb-0 text-muted" style="font-size:.75rem">JPG, PNG, WebP — حجم أقصى 5 MB للصورة</p>
                    </div>
                    <div class="img-preview-grid" id="previewGrid"></div>
                    <input type="hidden" name="primary_index" id="primaryIndex" value="0">
                    @error('images.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

            </div>

            <div id="imagesContainer" style="display:none"></div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="ti ti-device-floppy me-1"></i> حفظ المنتج
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>

        </form>
    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
    const dropZone    = document.getElementById('dropZone');
    const imageInput  = document.getElementById('imageInput');
    const previewGrid = document.getElementById('previewGrid');
    const primaryIdx  = document.getElementById('primaryIndex');

    let files = [];

    function renderPreviews() {
        previewGrid.innerHTML = '';
        const dt = new DataTransfer();
        files.forEach((file, i) => {
            dt.items.add(file);
            const url = URL.createObjectURL(file);
            const isPrimary = i === parseInt(primaryIdx.value);

            const item = document.createElement('div');
            item.className = 'img-preview-item' + (isPrimary ? ' primary' : '');
            item.innerHTML = `
                <img src="${url}" alt="">
                ${isPrimary ? '<div class="primary-badge">رئيسية</div>' : ''}
                <button type="button" class="remove-btn" data-i="${i}"><i class="ti ti-x"></i></button>
            `;
            item.addEventListener('click', (e) => {
                if (e.target.closest('.remove-btn')) return;
                primaryIdx.value = i;
                renderPreviews();
            });
            item.querySelector('.remove-btn').addEventListener('click', () => {
                files.splice(i, 1);
                if (parseInt(primaryIdx.value) >= files.length) primaryIdx.value = 0;
                renderPreviews();
            });
            previewGrid.appendChild(item);
        });

        // sync hidden file input
        const container = document.getElementById('imagesContainer');
        container.innerHTML = '';
        files.forEach((file, i) => {
            const dt2 = new DataTransfer();
            dt2.items.add(file);
            const input = document.createElement('input');
            input.type = 'file';
            input.name = 'images[]';
            input.style.display = 'none';
            input.files = dt2.files;
            container.appendChild(input);
        });
    }

    imageInput.addEventListener('change', () => {
        Array.from(imageInput.files).forEach(f => { if (files.length < 10) files.push(f); });
        renderPreviews();
    });

    dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        Array.from(e.dataTransfer.files).forEach(f => { if (files.length < 10) files.push(f); });
        renderPreviews();
    });
</script>
@endpush
