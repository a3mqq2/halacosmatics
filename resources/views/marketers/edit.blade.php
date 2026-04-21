@extends('layouts.app')

@section('title', 'تعديل بيانات المسوق')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('marketers.index') }}">المسوقين</a></li>
    <li class="breadcrumb-item active">تعديل</li>
@endsection

@push('styles')
<style>
    .drop-zone {
        border: 2px dashed #ced4da;
        border-radius: 10px;
        padding: 2rem 1rem;
        text-align: center;
        cursor: pointer;
        transition: border-color .2s, background .2s;
        background: #f8f9fa;
        position: relative;
    }
    .drop-zone.dragover {
        border-color: #0d6efd;
        background: #e8f0fe;
    }
    .drop-zone.has-file {
        border-color: #198754;
        background: #f0fdf4;
    }
    .drop-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .drop-zone .drop-icon { font-size: 2.5rem; color: #adb5bd; transition: color .2s; }
    .drop-zone.dragover .drop-icon,
    .drop-zone.has-file  .drop-icon { color: #198754; }

    .passport-preview img {
        max-height: 160px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        object-fit: cover;
    }

    .upload-progress { display: none; }
    .upload-progress.active { display: block; }
</style>
@endpush

@section('content')

<form id="marketerForm"
      method="POST"
      action="{{ route('marketers.update', $marketer) }}"
      enctype="multipart/form-data"
      novalidate>
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
                <i class="ti ti-user-edit me-2"></i>تعديل: {{ $marketer->first_name }} {{ $marketer->last_name }}
            </h5>
            <span class="badge bg-success-subtle text-success fs-12">
                <i class="ti ti-circle-check me-1"></i>مفعّل
            </span>
        </div>
        <div class="card-body">

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ── Section: Personal Info ── --}}
            <p class="text-muted fw-semibold fs-12 text-uppercase mb-2">
                <i class="ti ti-id-badge me-1"></i>المعلومات الشخصية
            </p>
            <div class="row g-3 mb-4">

                <div class="col-12 col-sm-6">
                    <label class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                    <input type="text"
                           name="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name', $marketer->first_name) }}">
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label">الاسم الأخير <span class="text-danger">*</span></label>
                    <input type="text"
                           name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name', $marketer->last_name) }}">
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="col-12 col-sm-6">
                    <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                    @php $phoneVal = old('phone', $marketer->phone); @endphp
                    <input type="hidden" name="phone" id="phone">
                    <div class="input-group @error('phone') is-invalid @enderror">
                        <span class="input-group-text fw-bold">218</span>
                        <input type="text"
                               class="form-control"
                               id="phone_num"
                               value="{{ strlen($phoneVal) > 3 ? substr($phoneVal, 3) : $phoneVal }}"
                               maxlength="9"
                               inputmode="numeric">
                    </div>
                    @error('phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Backup Phone --}}
                <div class="col-12 col-sm-6">
                    <label class="form-label">رقم الهاتف الاحتياطي</label>
                    @php $backupVal = old('backup_phone', $marketer->backup_phone ?? ''); @endphp
                    <input type="hidden" name="backup_phone" id="backup_phone">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">218</span>
                        <input type="text"
                               class="form-control"
                               id="backup_phone_num"
                               value="{{ strlen($backupVal) > 3 ? substr($backupVal, 3) : $backupVal }}"
                               maxlength="9"
                               inputmode="numeric">
                    </div>
                    @error('backup_phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $marketer->email) }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <hr class="my-3">

            {{-- ── Section: Account ── --}}
            <p class="text-muted fw-semibold fs-12 text-uppercase mb-2">
                <i class="ti ti-lock me-1"></i>بيانات الحساب
            </p>
            <div class="row g-3 mb-4">

                <div class="col-12 col-sm-6">
                    <label class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                    <input type="text"
                           name="username"
                           class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username', $marketer->username) }}"
                           autocomplete="off">
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label">
                        كلمة المرور الجديدة
                        <small class="text-muted fw-normal">(اتركها فارغة إن لم تريد تغييرها)</small>
                    </label>
                    <div class="input-group">
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               autocomplete="new-password">
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePassword('password', this)">
                            <i class="ti ti-eye"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <div class="input-group">
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="form-control"
                               autocomplete="new-password">
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePassword('password_confirmation', this)">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                </div>

            </div>

            <hr class="my-3">

            {{-- ── Section: Passport ── --}}
            <p class="text-muted fw-semibold fs-12 text-uppercase mb-2">
                <i class="ti ti-passport me-1"></i>جواز السفر
            </p>
            <div class="row g-3 mb-2">
                <div class="col-12 col-md-8 col-lg-6">

                    {{-- Current passport --}}
                    @if($marketer->passport)
                        <div class="d-flex align-items-center gap-2 p-2 bg-light rounded border mb-2" id="currentPassport">
                            <i class="ti ti-file fs-24 text-primary"></i>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="mb-0 fw-semibold fs-14">الملف الحالي</p>
                                <small class="text-muted">{{ basename($marketer->passport) }}</small>
                            </div>
                            <a href="{{ Storage::url($marketer->passport) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-eye me-1"></i>عرض
                            </a>
                        </div>
                    @endif

                    <div class="drop-zone @error('passport') border-danger @enderror" id="dropZone">
                        <input type="file"
                               name="passport"
                               id="passportInput"
                               accept=".jpg,.jpeg,.png,.pdf">
                        <div class="drop-zone-content" id="dropContent">
                            <i class="ti ti-cloud-upload drop-icon mb-2 d-block"></i>
                            <p class="mb-1 fw-semibold">
                                {{ $marketer->passport ? 'رفع ملف جديد بديلاً للحالي' : 'اسحب وأفلت الملف هنا' }}
                            </p>
                            <p class="text-muted mb-0 fs-13">أو <span class="text-primary">انقر للاختيار</span></p>
                            <small class="text-muted d-block mt-2">JPG, PNG, PDF — الحد الأقصى 5 ميغابايت</small>
                        </div>
                    </div>

                    {{-- New file preview --}}
                    <div class="passport-preview mt-2 d-none" id="passportPreview">
                        <div class="d-flex align-items-center gap-2 p-2 bg-light rounded border">
                            <div id="previewImg"></div>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="mb-0 fw-semibold text-truncate fs-14" id="fileName"></p>
                                <small class="text-muted" id="fileSize"></small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFile()">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    </div>

                    @error('passport')
                        <div class="text-danger fs-13 mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                    @enderror

                </div>
            </div>

        </div>

        {{-- Upload Progress --}}
        <div class="upload-progress px-4 pb-3" id="uploadProgress">
            <div class="d-flex justify-content-between mb-1">
                <small class="fw-semibold" id="progressLabel">جاري الحفظ...</small>
                <small id="progressPercent">0%</small>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                     id="progressBar"
                     role="progressbar"
                     style="width: 0%">
                </div>
            </div>
        </div>

        <div class="card-footer d-flex gap-2 justify-content-end">
            <a href="{{ route('marketers.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-right me-1"></i>رجوع
            </a>
            <button type="submit" class="btn btn-warning" id="submitBtn">
                <i class="ti ti-device-floppy me-1"></i>حفظ التعديلات
            </button>
        </div>
    </div>

</form>

@endsection

@push('scripts')
<script>
// ── Phone Builder ─────────────────────────────────────────
function initPhoneBuilder(numId, hiddenId) {
    const num = document.getElementById(numId);
    const sync = () => {
        num.value = num.value.replace(/\D/g, '');
        document.getElementById(hiddenId).value = num.value ? '218' + num.value : '';
    };
    num.addEventListener('input', sync);
    sync();
}
initPhoneBuilder('phone_num',        'phone');
initPhoneBuilder('backup_phone_num', 'backup_phone');

// ── Drag & Drop ───────────────────────────────────────────
const dropZone    = document.getElementById('dropZone');
const fileInput   = document.getElementById('passportInput');
const dropContent = document.getElementById('dropContent');
const preview     = document.getElementById('passportPreview');
const previewImg  = document.getElementById('previewImg');
const fileNameEl  = document.getElementById('fileName');
const fileSizeEl  = document.getElementById('fileSize');

['dragenter', 'dragover'].forEach(e =>
    dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.add('dragover'); })
);
['dragleave', 'drop'].forEach(e =>
    dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.remove('dragover'); })
);

