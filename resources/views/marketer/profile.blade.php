@extends('layouts.marketer')

@section('title', 'معلوماتي')

@push('styles')
<style>
.profile-section {
    border-radius: 16px;
    border: 1.5px solid #f0ebe8;
    background: #fff;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(74,38,25,.05);
}
.profile-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .95rem 1.1rem;
    border-bottom: 1px solid #f5f0ee;
    gap: 1rem;
    cursor: pointer;
    transition: background .12s;
    text-decoration: none;
    color: inherit;
}
.profile-row:last-child { border-bottom: none; }
.profile-row:hover { background: #fdfaf9; }
.profile-row-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.profile-row-label {
    flex: 1;
    font-size: .9rem;
    font-weight: 700;
    color: #111;
}
.profile-row-arrow {
    color: #d1c5c0;
    font-size: .9rem;
}
</style>
@endpush

@section('content')

@php
    $initials = mb_strtoupper(mb_substr($marketer->first_name, 0, 1) . mb_substr($marketer->last_name, 0, 1));
@endphp

{{-- Avatar & Name --}}
<div class="text-center mb-4 pt-2">
    <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#4a2619,#7c3d28);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.9rem;font-weight:800;margin:0 auto 1rem;box-shadow:0 4px 16px rgba(74,38,25,.3)">
        {{ $initials }}
    </div>
    <h5 class="fw-bold mb-1" style="color:#1a1a1a">{{ $marketer->first_name }} {{ $marketer->last_name }}</h5>
    <div style="font-size:.82rem;color:#9ca3af;font-weight:600">&#64;{{ $marketer->username }}</div>
</div>

{{-- Info rows --}}
<div class="profile-section mb-3">

    {{-- عن هالة كوزماتكس --}}
    @if($about)
    <div class="profile-row" data-bs-toggle="modal" data-bs-target="#aboutModal">
        <div class="profile-row-icon" style="background:#fdf6f3">
            <i class="ti ti-info-circle" style="color:#4a2619"></i>
        </div>
        <span class="profile-row-label">عن هالة كوزماتكس</span>
        <i class="ti ti-chevron-left profile-row-arrow"></i>
    </div>
    @endif

    {{-- سياسة الخصوصية --}}
    @if($policy)
    <div class="profile-row" data-bs-toggle="modal" data-bs-target="#policyModal">
        <div class="profile-row-icon" style="background:#f0f9ff">
            <i class="ti ti-shield-check" style="color:#0ea5e9"></i>
        </div>
        <span class="profile-row-label">سياسة الخصوصية</span>
        <i class="ti ti-chevron-left profile-row-arrow"></i>
    </div>
    @endif

    {{-- تقاريري --}}
    <a href="{{ route('marketer.reports') }}" class="profile-row">
        <div class="profile-row-icon" style="background:#f0f6ff">
            <i class="ti ti-chart-bar" style="color:#4f46e5"></i>
        </div>
        <span class="profile-row-label">تقاريري</span>
        <i class="ti ti-chevron-left profile-row-arrow"></i>
    </a>

    {{-- تحديث كلمة المرور --}}
    <div class="profile-row" data-bs-toggle="modal" data-bs-target="#passwordModal">
        <div class="profile-row-icon" style="background:#f0fdf4">
            <i class="ti ti-lock" style="color:#10b981"></i>
        </div>
        <span class="profile-row-label">تحديث كلمة المرور</span>
        <i class="ti ti-chevron-left profile-row-arrow"></i>
    </div>

</div>

{{-- Logout --}}
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-outline-danger w-100 rounded-3 py-3 fw-bold">
        <i class="ti ti-logout me-1"></i> تسجيل الخروج
    </button>
</form>

{{-- Modal: عن هالة كوزماتكس --}}
@if($about)
<div class="modal fade" id="aboutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" style="color:#4a2619">
                    <i class="ti ti-info-circle me-1"></i> عن هالة كوزماتكس
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="font-size:.92rem;line-height:1.8;color:#374151">
                {!! $about !!}
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal: سياسة الخصوصية --}}
@if($policy)
<div class="modal fade" id="policyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" style="color:#0ea5e9">
                    <i class="ti ti-shield-check me-1"></i> سياسة الخصوصية
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="font-size:.92rem;line-height:1.8;color:#374151">
                {!! $policy !!}
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal: تحديث كلمة المرور --}}
<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <form method="POST" action="{{ route('marketer.profile.password') }}">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold" style="color:#10b981">
                        <i class="ti ti-lock me-1"></i> تحديث كلمة المرور
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body d-flex flex-column gap-3">

                    @if(session('password_success'))
                        <div class="alert alert-success rounded-3 py-2 mb-0">{{ session('password_success') }}</div>
                    @endif

                    <div>
                        <label class="form-label fw-semibold" style="font-size:.85rem">كلمة المرور الحالية</label>
                        <input type="password" name="current_password" class="form-control rounded-3 @error('current_password') is-invalid @enderror"
                               placeholder="••••••••" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label fw-semibold" style="font-size:.85rem">كلمة المرور الجديدة</label>
                        <input type="password" name="password" class="form-control rounded-3 @error('password') is-invalid @enderror"
                               placeholder="8 أحرف على الأقل" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label fw-semibold" style="font-size:.85rem">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" class="form-control rounded-3"
                               placeholder="أعيدي الكتابة" required>
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success btn-sm px-4">
                        <i class="ti ti-check me-1"></i> تحديث
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
@if($errors->has('current_password') || $errors->has('password') || session('password_success'))
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('passwordModal')).show();
    });
@endif
</script>
@endpush
