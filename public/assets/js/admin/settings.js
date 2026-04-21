document.addEventListener('DOMContentLoaded', function () {

    const toolbarOptions = [
        ['bold', 'italic', 'underline'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ header: [1, 2, 3, false] }],
        [{ align: [] }],
        ['link'],
        ['clean'],
    ];

    const aboutQuill = new Quill('#aboutEditor', {
        theme: 'snow',
        placeholder: 'اكتب نبذة عن هالة كوزماتكس...',
        modules: { toolbar: toolbarOptions },
    });

    const policyQuill = new Quill('#policyEditor', {
        theme: 'snow',
        placeholder: 'اكتب سياسة الخصوصية وشروط الاستخدام...',
        modules: { toolbar: toolbarOptions },
    });

    const aboutVal  = document.getElementById('aboutInput').value.trim();
    const policyVal = document.getElementById('policyInput').value.trim();

    if (aboutVal)  aboutQuill.root.innerHTML  = aboutVal;
    if (policyVal) policyQuill.root.innerHTML = policyVal;

    document.getElementById('settingsForm').addEventListener('submit', function () {
        document.getElementById('aboutInput').value  = aboutQuill.root.innerHTML;
        document.getElementById('policyInput').value = policyQuill.root.innerHTML;
    });

    document.getElementById('toggleMusafirPass')?.addEventListener('click', function () {
        const input = this.closest('.input-group').querySelector('input');
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        this.querySelector('i').className = isPassword ? 'ti ti-eye-off' : 'ti ti-eye';
    });

    const musafirLoginBtn = document.getElementById('musafirLoginBtn');
    const musafirAlert    = document.getElementById('musafirAlert');
    const csrfToken       = document.querySelector('meta[name="csrf-token"]')?.content;

    musafirLoginBtn?.addEventListener('click', async function () {
        const phone    = document.getElementById('musafirPhone').value.trim();
        const password = document.getElementById('musafirPassword').value.trim();
        const url      = this.dataset.url;

        musafirAlert.style.display = 'none';
        musafirAlert.innerHTML = '';
        musafirLoginBtn.disabled = true;
        musafirLoginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جارٍ الاتصال...';

        try {
            const res  = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ musafir_phone: phone, musafir_password: password }),
            });

            const data = await res.json();

            if (data.success) {
                const ownerLine = data.owner_name
                    ? `<div class="small text-muted mt-1">${data.owner_name}</div>`
                    : '';
                const tokenLine = `<div class="small text-muted font-monospace mt-1" dir="ltr">${data.token.substring(0, 20)}…</div>`;
                musafirAlert.className = 'alert alert-success mb-4';
                musafirAlert.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <i class="ti ti-circle-check fs-5"></i>
                        <div>
                            <div class="fw-semibold">${data.message}</div>
                            ${ownerLine}
                            ${tokenLine}
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex align-items-center gap-1 text-success-emphasis">
                        <i class="ti ti-info-circle"></i>
                        <span class="small">يرجى الضغط على <strong>حفظ الإعدادات</strong> لتأكيد الربط.</span>
                    </div>`;
            } else {
                musafirAlert.className = 'alert alert-danger d-flex align-items-center gap-2 mb-4';
                musafirAlert.innerHTML = `<i class="ti ti-alert-circle fs-5"></i><span>${data.message}</span>`;
            }
        } catch {
            musafirAlert.className = 'alert alert-danger d-flex align-items-center gap-2 mb-4';
            musafirAlert.innerHTML = '<i class="ti ti-alert-circle fs-5"></i><span>حدث خطأ أثناء الاتصال.</span>';
        }

        musafirLoginBtn.disabled = false;
        musafirLoginBtn.innerHTML = '<i class="ti ti-login me-1"></i> تسجيل الدخول';

        if (data?.success) {
            bootstrap.Modal.getInstance(document.getElementById('musafirLoginModal'))?.hide();
            document.getElementById('musafirPhone').value    = '';
            document.getElementById('musafirPassword').value = '';
        }

        musafirAlert.style.display = 'flex';
    });
});
