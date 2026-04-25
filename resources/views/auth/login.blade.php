<!doctype html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8" />
        <title>تسجيل الدخول</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <link rel="shortcut icon" href="{{ asset('assets/images/logo-sm.png') }}" />

        <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet" />

        <link href="{{ asset('assets/css/vendors.min.css') }}" rel="stylesheet" type="text/css" />

        <style>
            :root {
                --login-bg: #ffffff;
                --login-text: #1a1a2e;
                --login-text-secondary: #6b7280;
                --login-input-bg: #f9fafb;
                --login-input-border: #e5e7eb;
                --login-input-focus-border: #8d5342;
                --login-btn-bg: #8d5342;
                --login-btn-hover: #8d5342;
                --login-overlay: rgba(15, 15, 35, 0.55);
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Almarai', sans-serif !important;
            }

            html, body {
                height: 100%;
                direction: rtl;
                text-align: right;
            }

            body {
                background: var(--login-bg);
            }

            .login-wrapper {
                display: flex;
                width: 100%;
                min-height: 100vh;
            }

            .login-image-side {
                flex: 1;
                position: relative;
                background: url('{{ asset('assets/images/auth.jpg') }}') center/cover no-repeat;
                display: flex;
                align-items: flex-end;
                justify-content: center;
            }

            .login-image-side::after {
                content: '';
                position: absolute;
                inset: 0;
                background: var(--login-overlay);
            }

            .image-content {
                position: relative;
                z-index: 1;
                padding: 48px;
                color: #fff;
                text-align: center;
            }

            .image-content h2 {
                font-size: 28px;
                font-weight: 800;
                margin-bottom: 8px;
                letter-spacing: -0.3px;
            }

            .image-content p {
                font-size: 15px;
                font-weight: 300;
                opacity: 0.85;
            }

            .login-form-side {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 40px;
                background: var(--login-bg);
            }

            .login-form-container {
                width: 100%;
                max-width: 400px;
            }

            .login-logo {
                margin-bottom: 40px;
                text-align: center;
            }

            .login-logo img {
                height: 174px;
            }

            .login-heading {
                margin-bottom: 32px;
            }

            .login-heading h1 {
                font-size: 24px;
                font-weight: 800;
                color: var(--login-text);
                margin-bottom: 6px;
            }

            .login-heading p {
                font-size: 14px;
                color: var(--login-text-secondary);
                font-weight: 400;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                font-size: 13px;
                font-weight: 700;
                color: var(--login-text);
                margin-bottom: 6px;
            }

            .form-group label .required {
                color: #ef4444;
                margin-right: 2px;
            }

            .input-wrapper {
                position: relative;
            }

            .input-wrapper i {
                position: absolute;
                right: 14px;
                top: 50%;
                transform: translateY(-50%);
                color: var(--login-text-secondary);
                font-size: 18px;
                pointer-events: none;
            }

            .input-wrapper input {
                width: 100%;
                padding: 12px 44px 12px 14px;
                border: 1.5px solid var(--login-input-border);
                border-radius: 10px;
                background: var(--login-input-bg);
                font-size: 14px;
                font-family: 'Almarai', sans-serif !important;
                color: var(--login-text);
                direction: rtl;
                text-align: right;
                transition: border-color 0.2s;
                outline: none;
            }

            .input-wrapper input::placeholder {
                color: #9ca3af;
                text-align: right;
            }

            .input-wrapper input:focus {
                border-color: var(--login-input-focus-border);
                background: #fff;
            }

            .input-wrapper input.is-invalid {
                border-color: #ef4444;
            }

            .invalid-feedback {
                display: block;
                font-size: 12px;
                color: #ef4444;
                margin-top: 4px;
            }

            .remember-row {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 24px;
            }

            .remember-row input[type="checkbox"] {
                width: 16px;
                height: 16px;
                accent-color: var(--login-btn-bg);
                cursor: pointer;
            }

            .remember-row label {
                font-size: 13px;
                color: var(--login-text-secondary);
                cursor: pointer;
            }

            .login-btn {
                width: 100%;
                padding: 13px;
                border: none;
                border-radius: 10px;
                background: var(--login-btn-bg);
                color: #fff;
                font-size: 15px;
                font-weight: 700;
                font-family: 'Almarai', sans-serif !important;
                cursor: pointer;
                transition: background 0.2s;
            }

            .login-btn:hover {
                background: var(--login-btn-hover);
            }

            .login-btn:disabled {
                opacity: 0.7;
                cursor: not-allowed;
            }

            .spinner-border {
                display: inline-block;
                width: 16px;
                height: 16px;
                border: 2px solid rgba(255,255,255,0.3);
                border-radius: 50%;
                border-top-color: #fff;
                animation: spin 0.6s linear infinite;
                vertical-align: middle;
                margin-left: 6px;
            }

            @keyframes spin {
                to { transform: rotate(360deg); }
            }

            .login-footer {
                text-align: center;
                font-size: 13px;
                color: var(--login-text-secondary);
                padding: 16px 0 4px;
                width: 100%;
            }

            /* ── Tabs ── */
            .auth-tabs {
                display: flex;
                border-bottom: 2px solid #e5e7eb;
                margin-bottom: 28px;
                gap: 4px;
            }
            .auth-tab {
                flex: 1;
                padding: 10px;
                text-align: center;
                font-size: 14px;
                font-weight: 700;
                color: var(--login-text-secondary);
                cursor: pointer;
                border: none;
                background: none;
                border-bottom: 2px solid transparent;
                margin-bottom: -2px;
                transition: color .2s, border-color .2s;
                font-family: 'Almarai', sans-serif !important;
            }
            .auth-tab.active {
                color: var(--login-btn-bg);
                border-bottom-color: var(--login-btn-bg);
            }
            .auth-pane { display: none; }
            .auth-pane.active { display: block; }

            /* ── Phone input ── */
            .phone-group {
                display: flex;
                border: 1.5px solid var(--login-input-border);
                border-radius: 10px;
                overflow: hidden;
                background: var(--login-input-bg);
                transition: border-color .2s;
            }
            .phone-group:focus-within {
                border-color: var(--login-input-focus-border);
                background: #fff;
            }
            .phone-group.is-invalid { border-color: #ef4444; }
            .phone-prefix {
                padding: 12px 12px;
                font-size: 13px;
                font-weight: 700;
                color: var(--login-text-secondary);
                background: #f3f4f6;
                border-left: 1.5px solid var(--login-input-border);
                white-space: nowrap;
            }
            .phone-group input {
                border: none !important;
                border-radius: 0 !important;
                background: transparent !important;
                flex: 1;
                padding: 12px !important;
            }
            .phone-group input:focus { border: none; outline: none; }

            /* ── register btn ── */
            .register-btn {
                width: 100%;
                padding: 13px;
                border: none;
                border-radius: 10px;
                background: #10b981;
                color: #fff;
                font-size: 15px;
                font-weight: 700;
                font-family: 'Almarai', sans-serif !important;
                cursor: pointer;
                transition: background .2s;
            }
            .register-btn:hover { background: #059669; }
            .register-btn:disabled { opacity: .7; cursor: not-allowed; }

            .success-alert {
                background: #f0fdf4;
                border: 1px solid #bbf7d0;
                color: #15803d;
                border-radius: 12px;
                padding: 20px;
                font-size: 13px;
                margin-bottom: 20px;
                text-align: center;
            }

            .login-form-side { overflow-y: auto; }

            @media (max-width: 991px) {
                .login-image-side { display: none; }
                .login-form-side { padding: 32px 24px; }
            }
        </style>
    </head>

    <body>
        <div class="login-wrapper">
            <div class="login-form-side">
                <div class="login-form-container">
                    <div class="login-logo">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('assets/images/logo-black.png') }}" alt="logo" />
                        </a>
                    </div>

                    {{-- Success after register --}}
                    @if(session('register_success'))
                        <div class="success-alert">
                            <p style="font-weight:700;margin-bottom:4px">تم إرسال طلبك!</p>
                            <p style="margin:0;line-height:1.6">{{ session('register_success') }}</p>
                        </div>
                    @endif

                    {{-- ── Tabs ── --}}
                    <div class="auth-tabs">
                        <button class="auth-tab {{ old('_form') != 'register' ? 'active' : '' }}"
                                onclick="switchTab('login')" id="tab-login" type="button">
                            تسجيل الدخول
                        </button>
                        <button class="auth-tab {{ old('_form') === 'register' ? 'active' : '' }}"
                                onclick="switchTab('register')" id="tab-register" type="button">
                            حساب مسوق جديد
                        </button>
                    </div>

                    {{-- ══ LOGIN PANE ══ --}}
                    <div class="auth-pane {{ old('_form') != 'register' ? 'active' : '' }}" id="pane-login">

                        <div class="login-heading" style="margin-bottom:24px">
                            <h1>مرحباً بك</h1>
                            <p>أدخل بيانات الدخول للمتابعة</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}" id="loginForm">
                            @csrf
                            <input type="hidden" name="_form" value="login">
                            <div class="form-group">
                                <label for="userLogin">رقم الهاتف <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="ti ti-phone"></i>
                                    <input type="tel" class="@error('phone') is-invalid @enderror"
                                           id="userLogin" name="phone"
                                           value="{{ old('phone') }}" required autofocus
                                           placeholder="0xxxxxxxxx" />
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="userPassword">كلمة المرور <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="ti ti-lock"></i>
                                    <input type="password" class="@error('password') is-invalid @enderror"
                                           id="userPassword" name="password" required />
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="remember-row">
                                <input type="checkbox" id="rememberMe" name="remember" />
                                <label for="rememberMe">تذكرني</label>
                            </div>

                            <button type="submit" class="login-btn" id="loginBtn">دخول</button>
                        </form>
                    </div>

                    {{-- ══ REGISTER PANE ══ --}}
                    <div class="auth-pane {{ old('_form') === 'register' ? 'active' : '' }}" id="pane-register">

                        <div class="login-heading" style="margin-bottom:24px">
                            <h1>إنشاء حساب</h1>
                            <p>سجّل بياناتك للانضمام كمسوق</p>
                        </div>

                        @if($errors->any() && old('_form') === 'register')
                            <div style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:16px;">
                                <ul style="margin:0;padding-right:16px">
                                    @foreach($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('marketers.register') }}"
                              enctype="multipart/form-data" id="registerForm">
                            @csrf
                            <input type="hidden" name="_form" value="register">

                            {{-- Name row --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                                <div class="form-group">
                                    <label>الاسم الأول <span class="required">*</span></label>
                                    <div class="input-wrapper">
                                        <i class="ti ti-user"></i>
                                        <input type="text" name="first_name"
                                               value="{{ old('first_name') }}"
                                               class="@error('first_name') is-invalid @enderror" required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>الاسم الأخير <span class="required">*</span></label>
                                    <div class="input-wrapper">
                                        <i class="ti ti-user"></i>
                                        <input type="text" name="last_name"
                                               value="{{ old('last_name') }}"
                                               class="@error('last_name') is-invalid @enderror" required />
                                    </div>
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="form-group">
                                <label>رقم الهاتف <span class="required">*</span></label>
                                <input type="hidden" name="phone" id="reg_phone">
                                <div class="phone-group @error('phone') is-invalid @enderror" id="reg_phone_group">
                                    <span class="phone-prefix">218</span>
                                    <input type="text" id="reg_phone_num"
                                           value="{{ old('phone') ? substr(old('phone'),3) : '' }}"
                                           maxlength="9" inputmode="numeric"
                                           placeholder="9xxxxxxxx" autocomplete="off" />
                                </div>
                                <small style="color:#6b7280;font-size:12px;margin-top:4px;display:block">
                                    يبدأ بـ 91 أو 92 أو 93 أو 94 — مثال: <strong>923456789</strong>
                                </small>
                                @error('phone')
                                    <div class="invalid-feedback" style="display:block">{{ $message }}</div>
                                @enderror
                                <div id="reg_phone_error" style="color:#ef4444;font-size:12px;margin-top:4px;display:none"></div>
                            </div>

                            {{-- Backup Phone --}}
                            <div class="form-group">
                                <label>رقم احتياطي</label>
                                <input type="hidden" name="backup_phone" id="reg_backup_phone">
                                <div class="phone-group">
                                    <span class="phone-prefix">218</span>
                                    <input type="text" id="reg_backup_num"
                                           value="{{ old('backup_phone') ? substr(old('backup_phone'),3) : '' }}"
                                           maxlength="9" inputmode="numeric" />
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="form-group">
                                <label>البريد الإلكتروني</label>
                                <div class="input-wrapper">
                                    <i class="ti ti-mail"></i>
                                    <input type="email" name="email"
                                           value="{{ old('email') }}"
                                           class="@error('email') is-invalid @enderror" />
                                </div>
                            </div>


                            {{-- Password --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                                <div class="form-group">
                                    <label>كلمة المرور <span class="required">*</span></label>
                                    <div class="input-wrapper">
                                        <i class="ti ti-lock"></i>
                                        <input type="password" name="password"
                                               autocomplete="new-password"
                                               class="@error('password') is-invalid @enderror" required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>تأكيد المرور <span class="required">*</span></label>
                                    <div class="input-wrapper">
                                        <i class="ti ti-lock-check"></i>
                                        <input type="password" name="password_confirmation"
                                               autocomplete="new-password" required />
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="register-btn" id="registerBtn">
                                إنشاء الحساب
                            </button>
                        </form>
                    </div>

                </div>

                <div class="login-footer">
                    تنفيذ <strong><a href="">شركة حلول لتقنية المعلومات</a></strong>
                </div>
            </div>

            <div class="login-image-side">
                <div class="image-content">
                    <h2>تابع لأنظمة حلول لإدارة الأعمال</h2>
                    <p></p>
                </div>
            </div>
        </div>

        {{-- ── Confirm Modal ── --}}
        <div id="confirmModal" style="
            display:none;position:fixed;inset:0;z-index:9999;
            background:rgba(0,0,0,.45);
            align-items:center;justify-content:center;padding:16px">
            <div style="
                background:#fff;border-radius:16px;
                width:100%;max-width:440px;
                box-shadow:0 20px 60px rgba(0,0,0,.2);
                overflow:hidden;animation:modalIn .2s ease">

                {{-- Header --}}
                <div style="background:#10b981;padding:20px 24px;color:#fff;display:flex;align-items:center;gap:10px">
                    <i class="ti ti-user-check" style="font-size:22px"></i>
                    <div>
                        <p style="margin:0;font-weight:800;font-size:16px">تأكيد بيانات التسجيل</p>
                        <small style="opacity:.85">يرجى مراجعة البيانات قبل الإرسال</small>
                    </div>
                </div>

                {{-- Body --}}
                <div style="padding:20px 24px" id="modalBody"></div>

                {{-- Footer --}}
                <div style="padding:16px 24px;border-top:1px solid #f3f4f6;display:flex;gap:10px;justify-content:flex-end">
                    <button onclick="closeConfirmModal()" style="
                        padding:10px 20px;border:1.5px solid #e5e7eb;border-radius:8px;
                        background:#fff;font-size:14px;font-weight:700;cursor:pointer;
                        font-family:'Almarai',sans-serif">
                        تعديل
                    </button>
                    <button id="confirmSubmitBtn" onclick="submitRegister()" style="
                        padding:10px 24px;border:none;border-radius:8px;
                        background:#10b981;color:#fff;font-size:14px;font-weight:700;
                        cursor:pointer;font-family:'Almarai',sans-serif;transition:background .2s">
                        تأكيد وإرسال
                    </button>
                </div>
            </div>
        </div>

        <style>
            @keyframes modalIn {
                from { opacity:0; transform:scale(.95) translateY(10px); }
                to   { opacity:1; transform:scale(1)  translateY(0); }
            }
            .confirm-row {
                display:flex;align-items:flex-start;gap:10px;
                padding:8px 0;border-bottom:1px solid #f9fafb;
            }
            .confirm-row:last-child { border-bottom:none; }
            .confirm-icon {
                width:30px;height:30px;border-radius:50%;
                display:flex;align-items:center;justify-content:center;
                flex-shrink:0;font-size:14px;
            }
            .confirm-label { font-size:12px;color:#6b7280;margin-bottom:2px; }
            .confirm-value { font-size:14px;font-weight:700;color:#1a1a2e; }
        </style>

        <script src="{{ asset('assets/js/vendors.min.js') }}"></script>
        <script>
        // ── Tabs ─────────────────────────────────────────────
        function switchTab(tab) {
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.auth-pane').forEach(p => p.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
            document.getElementById('pane-' + tab).classList.add('active');
        }

        // ── Login submit ──────────────────────────────────────
        document.getElementById('loginForm').addEventListener('submit', function() {
            var btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border"></span>جاري الدخول...';
        });

        // ── Phone builder ─────────────────────────────────────
        function initPhone(numId, hiddenId) {
            const num = document.getElementById(numId);
            const sync = () => {
                let val = num.value.replace(/\D/g, '');
                val = val.replace(/^0+/, '');
                val = val.slice(0, 9);
                num.value = val;
                document.getElementById(hiddenId).value = val ? '218' + val : '';
            };
            num.addEventListener('input', sync);
            sync();
        }
        initPhone('reg_phone_num',  'reg_phone');
        initPhone('reg_backup_num', 'reg_backup_phone');

        // ── Register submit ───────────────────────────────────
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            var phoneVal = document.getElementById('reg_phone_num').value;
            var errEl    = document.getElementById('reg_phone_error');
            var group    = document.getElementById('reg_phone_group');

            var validPrefixes = ['91', '92', '93', '94'];
            var prefix = phoneVal.slice(0, 2);

            if (phoneVal.length != 9) {
                e.preventDefault();
                errEl.textContent = 'رقم الهاتف يجب أن يكون 9 أرقام بالضبط.';
                errEl.style.display = 'block';
                group.classList.add('is-invalid');
                return;
            }
            if (!validPrefixes.includes(prefix)) {
                e.preventDefault();
                errEl.textContent = 'رقم الهاتف يجب أن يبدأ بـ 91 أو 92 أو 93 أو 94.';
                errEl.style.display = 'block';
                group.classList.add('is-invalid');
                return;
            }

            errEl.style.display = 'none';
            group.classList.remove('is-invalid');
            var btn = document.getElementById('registerBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border"></span>جاري الحفظ...';
        });

        </script>
    </body>
</html>
