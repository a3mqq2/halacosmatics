@extends('layouts.marketer')

@section('title', 'إتمام الطلب')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<link href="{{ asset('assets/css/marketer/checkout.css') }}" rel="stylesheet">
<style>
/* ── Deposit Section ── */
.deposit-section {
    border-top: 1px solid #f0e8e3;
    margin-top: 18px;
    padding-top: 16px;
}
.deposit-toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.deposit-toggle-label {
    font-weight: 700;
    font-size: .95rem;
    color: #1a1a2e;
}
.deposit-toggle-hint {
    font-size: .78rem;
    color: #9ca3af;
    margin-top: 2px;
}
.deposit-switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 26px;
    flex-shrink: 0;
}
.deposit-switch input { opacity: 0; width: 0; height: 0; }
.deposit-switch-slider {
    position: absolute;
    inset: 0;
    background: #d1d5db;
    border-radius: 26px;
    cursor: pointer;
    transition: background .2s;
}
.deposit-switch-slider:before {
    content: '';
    position: absolute;
    width: 20px; height: 20px;
    left: 3px; bottom: 3px;
    background: #fff;
    border-radius: 50%;
    transition: transform .2s;
    box-shadow: 0 1px 4px rgba(0,0,0,.15);
}
.deposit-switch input:checked + .deposit-switch-slider { background: #4a2619; }
.deposit-switch input:checked + .deposit-switch-slider:before { transform: translateX(22px); }

#depositDetails {
    margin-top: 16px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.deposit-block-label {
    font-size: .82rem;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 10px;
}
.deposit-chips {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.deposit-chip {
    cursor: pointer;
}
.deposit-chip input { display: none; }
.deposit-chip span {
    display: block;
    padding: 8px 18px;
    border: 2px solid #e5e7eb;
    border-radius: 50px;
    font-weight: 700;
    font-size: .9rem;
    color: #374151;
    transition: all .15s;
    white-space: nowrap;
}
.deposit-chip input:checked + span {
    border-color: #4a2619;
    background: #4a2619;
    color: #fff;
}
.deposit-payer-options {
    display: flex;
    gap: 10px;
}
.deposit-payer-option {
    flex: 1;
    cursor: pointer;
}
.deposit-payer-option input { display: none; }
.deposit-payer-option > i,
.deposit-payer-option > span {
    pointer-events: none;
}
.deposit-payer-option {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-weight: 700;
    font-size: .88rem;
    color: #374151;
    transition: all .15s;
}
.deposit-payer-option:has(input:checked) {
    border-color: #4a2619;
    background: #fdf8f6;
    color: #4a2619;
}
/* Fallback for browsers without :has */
.deposit-payer-option.selected {
    border-color: #4a2619;
    background: #fdf8f6;
    color: #4a2619;
}

.transfer-info-card {
    background: #f0fdf4;
    border: 1.5px solid #bbf7d0;
    border-radius: 14px;
    padding: 16px;
}
.transfer-info-title {
    font-weight: 700;
    font-size: .88rem;
    color: #15803d;
    margin-bottom: 14px;
}
.transfer-numbers {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.transfer-number-row {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    border-radius: 10px;
    padding: 10px 14px;
    border: 1px solid #dcfce7;
}
.transfer-number-label {
    font-size: .78rem;
    font-weight: 700;
    color: #6b7280;
    flex-shrink: 0;
    width: 38px;
}
.transfer-number {
    flex: 1;
    font-weight: 800;
    font-size: 1rem;
    letter-spacing: .04em;
    color: #1a1a2e;
}
.copy-btn {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    padding: 5px 10px;
    cursor: pointer;
    color: #15803d;
    font-size: .85rem;
    transition: all .15s;
    flex-shrink: 0;
}
.copy-btn:hover { background: #dcfce7; }
.copy-btn.copied { background: #15803d; color: #fff; border-color: #15803d; }

.proof-upload-zone {
    border: 2px dashed #e5e7eb;
    border-radius: 14px;
    padding: 24px 16px;
    text-align: center;
    cursor: pointer;
    background: #fafafa;
    transition: border-color .2s, background .2s;
}
.proof-upload-zone:hover {
    border-color: #4a2619;
    background: #fdf8f6;
}
.proof-upload-zone.has-file {
    border-color: #15803d;
    background: #f0fdf4;
}
</style>
@endpush

@section('content')

<h2 class="page-title">إتمام الطلب</h2>

@if($errors->any())
<div class="alert alert-danger rounded-3 mb-3 py-2 px-3" style="font-size:.9rem">
    <i class="ti ti-alert-circle me-1"></i>
    <strong>يرجى مراجعة البيانات:</strong>
    <ul class="mb-0 mt-1 ps-3">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('marketer.checkout.store') }}" id="checkoutForm" enctype="multipart/form-data">
@csrf
<input type="hidden" name="delivery_type"  id="hiddenDeliveryType" value="local">
<input type="hidden" name="local_area_id"  id="hiddenLocalAreaId">
<input type="hidden" name="city_id"        id="hiddenCityId">
<input type="hidden" name="city_name"      id="hiddenCityName">
<input type="hidden" name="sub_city_id"    id="hiddenSubCityId">
<input type="hidden" name="sub_city_name"  id="hiddenSubCityName">
<input type="hidden" name="delivery_cost"  id="hiddenDeliveryCost" value="0">

<div class="checkout-wrap">

    {{-- Order Form --}}
    <div class="checkout-form-card">
        <h6 class="section-label">بيانات الزبون</h6>

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">اسم الزبون</label>
                <input type="text" name="customer_name" id="customerName" class="form-control" placeholder="الاسم الكامل" value="{{ old('customer_name') }}">
            </div>

            <div class="col-12 col-sm-6">
                <label class="form-label">رقم الهاتف</label>
                <input type="text" name="customer_phone" id="customerPhone" class="form-control" placeholder="09XXXXXXXX" dir="ltr" value="{{ old('customer_phone') }}">
            </div>

            <div class="col-12 col-sm-6">
                <label class="form-label">رقم هاتف احتياطي <span class="text-muted small">(اختياري)</span></label>
                <input type="text" name="customer_phone2" id="customerPhone2" class="form-control" placeholder="09XXXXXXXX" dir="ltr" value="{{ old('customer_phone2') }}">
            </div>

            {{-- ── Delivery Type Selector ── --}}
            <div class="col-12">
                <label class="form-label">نوع التوصيل</label>
                <div class="deposit-payer-options">
                    <label class="deposit-payer-option" id="optLocal">
                        <input type="radio" name="_delivery_type_ui" value="local" checked>
                        <i class="ti ti-map-pin"></i>
                        <span>بنغازي وضواحيها</span>
                    </label>
                    <label class="deposit-payer-option" id="optMosafir">
                        <input type="radio" name="_delivery_type_ui" value="mosafir">
                        <img src="{{ asset('mosafer.svg') }}" style="height:16px"> خارج بنغازي
                    </label>
                </div>
            </div>

            {{-- ── Local Areas (بنغازي) ── --}}
            <div class="col-12" id="localBox">
                <label class="form-label">المنطقة</label>
                <select id="localAreaSelect">
                    <option value="">-- اختر المنطقة --</option>
                    @foreach($localAreas as $area)
                        <option value="{{ $area->id }}"
                                data-price="{{ $area->price }}"
                                data-name="{{ $area->name }}">
                            {{ $area->name }} — {{ $area->price }} د.ل
                        </option>
                    @endforeach
                </select>
                <div id="localAreaError" style="color:#ef4444;font-size:.82rem;margin-top:4px;display:none">يرجى اختيار المنطقة</div>
            </div>

            {{-- ── Mosafir Cities (خارج بنغازي) ── --}}
            <div class="col-12" id="mosafirBox" style="display:none">
                <label class="form-label">المدينة</label>
                <select id="parentCity">
                    <option value="">-- اختر المدينة --</option>
                    @foreach($cities as $city)
                        <option value="{{ $city['id'] }}"
                                data-price="{{ $city['price'] }}"
                                data-sub="{{ json_encode($city['sub_cities'] ?? []) }}">
                            {{ $city['name'] }} — {{ $city['price'] }} د.ل
                        </option>
                    @endforeach
                </select>
                <div id="mosafirCityError" style="color:#ef4444;font-size:.82rem;margin-top:4px;display:none">يرجى اختيار المدينة</div>
            </div>

            <div class="col-12" id="subCityWrap" style="display:none">
                <label class="form-label">المنطقة</label>
                <select id="subCity">
                    <option value="">-- اختر المنطقة --</option>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">العنوان التفصيلي</label>
                <input type="text" name="address" id="customerAddress" class="form-control" placeholder="الشارع، المبنى..." value="{{ old('address') }}">
            </div>

            <div class="col-12">
                <label class="form-label">ملاحظات <span class="text-muted small">(اختياري)</span></label>
                <textarea name="notes" id="orderNotes" class="form-control" rows="3" placeholder="أي تعليمات إضافية للتوصيل...">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Order Summary --}}
    <div class="checkout-summary-card">
        <h6 class="section-label">ملخص الطلب</h6>

        <div class="checkout-items">
            @foreach($cart['items'] as $item)
            <div class="checkout-item">
                <span class="checkout-item-name">{{ $item->product->name }}</span>
                <span class="checkout-item-qty">× {{ $item->quantity }}</span>
                <span class="checkout-item-price">{{ number_format($item->quantity * $item->product->price) }} د.ل</span>
            </div>
            @endforeach
        </div>

        <div class="checkout-totals">
            <div class="checkout-total-row">
                <span>إجمالي المنتجات</span>
                <span>{{ number_format($cart['total']) }} د.ل</span>
            </div>
            <div class="checkout-total-row" id="deliveryRow" style="display:none">
                <span>رسوم التوصيل</span>
                <span id="deliveryCost">0 د.ل</span>
            </div>
            <div class="checkout-grand-total">
                <span>الإجمالي الكلي</span>
                <span id="grandTotal">{{ number_format($cart['total']) }} د.ل</span>
            </div>
            <div class="checkout-total-row text-success fw-bold" id="collectionRow" style="display:none">
                <span>يُستلم عند التسليم</span>
                <span id="collectionAmount">0 د.ل</span>
            </div>
        </div>

        {{-- ── Payment Method ── --}}
        <div class="deposit-section">
            <div class="deposit-block-label" style="margin-bottom:12px">طريقة الدفع</div>
            <div class="deposit-payer-options" style="margin-bottom:0">
                <label class="deposit-payer-option" id="optCash">
                    <input type="radio" name="payment_method" value="cash"
                           onchange="switchPaymentMethod('cash')" checked>
                    <i class="ti ti-cash"></i>
                    <span>كاش</span>
                </label>
                <label class="deposit-payer-option" id="optBankTransfer">
                    <input type="radio" name="payment_method" value="bank_transfer"
                           onchange="switchPaymentMethod('bank_transfer')">
                    <i class="ti ti-building-bank"></i>
                    <span>تحويل مصرفي</span>
                </label>
            </div>
        </div>

        {{-- ── Bank Transfer Details ── --}}
        <div id="bankTransferBox" style="display:none" class="deposit-section">

            <div class="transfer-info-card mb-3">
                <div class="transfer-info-title">
                    <i class="ti ti-building-bank me-1"></i>
                    يرجى إرسال التحويل إلى الحساب التالي
                </div>
                <div class="transfer-numbers">
                    <div class="transfer-number-row">
                        <div style="flex:1;min-width:0">
                            <div style="font-size:.75rem;font-weight:700;color:#6b7280;margin-bottom:4px">مصرف التجارة والتنمية</div>
                            <div style="font-size:.75rem;color:#6b7280;margin-bottom:6px">أيمن محمد صالحين أبوفانه</div>
                            <div class="transfer-number" dir="ltr">0112575429001</div>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:6px;flex-shrink:0">
                            <button type="button" class="copy-btn" onclick="copyBankMessage(this)" title="نسخ الرسالة كاملة">
                                <i class="ti ti-copy"></i>
                            </button>
                            <button type="button" class="copy-btn" onclick="shareWhatsApp()" title="إرسال عبر واتساب"
                                    style="background:#dcfce7;border-color:#86efac;color:#15803d">
                                <i class="ti ti-brand-whatsapp"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="deposit-block">
                <div class="deposit-block-label">إيصال التحويل <span class="text-danger">*</span></div>
                <div class="proof-upload-zone" id="paymentProofZone" onclick="document.getElementById('paymentProofInput').click()">
                    <input type="file" name="payment_proof" id="paymentProofInput"
                           accept=".jpg,.jpeg,.png" style="display:none"
                           onchange="previewPaymentProof(this)">
                    <div id="paymentProofPlaceholder">
                        <i class="ti ti-camera-upload" style="font-size:2rem;color:#9ca3af;display:block;margin-bottom:8px"></i>
                        <div style="font-weight:700;font-size:.9rem">ارفع صورة إيصال التحويل</div>
                        <div style="font-size:.78rem;color:#9ca3af;margin-top:4px">JPG, PNG — حتى 5MB</div>
                    </div>
                    <div id="paymentProofPreview" style="display:none">
                        <img id="paymentProofImg" style="max-height:140px;border-radius:10px;object-fit:cover">
                        <div id="paymentProofName" style="font-size:.8rem;font-weight:700;margin-top:6px;color:#374151"></div>
                        <button type="button" onclick="clearPaymentProof(event)"
                                style="margin-top:6px;font-size:.75rem;color:#ef4444;background:none;border:none;cursor:pointer;font-weight:700">
                            <i class="ti ti-x me-1"></i>إزالة
                        </button>
                    </div>
                </div>
            </div>

            <div class="deposit-block mt-3">
                <div class="deposit-block-label">هل يشمل سعر التوصيل؟</div>
                <div class="deposit-payer-options">
                    <label class="deposit-payer-option" id="optDeliveryYes">
                        <input type="radio" name="delivery_included" value="1" checked>
                        <i class="ti ti-truck"></i>
                        <span>نعم — شامل التوصيل</span>
                    </label>
                    <label class="deposit-payer-option" id="optDeliveryNo">
                        <input type="radio" name="delivery_included" value="0">
                        <i class="ti ti-package"></i>
                        <span>لا — المنتجات فقط</span>
                    </label>
                </div>
            </div>

        </div>

        {{-- ── Deposit Section (cash only) ── --}}
        <div id="depositSection" class="deposit-section">

            <div class="deposit-toggle-row">
                <div>
                    <div class="deposit-toggle-label">هل يوجد عربون؟</div>
                    <div class="deposit-toggle-hint">مبلغ مقدّم يدفعه الزبون</div>
                </div>
                <label class="deposit-switch">
                    <input type="checkbox" name="has_deposit" id="hasDeposit" value="1"
                           onchange="toggleDeposit(this.checked)">
                    <span class="deposit-switch-slider"></span>
                </label>
            </div>

            <div id="depositDetails" style="display:none">

                {{-- Amount chips --}}
                <div class="deposit-block">
                    <div class="deposit-block-label">قيمة العربون</div>
                    <div class="deposit-chips">
                        @foreach([5, 10, 20, 30] as $amt)
                        <label class="deposit-chip">
                            <input type="radio" name="deposit_amount" value="{{ $amt }}"
                                   onchange="updateDepositTotal()">
                            <span>{{ $amt }} د.ل</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Payer --}}
                <div class="deposit-block">
                    <div class="deposit-block-label">العربون على حساب</div>
                    <div class="deposit-payer-options">
                        <label class="deposit-payer-option" id="optMarketer">
                            <input type="radio" name="deposit_payer" value="marketer"
                                   onchange="switchPayer('marketer')" checked>
                            <i class="ti ti-user"></i>
                            <span>على حسابي</span>
                        </label>
                        <label class="deposit-payer-option" id="optCompany">
                            <input type="radio" name="deposit_payer" value="company"
                                   onchange="switchPayer('company')">
                            <i class="ti ti-building"></i>
                            <span>على حساب الشركة</span>
                        </label>
                    </div>
                </div>

                {{-- Company transfer instructions --}}
                <div id="companyTransferBox" style="display:none">

                    <div class="transfer-info-card">
                        <div class="transfer-info-title">
                            <i class="ti ti-transfer me-1"></i>
                            يرجى تحويل مبلغ العربون على أحد الأرقام التالية
                        </div>
                        <div class="transfer-numbers">
                            <div class="transfer-number-row">
                                <span class="transfer-number-label">رقم 1</span>
                                <span class="transfer-number" dir="ltr">0916867739</span>
                                <button type="button" class="copy-btn" onclick="copyNumber('0916867739', this)">
                                    <i class="ti ti-copy"></i>
                                </button>
                            </div>
                            <div class="transfer-number-row">
                                <span class="transfer-number-label">رقم 2</span>
                                <span class="transfer-number" dir="ltr">0942880719</span>
                                <button type="button" class="copy-btn" onclick="copyNumber('0942880719', this)">
                                    <i class="ti ti-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="deposit-block mt-3">
                        <div class="deposit-block-label">إثبات التحويل <span class="text-danger">*</span></div>
                        <div class="proof-upload-zone" id="proofZone" onclick="document.getElementById('proofInput').click()">
                            <input type="file" name="deposit_proof" id="proofInput"
                                   accept=".jpg,.jpeg,.png" style="display:none"
                                   onchange="previewProof(this)">
                            <div id="proofPlaceholder">
                                <i class="ti ti-camera-upload" style="font-size:2rem;color:#9ca3af;display:block;margin-bottom:8px"></i>
                                <div style="font-weight:700;font-size:.9rem">ارفع صورة إثبات التحويل</div>
                                <div style="font-size:.78rem;color:#9ca3af;margin-top:4px">JPG, PNG — حتى 5MB</div>
                            </div>
                            <div id="proofPreview" style="display:none">
                                <img id="proofImg" style="max-height:140px;border-radius:10px;object-fit:cover">
                                <div id="proofName" style="font-size:.8rem;font-weight:700;margin-top:6px;color:#374151"></div>
                                <button type="button" onclick="clearProof(event)"
                                        style="margin-top:6px;font-size:.75rem;color:#ef4444;background:none;border:none;cursor:pointer;font-weight:700">
                                    <i class="ti ti-x me-1"></i>إزالة
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        </div>{{-- end depositSection --}}

        <button type="submit" class="btn-place-order mt-3" id="placeOrderBtn">
            <i class="ti ti-send me-1"></i> إرسال الطلب
        </button>

        <a href="{{ route('marketer.cart') }}" class="btn-back-cart">
            <i class="ti ti-arrow-right me-1"></i> العودة للسلة
        </a>
    </div>

