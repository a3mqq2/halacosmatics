const PHONE_PATTERN = /^09[1-4]\d{7}$/;

function validateDispatchPhone() {
    const input = document.querySelector('#form-mosafir [name="recipient_number"]');
    if (!input) return true;

    let errEl = document.getElementById('dispatchPhoneError');
    if (!errEl) {
        errEl = document.createElement('div');
        errEl.id = 'dispatchPhoneError';
        errEl.style.cssText = 'color:#ef4444;font-size:.82rem;margin-top:4px;display:none';
        errEl.textContent   = 'رقم الهاتف يجب أن يتكون من 10 أرقام ويبدأ بـ 091 أو 092 أو 093 أو 094';
        input.parentNode.appendChild(errEl);
    }

    if (PHONE_PATTERN.test(input.value.trim())) {
        errEl.style.display = 'none';
        input.classList.remove('is-invalid');
        return true;
    }
    errEl.style.display = '';
    input.classList.add('is-invalid');
    return false;
}

document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', (e) => {
        if (e.defaultPrevented) return;

        if (form.id === 'form-mosafir') {
            if (!validateDispatchPhone()) {
                e.preventDefault();
                const errEl = document.getElementById('dispatchPhoneError');
                if (errEl) errEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
        }

        const btn = form.querySelector('[type="submit"]');
        if (! btn) return;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> جاري...';
    });
});

let tsCity = null;
let tsSubCity = null;

function initDispatchSelects() {
    if (tsCity) return;

    tsCity = new TomSelect('#ms-city', {
        placeholder: '— اختر المدينة —',
        allowEmptyOption: true,
        onChange(cityId) {
            const opt = document.querySelector(`#ms-city option[value="${cityId}"]`);
            const subCities = opt ? JSON.parse(opt.dataset.subcities || '[]') : [];

            tsSubCity.clear();
            tsSubCity.clearOptions();
            tsSubCity.addOption({ value: '', text: '— اختر المنطقة —' });
            subCities.forEach(s => tsSubCity.addOption({ value: s.id, text: s.name }));
            tsSubCity.setValue('');

            const wrap = document.getElementById('ms-subcity').closest('.col-12');
            wrap.style.display = subCities.length ? '' : 'none';
        },
    });

    tsSubCity = new TomSelect('#ms-subcity', {
        placeholder: '— اختر المنطقة —',
        allowEmptyOption: true,
    });

    tsCity.trigger('change', tsCity.getValue());
}

function selectDispatch(type) {
    document.querySelectorAll('.dispatch-card').forEach(c => c.classList.remove('selected'));
    document.querySelectorAll('.dispatch-form').forEach(f => f.classList.add('d-none'));
    document.getElementById('card-' + type).classList.add('selected');
    document.getElementById('form-' + type).classList.remove('d-none');

    if (type === 'mosafir') {
        setTimeout(initDispatchSelects, 50);
        const phoneInput = document.querySelector('#form-mosafir [name="recipient_number"]');
        if (phoneInput && !phoneInput.dataset.phoneListenerAdded) {
            phoneInput.addEventListener('input', validateDispatchPhone);
            phoneInput.dataset.phoneListenerAdded = '1';
        }
    }
    if (type === 'agent') {
        updateAreaDeliveryCost();
    }
}

function resetDispatch() {
    document.querySelectorAll('.dispatch-card').forEach(c => c.classList.remove('selected'));
    document.querySelectorAll('.dispatch-form').forEach(f => f.classList.add('d-none'));
}

function updateAreaDeliveryCost() {
    const select  = document.getElementById('agentAreaSelect');
    const preview = document.getElementById('agentAreaCostPreview');
    const value   = document.getElementById('agentAreaCostValue');
    const selected = select.options[select.selectedIndex];
    const price   = selected?.dataset?.price;

    if (price !== undefined && price !== '') {
        value.textContent = Number(price).toLocaleString('en');
        preview.classList.remove('d-none');
    } else {
        preview.classList.add('d-none');
    }
}

function toggleFailNotes(value) {
    const wrap  = document.getElementById('failNotesWrap');
    const notes = document.getElementById('failNotes');
    const show  = value === 'other';
    wrap.style.display = show ? '' : 'none';
    notes.required = show;
    if (!show) notes.value = '';
}
