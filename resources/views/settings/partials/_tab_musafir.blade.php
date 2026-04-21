<div class="tab-pane fade" id="musafir-pane" role="tabpanel">
    <div class="d-flex align-items-center gap-3 mb-4">
        <img src="{{ asset('mosafer.svg') }}" alt="شركة المسافر" style="height:48px;width:auto;">
        <p class="text-muted small mb-0">ربط حساب شركة المسافر للشحن.</p>
    </div>

    @if($settings['musafir_token'])
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ti ti-circle-check fs-5"></i>
            <div>
                <div class="fw-semibold">تم الربط بنجاح</div>
                @if($settings['musafir_owner_name'])
                    <div class="small text-muted">{{ $settings['musafir_owner_name'] }}</div>
                @endif
            </div>
        </div>
    @endif

    <div id="musafirAlert" class="mb-4" style="display:none"></div>

    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#musafirLoginModal">
            <i class="ti ti-login me-1"></i> تسجيل الدخول
        </button>
    </div>
</div>

<div class="modal fade" id="musafirLoginModal" tabindex="-1" aria-labelledby="musafirLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="musafirLoginModalLabel">
                    <img src="{{ asset('mosafer.svg') }}" alt="" style="height:28px;width:auto;" class="me-2">
                    تسجيل الدخول
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">رقم الهاتف</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-phone"></i></span>
                        <input type="text" id="musafirPhone" class="form-control"
                               placeholder="09XXXXXXXX" dir="ltr" autocomplete="off">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">كلمة المرور</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-lock"></i></span>
                        <input type="password" id="musafirPassword" class="form-control"
                               placeholder="••••••••" dir="ltr" autocomplete="off">
                        <button class="btn btn-outline-secondary" type="button" id="toggleMusafirPass">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="musafirLoginBtn"
                        data-url="{{ route('settings.musafir.login') }}">
                    <i class="ti ti-login me-1"></i> تسجيل الدخول
                </button>
            </div>
        </div>
    </div>
</div>