</div>
</form>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    const productTotal  = {{ $cart['total'] }};
    const deliveryRow   = document.getElementById('deliveryRow');
    const deliveryCostEl = document.getElementById('deliveryCost');
    const grandTotalEl  = document.getElementById('grandTotal');
    const subCityWrap   = document.getElementById('subCityWrap');

    const tsOptions = {
        placeholder: 'ابحث أو اختر...',
        searchField: ['text'],
        render: { no_results: () => '<div class="no-results">لا توجد نتائج</div>' },
    };

    // ── Local Areas TomSelect ────────────────────────────────────
    const localAreaTS = new TomSelect('#localAreaSelect', {
        ...tsOptions,
        onChange(value) {
            const opt = document.querySelector(`#localAreaSelect option[value="${value}"]`);
            if (!opt || !value) {
                setDeliveryCost(0);
                document.getElementById('hiddenLocalAreaId').value = '';
                document.getElementById('hiddenCityName').value    = '';
                return;
            }
            document.getElementById('localAreaError').style.display = 'none';
            document.getElementById('hiddenLocalAreaId').value = value;
            document.getElementById('hiddenCityName').value    = opt.dataset.name;
            setDeliveryCost(parseFloat(opt.dataset.price || 0));
        },
    });

    // ── Mosafir TomSelect ────────────────────────────────────────
    const parentTS = new TomSelect('#parentCity', {
        ...tsOptions,
        onChange(value) {
            const opt = document.querySelector(`#parentCity option[value="${value}"]`);
            if (subTS) { subTS.clear(); subTS.clearOptions(); subTS.addOption({ value: '', text: '-- اختر المنطقة --' }); }
            subCityWrap.style.display = 'none';
            document.getElementById('hiddenCityId').value   = '';
            document.getElementById('hiddenCityName').value = '';
            setDeliveryCost(0);
            if (!opt || !value) return;
            const subCities  = JSON.parse(opt.dataset.sub || '[]');
            const parentName = opt.text.split(' — ')[0].trim();
            document.getElementById('mosafirCityError').style.display = 'none';
            if (subCities.length > 0) {
                subCities.forEach(sub => {
                    subTS.addOption({ value: String(sub.id), text: sub.name + ' — ' + sub.price + ' د.ل', price: sub.price, name: sub.name, parentId: value, parentName });
                });
                subTS.refreshOptions(false);
                subCityWrap.style.display = '';
                document.getElementById('hiddenCityId').value   = value;
                document.getElementById('hiddenCityName').value = parentName;
                setDeliveryCost(0);
            } else {
                document.getElementById('hiddenCityId').value   = value;
                document.getElementById('hiddenCityName').value = parentName;
                setDeliveryCost(parseFloat(opt.dataset.price || 0));
            }
        },
    });

    const subTS = new TomSelect('#subCity', {
        ...tsOptions,
        onChange(value) {
            if (!value) {
                document.getElementById('hiddenSubCityId').value   = '';
                document.getElementById('hiddenSubCityName').value = '';
                setDeliveryCost(0);
                return;
            }
            const opt = subTS.options[value];
            document.getElementById('hiddenSubCityId').value   = value;
            document.getElementById('hiddenSubCityName').value = opt?.name ?? '';
            setDeliveryCost(parseFloat(opt?.price || 0));
        },
    });

    // ── Delivery cost setter ─────────────────────────────────────
    function setDeliveryCost(price) {
        document.getElementById('hiddenDeliveryCost').value = price ?? 0;
        if (price > 0) {
            deliveryCostEl.textContent = price.toLocaleString('ar') + ' د.ل';
            deliveryRow.style.display  = '';
            grandTotalEl.textContent   = (productTotal + price).toLocaleString('ar') + ' د.ل';
        } else {
            deliveryRow.style.display  = 'none';
            grandTotalEl.textContent   = productTotal.toLocaleString('ar') + ' د.ل';
        }
        const method = document.querySelector('[name=payment_method]:checked')?.value;
        if (method === 'bank_transfer') updateCollectionDisplay();
        else updateDepositTotal();
    }

    // ── Delivery Type Switch ─────────────────────────────────────
    function switchDeliveryType(type) {
        const localBox   = document.getElementById('localBox');
        const mosafirBox = document.getElementById('mosafirBox');
        const optLocal   = document.getElementById('optLocal');
        const optMosafir = document.getElementById('optMosafir');

        document.getElementById('hiddenDeliveryType').value = type;

        if (type === 'local') {
            localBox.style.display   = '';
            mosafirBox.style.display = 'none';
            subCityWrap.style.display = 'none';
            optLocal.classList.add('selected');
            optMosafir.classList.remove('selected');
            // reset Mosafir fields
            document.getElementById('hiddenCityId').value      = '';
            document.getElementById('hiddenSubCityId').value   = '';
            document.getElementById('hiddenSubCityName').value = '';
            if (parentTS) { parentTS.clear(); }
            if (subTS)    { subTS.clear(); subTS.clearOptions(); }
            // restore local area price if already selected
            const localVal = localAreaTS?.getValue();
            if (localVal) {
                const opt = document.querySelector(`#localAreaSelect option[value="${localVal}"]`);
                if (opt) {
                    document.getElementById('hiddenLocalAreaId').value = localVal;
                    document.getElementById('hiddenCityName').value    = opt.dataset.name;
                    setDeliveryCost(parseFloat(opt.dataset.price || 0));
                    return;
                }
            }
            setDeliveryCost(0);
        } else {
            localBox.style.display   = 'none';
            mosafirBox.style.display = '';
            optMosafir.classList.add('selected');
            optLocal.classList.remove('selected');
            // reset local fields
            document.getElementById('hiddenLocalAreaId').value = '';
            if (localAreaTS) localAreaTS.clear();
            // restore Mosafir price if already selected
            const parentVal = parentTS?.getValue();
            if (parentVal) {
                const opt = document.querySelector(`#parentCity option[value="${parentVal}"]`);
                if (opt) {
                    const subVal = subTS?.getValue();
                    if (subVal) {
                        const subOpt = subTS.options[subVal];
                        setDeliveryCost(parseFloat(subOpt?.price || 0));
                    } else {
                        setDeliveryCost(parseFloat(opt.dataset.price || 0));
                    }
                    return;
                }
            }
            setDeliveryCost(0);
        }
    }

    // ── Payment Method ───────────────────────────────────────────
    function switchPaymentMethod(value) {
        const bankBox      = document.getElementById('bankTransferBox');
        const depositSec   = document.getElementById('depositSection');
        const optCash      = document.getElementById('optCash');
        const optBank      = document.getElementById('optBankTransfer');
        const paymentProof = document.getElementById('paymentProofInput');

        document.querySelectorAll('.deposit-payer-option[id^="opt"]').forEach(el => {
            if (el.id === 'optCash' || el.id === 'optBankTransfer') el.classList.remove('selected');
        });

        if (value === 'bank_transfer') {
            bankBox.style.display    = '';
            depositSec.style.display = 'none';
            optBank.classList.add('selected');
            document.getElementById('hasDeposit').checked = false;
            toggleDeposit(false);
            updateCollectionDisplay();
        } else {
            bankBox.style.display    = 'none';
            depositSec.style.display = '';
            optCash.classList.add('selected');
            clearPaymentProof();
            updateDepositTotal();
        }
    }

    function updateCollectionDisplay() {
        const delivCost     = parseFloat(document.getElementById('hiddenDeliveryCost').value) || 0;
        const base          = {{ $cart['total'] }};
        const grand         = base + delivCost;
        const included      = document.querySelector('[name=delivery_included]:checked')?.value === '1';
        const collectionRow = document.getElementById('collectionRow');
        const collectionEl  = document.getElementById('collectionAmount');

        grandTotalEl.textContent = grand.toLocaleString('ar') + ' د.ل';

        if (!included && delivCost > 0) {
            collectionEl.textContent    = delivCost.toLocaleString('ar') + ' د.ل';
            collectionRow.style.display = '';
        } else {
            collectionRow.style.display = 'none';
        }
    }

    function previewPaymentProof(input) {
        if (! input.files[0]) return;
        const file = input.files[0];
        const zone = document.getElementById('paymentProofZone');
        const img  = document.getElementById('paymentProofImg');
        const name = document.getElementById('paymentProofName');
        zone.style.borderColor = '';
        zone.style.background  = '';
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            name.textContent = file.name;
            document.getElementById('paymentProofPlaceholder').style.display = 'none';
            document.getElementById('paymentProofPreview').style.display     = '';
            zone.classList.add('has-file');
        };
        reader.readAsDataURL(file);
    }

    function clearPaymentProof(e) {
        if (e) e.stopPropagation();
        const input = document.getElementById('paymentProofInput');
        if (input) input.value = '';
        const zone = document.getElementById('paymentProofZone');
        if (! zone) return;
        zone.classList.remove('has-file');
        document.getElementById('paymentProofPlaceholder').style.display = '';
        document.getElementById('paymentProofPreview').style.display     = 'none';
        document.getElementById('paymentProofImg').src                   = '';
    }

    document.addEventListener('DOMContentLoaded', () => {
        switchPaymentMethod('cash');
        switchDeliveryType('local');

        document.querySelectorAll('[name=_delivery_type_ui]').forEach(radio => {
            radio.addEventListener('change', () => switchDeliveryType(radio.value));
        });

        document.querySelectorAll('[name=delivery_included]').forEach(radio => {
            radio.addEventListener('change', updateCollectionDisplay);
        });

        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            let firstError = null;
            const delivType = document.getElementById('hiddenDeliveryType').value;

            if (delivType === 'local') {
                if (!document.getElementById('hiddenLocalAreaId').value) {
                    e.preventDefault();
                    const errEl = document.getElementById('localAreaError');
                    errEl.style.display = '';
                    firstError = errEl;
                }
            } else {
                if (!document.getElementById('hiddenCityId').value) {
                    e.preventDefault();
                    const errEl = document.getElementById('mosafirCityError');
                    errEl.style.display = '';
                    firstError = errEl;
                }
            }

            const method = document.querySelector('[name=payment_method]:checked')?.value;
            if (method === 'bank_transfer') {
                const proofInput = document.getElementById('paymentProofInput');
                if (!proofInput.files || !proofInput.files.length) {
                    e.preventDefault();
                    const zone = document.getElementById('paymentProofZone');
                    zone.style.borderColor = '#ef4444';
                    zone.style.background  = '#fff5f5';
                    firstError = firstError ?? zone;
                }
            }

            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });

    // ── Deposit ──────────────────────────────────────────────────
    function toggleDeposit(on) {
        document.getElementById('depositDetails').style.display = on ? '' : 'none';
        if (!on) {
            document.querySelectorAll('[name=deposit_amount]').forEach(r => r.checked = false);
            document.querySelectorAll('[name=deposit_payer]').forEach(r => r.checked = false);
            switchPayer('marketer');
            clearProof();
        }
        updateDepositTotal();
    }

    function switchPayer(value) {
        document.getElementById('companyTransferBox').style.display = value === 'company' ? '' : 'none';

        document.querySelectorAll('.deposit-payer-option').forEach(el => el.classList.remove('selected'));
        if (value === 'marketer') document.getElementById('optMarketer').classList.add('selected');
        if (value === 'company')  document.getElementById('optCompany').classList.add('selected');

        const proofInput = document.getElementById('proofInput');
        if (value !== 'company') {
            proofInput.removeAttribute('required');
            clearProof();
        } else {
            proofInput.setAttribute('required', '');
        }
    }

    function updateDepositTotal() {
        const hasDeposit      = document.getElementById('hasDeposit').checked;
        const amtInput        = document.querySelector('[name=deposit_amount]:checked');
        const amt             = hasDeposit && amtInput ? parseFloat(amtInput.value) : 0;
        const delivCost       = parseFloat(document.getElementById('hiddenDeliveryCost').value) || 0;
        const base            = {{ $cart['total'] }};
        const grand           = base + delivCost;
        grandTotalEl.textContent = grand.toLocaleString('ar') + ' د.ل';

        const collectionRow = document.getElementById('collectionRow');
        const collectionEl  = document.getElementById('collectionAmount');
        if (hasDeposit && amt > 0) {
            collectionEl.textContent  = (grand - amt).toLocaleString('ar') + ' د.ل';
            collectionRow.style.display = '';
        } else {
            collectionRow.style.display = 'none';
        }
    }

    function previewProof(input) {
        if (!input.files[0]) return;
        const file = input.files[0];
        const zone = document.getElementById('proofZone');
        const img  = document.getElementById('proofImg');
        const name = document.getElementById('proofName');

        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            name.textContent = file.name;
            document.getElementById('proofPlaceholder').style.display = 'none';
            document.getElementById('proofPreview').style.display = '';
            zone.classList.add('has-file');
        };
        reader.readAsDataURL(file);
    }

    function clearProof(e) {
        if (e) e.stopPropagation();
        const input = document.getElementById('proofInput');
        if (input) input.value = '';
        const zone = document.getElementById('proofZone');
        if (!zone) return;
        zone.classList.remove('has-file');
        document.getElementById('proofPlaceholder').style.display = '';
        document.getElementById('proofPreview').style.display = 'none';
        document.getElementById('proofImg').src = '';
    }

    const bankMsg = `نقبل التعامل بالكاش وخدمة تحويل مصرفي بنفس سعر الكاش 💸\n\n📋 بيانات التحويل المصرفي:\n\nمصرف التجارة والتنمية\nاسم صاحب الحساب: أيمن محمد صالحين أبوفانه\nرقم الحساب: 0112575429001\n\n📸 ملاحظة مهمة:\nأي تحويل ضروري يتم إرسال سكرين شوت للفاتورة`;

    function copyBankMessage(btn) {
        navigator.clipboard.writeText(bankMsg).then(() => {
            btn.classList.add('copied');
            btn.innerHTML = '<i class="ti ti-check"></i>';
            setTimeout(() => {
                btn.classList.remove('copied');
                btn.innerHTML = '<i class="ti ti-copy"></i>';
            }, 2000);
        });
    }

    function shareWhatsApp() {
        window.open('https://wa.me/?text=' + encodeURIComponent(bankMsg), '_blank');
    }

    function copyNumber(number, btn) {
        navigator.clipboard.writeText(number).then(() => {
            btn.classList.add('copied');
            btn.innerHTML = '<i class="ti ti-check"></i>';
            setTimeout(() => {
                btn.classList.remove('copied');
                btn.innerHTML = '<i class="ti ti-copy"></i>';
            }, 2000);
        });
    }

</script>
@endpush