dropZone.addEventListener('drop', ev => {
    const file = ev.dataTransfer.files[0];
    if (file) attachFile(file);
});

fileInput.addEventListener('change', () => {
    if (fileInput.files[0]) attachFile(fileInput.files[0]);
});

function attachFile(file) {
    const allowed = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!allowed.includes(file.type)) {
        showToast('صيغة الملف غير مدعومة. يُقبل: JPG, PNG, PDF', 'danger');
        return;
    }
    if (file.size > 5 * 1024 * 1024) {
        showToast('حجم الملف يتجاوز 5 ميغابايت', 'danger');
        return;
    }
    const dt = new DataTransfer();
    dt.items.add(file);
    fileInput.files = dt.files;

    dropZone.classList.add('has-file');
    fileNameEl.textContent = file.name;
    fileSizeEl.textContent = formatBytes(file.size);

    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => { previewImg.innerHTML = `<img src="${e.target.result}" alt="preview">`; };
        reader.readAsDataURL(file);
    } else {
        previewImg.innerHTML = `<i class="ti ti-file-type-pdf text-danger fs-36"></i>`;
    }

    dropContent.querySelector('p').textContent = 'تم اختيار ملف جديد';
    preview.classList.remove('d-none');
}

function clearFile() {
    fileInput.value = '';
    dropZone.classList.remove('has-file');
    dropContent.querySelector('p').textContent = '{{ $marketer->passport ? "رفع ملف جديد بديلاً للحالي" : "اسحب وأفلت الملف هنا" }}';
    preview.classList.add('d-none');
    previewImg.innerHTML = '';
}

