@extends('layouts.app')

@section('title', 'الإعدادات العامة')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">الإعدادات العامة</li>
@endsection

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<link href="{{ asset('assets/css/admin/settings.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="row justify-content-center">
<div class="col-12">

    <form method="POST" action="{{ route('settings.update') }}" id="settingsForm">
        @csrf @method('PUT')

        <div class="card">
            <div class="card-header p-0 border-bottom">
                <ul class="nav nav-tabs card-header-tabs px-3" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold py-3" id="about-tab"
                                data-bs-toggle="tab" data-bs-target="#about-pane"
                                type="button" role="tab">
                            <i class="ti ti-info-circle me-1"></i> عن هالة كوزماتكس
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold py-3" id="policy-tab"
                                data-bs-toggle="tab" data-bs-target="#policy-pane"
                                type="button" role="tab">
                            <i class="ti ti-file-text me-1"></i> سياسة الخصوصية والاستخدام
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold py-3" id="musafir-tab"
                                data-bs-toggle="tab" data-bs-target="#musafir-pane"
                                type="button" role="tab">
                            <i class="ti ti-truck-delivery me-1"></i> شركة المسافر
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content" id="settingsTabContent">
                    @include('settings.partials._tab_about')
                    @include('settings.partials._tab_policy')
                    @include('settings.partials._tab_musafir')
                </div>
            </div>

            <div class="card-footer border-top d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i> حفظ الإعدادات
                </button>
            </div>
        </div>

    </form>

</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script src="{{ asset('assets/js/admin/settings.js') }}"></script>
@endpush
