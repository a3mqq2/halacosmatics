@extends('layouts.app')

@section('title', 'إضافة مسوق جديدة')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('marketers.index') }}">المسوقين</a></li>
    <li class="breadcrumb-item active">إضافة جديدة</li>
@endsection

@push('styles')
<style>
    .password-toggle { cursor: pointer; }
</style>
@endpush

@section('content')

<form id="marketerForm"
      method="POST"
      action="{{ route('marketers.store') }}"
      novalidate>
    @csrf

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="ti ti-user-plus me-2"></i>بيانات المسوق الجديدة
            </h5>
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
                           value="{{ old('first_name') }}">
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label">الاسم الأخير <span class="text-danger">*</span></label>
                    <input type="text"
                           name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name') }}">
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                    <input type="hidden" name="phone" id="phone">
                    <div class="input-group @error('phone') is-invalid @enderror">
                        <span class="input-group-text fw-bold">218</span>
                        <input type="text"
                               class="form-control"
                               id="phone_num"
                               value="{{ old('phone') ? substr(old('phone'), 3) : '' }}"
                               maxlength="9"
                               inputmode="numeric">
                    </div>
                    @error('phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label">رقم الهاتف الاحتياطي</label>
                    <input type="hidden" name="backup_phone" id="backup_phone">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">218</span>
                        <input type="text"
                               class="form-control"
                               id="backup_phone_num"
                               value="{{ old('backup_phone') ? substr(old('backup_phone'), 3) : '' }}"
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
                           value="{{ old('email') }}">
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
                           value="{{ old('username') }}"
                           autocomplete="off">
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               autocomplete="new-password">
                        <button type="button" class="btn btn-outline-secondary password-toggle"
                                onclick="togglePassword('password', this)">
                            <i class="ti ti-eye"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="form-control"
                               autocomplete="new-password">
                        <button type="button" class="btn btn-outline-secondary password-toggle"
                                onclick="togglePassword('password_confirmation', this)">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                </div>

            </div>

        </div>

        <div class="card-footer d-flex gap-2 justify-content-end">
            <a href="{{ route('marketers.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-right me-1"></i>رجوع
            </a>
            <button type="submit" class="btn btn-success" id="submitBtn">
                <i class="ti ti-device-floppy me-1"></i>حفظ المسوق
            </button>
        </div>
    </div>

</form>

@endsection

@push('scripts')
<script>
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

document.getElementById('marketerForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...';
});

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
</script>
@endpush