// ── XHR Upload with Progress ──────────────────────────────
document.getElementById('marketerForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const submitBtn     = document.getElementById('submitBtn');
    const progressWrap  = document.getElementById('uploadProgress');
    const progressBar   = document.getElementById('progressBar');
    const progressPct   = document.getElementById('progressPercent');
    const progressLabel = document.getElementById('progressLabel');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...';
    progressWrap.classList.add('active');

    const formData = new FormData(this);
    const xhr      = new XMLHttpRequest();

    xhr.upload.addEventListener('progress', ev => {
        if (ev.lengthComputable) {
            const pct = Math.round((ev.loaded / ev.total) * 100);
            progressBar.style.width = pct + '%';
            progressPct.textContent = pct + '%';
            if (pct === 100) progressLabel.textContent = 'جاري المعالجة...';
        }
    });

    xhr.addEventListener('load', () => {
        if (xhr.status === 302 || xhr.status === 200) {
            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.add('bg-success');
            progressBar.style.width = '100%';
            progressLabel.textContent = 'تم الحفظ بنجاح!';
            progressPct.textContent = '100%';
            setTimeout(() => { window.location.href = xhr.responseURL; }, 400);
        } else {
            document.open();
            document.write(xhr.responseText);
            document.close();
        }
    });

    xhr.addEventListener('error', () => {
        showToast('حدث خطأ أثناء الرفع، حاول مرة أخرى', 'danger');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>حفظ التعديلات';
        progressWrap.classList.remove('active');
    });

    xhr.open('POST', this.action);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send(formData);
});

// ── Helpers ───────────────────────────────────────────────
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('ti-eye', 'ti-eye-off');
    } else {
        input.type = 'password';
        icon.classList.replace('ti-eye-off', 'ti-eye');
    }
}

function formatBytes(bytes) {
    if (bytes < 1024)     return bytes + ' B';
    if (bytes < 1048576)  return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed bottom-0 end-0 m-3 shadow`;
    toast.style.zIndex = 9999;
    toast.style.minWidth = '280px';
    toast.innerHTML = `<i class="ti ti-alert-circle me-1"></i>${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}
</script>
@endpush
