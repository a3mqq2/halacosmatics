@extends('layouts.app')

@section('title', 'تعديل منتج')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">المنتجات</a></li>
    <li class="breadcrumb-item active">تعديل: {{ $product->name }}</li>
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
    .img-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px; }
    .img-item {
        position: relative; width: 90px; height: 90px; border-radius: 10px;
        overflow: hidden; border: 2px solid #e5e7eb; cursor: pointer;
        transition: border-color .2s;
    }
    .img-item img { width: 100%; height: 100%; object-fit: cover; }
    .img-item.primary { border-color: #4a2619; }
    .img-item.deleted { opacity: .35; border-color: #dc2626; }
    .img-item .primary-badge {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: rgba(74,38,25,.85); color: #fff;
        font-size: .6rem; text-align: center; padding: 2px 0;
    }
    .img-item .remove-btn {
        position: absolute; top: 3px; right: 3px;
        background: rgba(220,38,38,.85); color: #fff;
        border: none; border-radius: 50%; width: 20px; height: 20px;
        font-size: 11px; cursor: pointer; padding: 0;
        display: flex; align-items: center; justify-content: center;
    }
    .img-item .restore-btn {
        position: absolute; top: 3px; right: 3px;
        background: rgba(16,185,129,.85); color: #fff;
        border: none; border-radius: 50%; width: 20px; height: 20px;
        font-size: 11px; cursor: pointer; padding: 0;
        display: flex; align-items: center; justify-content: center;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
<div class="col-md-12">
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 fw-bold"><i class="ti ti-edit me-1"></i> تعديل: {{ $product->name }}</h5>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form id="productForm" method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="row g-3">

                <div class="col-12 col-sm-8">
                    <label class="form-label fw-semibold">اسم المنتج <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $product->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-sm-4">
                    <label class="form-label fw-semibold">الكود <span class="text-muted small">(اختياري)</span></label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                           value="{{ old('code', $product->code) }}">
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">سعر البيع (د.ل) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                           value="{{ old('price', $product->price) }}" min="0" required>
                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">سعر التكلفة (د.ل) <span class="text-danger">*</span></label>
                    <input type="number" name="cost_price" class="form-control @error('cost_price') is-invalid @enderror"
                           value="{{ old('cost_price', $product->cost_price) }}" min="0" required>
                    @error('cost_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">الوصف <span class="text-muted small">(اختياري)</span></label>
                    <textarea name="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                               {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="isActive">مفعّل</label>
                    </div>
                </div>

                {{-- Existing Images --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">الصور الحالية</label>
                    <p class="text-muted small mb-2">اضغط على صورة لتعيينها كـ رئيسية — اضغط ✕ لحذفها</p>

                    @if($product->images->isEmpty())
                        <p class="text-muted small">لا توجد صور</p>
                    @else
                        <div class="img-grid" id="existingGrid">
                            @foreach($product->images as $img)
                            <div class="img-item {{ $img->is_primary ? 'primary' : '' }}"
                                 data-id="{{ $img->id }}" id="imgItem{{ $img->id }}">
                                <img src="{{ $img->url }}" alt="">
                                @if($img->is_primary)
                                    <div class="primary-badge">رئيسية</div>
                                @endif
                                <button type="button" class="remove-btn" onclick="markDelete({{ $img->id }})">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    @endif

                    <input type="hidden" name="primary_existing" id="primaryExisting"
                           value="{{ $product->images->where('is_primary', true)->first()?->id }}">
                    <div id="deleteInputs"></div>
                </div>

                {{-- New Images --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        إضافة صور جديدة <span class="text-muted small">(اختياري — اضغط على صورة لتعيينها رئيسية)</span>
                    </label>
                    <div class="drop-zone" id="dropZone">
                        <input type="file" id="imageInput" accept="image/*" multiple>
                        <i class="ti ti-photos fs-2 text-muted d-block mb-1"></i>
                        <p class="mb-0 text-muted small">اسحب الصور هنا أو اضغط للاختيار</p>
                        <p class="mb-0 text-muted" style="font-size:.75rem">JPG, PNG, WebP — حجم أقصى 5 MB</p>
                    </div>
                    <div class="img-grid" id="newPreviewGrid"></div>
                    <input type="hidden" name="primary_index" id="primaryIndex" value="0">
                </div>

            </div>

            <div id="newImagesContainer" style="display:none"></div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i> حفظ التعديلات
                </button>
                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>

        </form>
    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
    // ── Existing images ──
    let deletedIds = [];

    document.querySelectorAll('#existingGrid .img-item').forEach(item => {
        item.addEventListener('click', function(e) {
            if (e.target.closest('.remove-btn')) return;
            const id = this.dataset.id;
            document.getElementById('primaryExisting').value = id;
            document.querySelectorAll('#existingGrid .img-item').forEach(el => {
                el.classList.remove('primary');
                el.querySelector('.primary-badge')?.remove();
            });
            this.classList.add('primary');
            const badge = document.createElement('div');
            badge.className = 'primary-badge';
            badge.textContent = 'رئيسية';
            this.appendChild(badge);
        });
    });

    function markDelete(id) {
        const item = document.getElementById('imgItem' + id);
        const alreadyDeleted = deletedIds.includes(id);
        if (alreadyDeleted) {
            deletedIds = deletedIds.filter(i => i !== id);
            item.classList.remove('deleted');
            item.querySelector('.remove-btn').innerHTML = '<i class="ti ti-x"></i>';
            item.querySelector('.remove-btn').onclick = () => markDelete(id);
        } else {
            deletedIds.push(id);
            item.classList.add('deleted');
            item.querySelector('.remove-btn').innerHTML = '<i class="ti ti-rotate"></i>';
            item.querySelector('.remove-btn').onclick = () => markDelete(id);
        }
        document.getElementById('deleteInputs').innerHTML = deletedIds
            .map(i => `<input type="hidden" name="delete_images[]" value="${i}">`)
            .join('');
    }

    // ── New images ──
    const dropZone    = document.getElementById('dropZone');
    const imageInput  = document.getElementById('imageInput');
    const previewGrid = document.getElementById('newPreviewGrid');
    const primaryIdx  = document.getElementById('primaryIndex');
    let newFiles = [];

    function renderNewPreviews() {
        previewGrid.innerHTML = '';
        document.getElementById('newImagesContainer').innerHTML = '';
        newFiles.forEach((file, i) => {
            const url = URL.createObjectURL(file);
            const isPrimary = i === parseInt(primaryIdx.value);
            const item = document.createElement('div');
            item.className = 'img-item' + (isPrimary ? ' primary' : '');
            item.innerHTML = `
                <img src="${url}" alt="">
                ${isPrimary ? '<div class="primary-badge">رئيسية جديدة</div>' : ''}
                <button type="button" class="remove-btn" data-i="${i}"><i class="ti ti-x"></i></button>
            `;
            item.addEventListener('click', (e) => {
                if (e.target.closest('.remove-btn')) return;
                primaryIdx.value = i;
                document.getElementById('primaryExisting').value = '';
                renderNewPreviews();
            });
            item.querySelector('.remove-btn').addEventListener('click', () => {
                newFiles.splice(i, 1);
                if (parseInt(primaryIdx.value) >= newFiles.length) primaryIdx.value = 0;
                renderNewPreviews();
            });
            previewGrid.appendChild(item);

            const dt = new DataTransfer();
            dt.items.add(file);
            const input = document.createElement('input');
            input.type = 'file'; input.name = 'images[]'; input.style.display = 'none';
            input.files = dt.files;
            document.getElementById('newImagesContainer').appendChild(input);
        });
    }

    imageInput.addEventListener('change', () => {
        Array.from(imageInput.files).forEach(f => { if (newFiles.length < 10) newFiles.push(f); });
        renderNewPreviews();
    });
    dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault(); dropZone.classList.remove('dragover');
        Array.from(e.dataTransfer.files).forEach(f => { if (newFiles.length < 10) newFiles.push(f); });
        renderNewPreviews();
    });
</script>
@endpush
