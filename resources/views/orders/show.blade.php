@extends('layouts.app')

@use('Illuminate\Support\Facades\Storage')

@section('title', 'طلب #' . $order->id)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">الطلبات</a></li>
    <li class="breadcrumb-item active">طلب #{{ $order->id }}</li>
@endsection

@php
    function waLink(string $phone): string {
        $digits = preg_replace('/\D/', '', $phone);
        $last7  = substr($digits, -7);
        return 'https://wa.me/218' . $last7;
    }
@endphp

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="badge fs-6 bg-{{ $order->status_color }}-subtle text-{{ $order->status_color }} border border-{{ $order->status_color }}-subtle px-3 py-2">
            {{ $order->status_label }}
        </span>

        @if($order->status === 'pending' && Auth::user()->can_access('orders.approve'))
            <button type="button" class="btn btn-success btn-sm"
                    data-bs-toggle="modal" data-bs-target="#approveModal">
                <i class="ti ti-check me-1"></i> موافقة
            </button>

            <button type="button" class="btn btn-danger btn-sm"
                    data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="ti ti-x me-1"></i> رفض
            </button>
        @endif

        @if($order->status === 'processing' && Auth::user()->can_access('orders.deliver'))
            <button type="button" class="btn btn-info btn-sm text-white"
                    data-bs-toggle="modal" data-bs-target="#dispatchModal">
                <i class="ti ti-truck-delivery me-1"></i> إحالة للتوصيل
            </button>
        @endif

        @if($order->status === 'returning' && $order->agent_id && Auth::user()->can_access('orders.returned'))
            <button type="button" class="btn btn-secondary btn-sm"
                    data-bs-toggle="modal" data-bs-target="#acceptReturnModal">
                <i class="ti ti-package-import me-1"></i> تأكيد الاستلام
            </button>
        @endif

        @if($order->status === 'with_agent' && $order->agent_id && Auth::user()->can_access('orders.deliver'))
            <button type="button" class="btn btn-success btn-sm"
                    data-bs-toggle="modal" data-bs-target="#deliverModal">
                <i class="ti ti-check me-1"></i> تم التسليم
            </button>

            <button type="button" class="btn btn-warning btn-sm"
                    data-bs-toggle="modal" data-bs-target="#failDeliveryModal">
                <i class="ti ti-x me-1"></i> تعذر التسليم
            </button>
        @endif
    </div>
</div>


<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header p-0 border-bottom">
        <ul class="nav nav-tabs card-header-tabs px-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-customer" type="button">
                    <i class="ti ti-user me-1"></i> بيانات الزبون
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-products" type="button">
                    <i class="ti ti-box me-1"></i> المنتجات
                    <span class="badge bg-secondary ms-1">{{ $order->items->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-marketer" type="button">
                    <i class="ti ti-user-check me-1"></i> المسوقة
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-summary" type="button">
                    <i class="ti ti-receipt me-1"></i> الملخص المالي
                </button>
            </li>
            @if($mosafirParcel)
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-mosafir" type="button">
                    <img src="{{ asset('mosafer.svg') }}" style="height:18px;margin-left:4px"> 
                </button>
            </li>
            @endif
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-logs" type="button">
                    <i class="ti ti-history me-1"></i> سجل الطلب
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content">

            {{-- Tab: Customer --}}
            <div class="tab-pane fade show active" id="tab-customer" role="tabpanel">
                <dl class="info-list">
                    <div class="info-row">
                        <dt>اسم الزبون</dt>
                        <dd>{{ $order->customer_name }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>رقم الهاتف</dt>
                        <dd class="d-flex align-items-center gap-2">
                            <span>{{ $order->customer_phone }}</span>
                            <a href="{{ waLink($order->customer_phone) }}" target="_blank" class="btn-wa" title="واتساب">
                                <i class="ti ti-brand-whatsapp"></i>
                            </a>
                        </dd>
                    </div>
                    @if($order->customer_phone2)
                    <div class="info-row">
                        <dt>هاتف احتياطي</dt>
                        <dd class="d-flex align-items-center gap-2">
                            <span>{{ $order->customer_phone2 }}</span>
                            <a href="{{ waLink($order->customer_phone2) }}" target="_blank" class="btn-wa" title="واتساب">
                                <i class="ti ti-brand-whatsapp"></i>
                            </a>
                        </dd>
                    </div>
                    @endif
                    <div class="info-row">
                        <dt>المدينة</dt>
                        <dd>{{ $order->city_name }}</dd>
                    </div>
                    @if($order->sub_city_name)
                    <div class="info-row">
                        <dt>المنطقة</dt>
                        <dd>{{ $order->sub_city_name }}</dd>
                    </div>
                    @endif
                    <div class="info-row">
                        <dt>العنوان</dt>
                        <dd>{{ $order->address }}</dd>
                    </div>
                    @if($order->notes)
                    <div class="info-row">
                        <dt>ملاحظات</dt>
                        <dd class="text-muted">{{ $order->notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Tab: Products --}}
            <div class="tab-pane fade" id="tab-products" role="tabpanel">
                <div class="row g-3 p-3">
                    @foreach($order->items as $item)
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="order-product-card">
                            @php $img = $item->product?->primaryImage?->url ?? null; @endphp
                            @if($img)
                                <img src="{{ $img }}" alt="{{ $item->product_name }}" class="order-product-img">
                            @else
                                <div class="order-product-img-placeholder">
                                    <i class="ti ti-photo"></i>
                                </div>
                            @endif
                            <div class="order-product-body">
                                <div class="order-product-name">{{ $item->product_name }}</div>
                                @if($item->product?->code)
                                    <div class="order-product-code">{{ $item->product->code }}</div>
                                @endif
                                <div class="order-product-meta">
                                    <span class="order-product-unit">{{ number_format($item->product_price) }} د.ل / وحدة</span>
                                    <span class="order-product-qty">× {{ $item->quantity }}</span>
                                </div>
                                <div class="order-product-total">{{ number_format($item->total) }} د.ل</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Tab: Marketer --}}
            <div class="tab-pane fade" id="tab-marketer" role="tabpanel">
                <dl class="info-list">
                    <div class="info-row">
                        <dt>الاسم</dt>
                        <dd>{{ $order->marketer->first_name }} {{ $order->marketer->last_name }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>رقم الهاتف</dt>
                        <dd class="d-flex align-items-center gap-2">
                            <span>{{ $order->marketer->phone }}</span>
                            <a href="{{ waLink($order->marketer->phone) }}" target="_blank" class="btn-wa" title="واتساب">
                                <i class="ti ti-brand-whatsapp"></i>
                            </a>
                        </dd>
                    </div>
                    @if($order->marketer->email)
                    <div class="info-row">
                        <dt>البريد الإلكتروني</dt>
                        <dd>{{ $order->marketer->email }}</dd>
                    </div>
                    @endif
                    <div class="info-row">
                        <dt>الحالة</dt>
                        <dd>
                            @if($order->marketer->status === 'approved' && $order->marketer->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">نشطة</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">غير نشطة</span>
                            @endif
                        </dd>
                    </div>
                    <div class="info-row">
                        <dt>عدد الطلبات</dt>
                        <dd>{{ $order->marketer->orders()->count() }} طلب</dd>
                    </div>
                    @if(Auth::user()->can_access('marketers.view'))
                    <div class="info-row border-0">
                        <dt></dt>
                        <dd>
                            <a href="{{ route('marketers.show', $order->marketer) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-external-link me-1"></i> عرض ملف المسوقة
                            </a>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Tab: Financial Summary --}}
            <div class="tab-pane fade" id="tab-summary" role="tabpanel">
                @php
                    $costTotal = $order->items->sum(fn($i) =>
                        $i->quantity * ((float) $i->product_cost ?: (float) ($i->product?->price ?? 0))
                    );
                    $profit = $order->products_total - $costTotal;
                @endphp
                <dl class="info-list">
                    <div class="info-row">
                        <dt>إجمالي البيع</dt>
                        <dd>{{ number_format($order->products_total) }} د.ل</dd>
                    </div>
                    <div class="info-row">
                        <dt>إجمالي التكلفة</dt>
                        <dd class="text-muted">{{ number_format($costTotal) }} د.ل</dd>
                    </div>
                    <div class="info-row">
                        <dt class="fw-semibold">ربح المسوق</dt>
                        <dd class="fw-bold" style="color:{{ $profit >= 0 ? '#16a34a' : '#dc2626' }}">
                            {{ number_format($profit) }} د.ل
                        </dd>
                    </div>
                    <div class="info-row">
                        <dt>رسوم التوصيل</dt>
                        <dd>{{ number_format($order->delivery_cost) }} د.ل</dd>
                    </div>
                    <div class="info-row">
                        <dt class="fw-bold" style="color:#1a1a1a">الإجمالي الكلي</dt>
                        <dd class="fw-bold fs-5" style="color:#4a2619">{{ number_format($order->grand_total) }} د.ل</dd>
                    </div>
                    @if($order->has_deposit && $order->deposit_amount)
                    <div class="info-row">
                        <dt>العربون المدفوع</dt>
                        <dd class="text-muted">
                            − {{ number_format($order->deposit_amount) }} د.ل
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle ms-1 fw-normal">
                                {{ $order->deposit_payer === 'company' ? 'على حساب الشركة' : 'على حساب المسوق' }}
                            </span>
                            @if($order->deposit_proof)
                                <a href="{{ Storage::url($order->deposit_proof) }}" target="_blank" class="ms-1 text-decoration-none small">
                                    <i class="ti ti-file-check"></i> إثبات التحويل
                                </a>
                            @endif
                        </dd>
                    </div>
                    <div class="info-row">
                        <dt class="fw-bold text-success">المبلغ المستحق التحصيل</dt>
                        <dd class="fw-bold fs-5 text-success">{{ number_format($order->collection_amount) }} د.ل</dd>
                    </div>
                    @endif
                    <div class="info-row">
                        <dt>تاريخ الطلب</dt>
                        <dd class="text-muted">{{ dt($order->created_at) }}</dd>
                    </div>
                    @if($order->approved_at)
                    <div class="info-row">
                        <dt>تمت الموافقة بواسطة</dt>
                        <dd class="text-success fw-semibold">{{ $order->approvedBy?->name }} — {{ dt($order->approved_at) }}</dd>
                    </div>
                    @endif
                    @if($order->rejected_at)
                    <div class="info-row">
                        <dt>تم الرفض بواسطة</dt>
                        <dd class="text-danger fw-semibold">{{ $order->rejectedBy?->name }} — {{ dt($order->rejected_at) }}</dd>
                    </div>
                    <div class="info-row border-0">
                        <dt>سبب الرفض</dt>
                        <dd class="text-muted">{{ $order->rejected_reason }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Tab: Mosafir --}}
            @if($mosafirParcel)
            <div class="tab-pane fade" id="tab-mosafir" role="tabpanel">
                <div class="p-3">
                    @include('orders.partials._mosafir')
                </div>
            </div>
            @endif

            {{-- Tab: Logs --}}
            <div class="tab-pane fade" id="tab-logs" role="tabpanel">
                <div class="p-3">
                    @include('orders.partials._logs')
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Approve Modal --}}
@if($order->status === 'pending' && Auth::user()->can_access('orders.approve'))
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('orders.approve', $order) }}">
                @csrf @method('PATCH')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-success">
                        <i class="ti ti-check-circle me-1"></i> الموافقة على الطلب #{{ $order->id }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">هل تريد الموافقة على هذا الطلب؟</p>
                    <p class="text-muted small mb-0">سيتغير الطلب إلى حالة <strong>قيد التجهيز</strong> وسيُسجَّل في السجل.</p>

                    @if($order->has_deposit && $order->deposit_payer === 'company' && $order->deposit_amount > 0)
                    <div class="alert alert-warning mt-3 mb-0 py-2 px-3 d-flex align-items-center gap-2">
                        <i class="ti ti-cash fs-5"></i>
                        <span>هذا الطلب فيه عربون <strong>{{ number_format($order->deposit_amount) }} د.ل</strong> على حساب الشركة.</span>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold">اختر الخزينة <span class="text-danger">*</span></label>
                        <select name="vault_id" class="form-select @error('vault_id') is-invalid @enderror" required>
                            <option value="">— اختر خزينة —</option>
                            @foreach($vaults as $vault)
                                <option value="{{ $vault->id }}" {{ old('vault_id') == $vault->id ? 'selected' : '' }}>
                                    {{ $vault->name }} ({{ number_format($vault->current_balance) }} د.ل)
                                </option>
                            @endforeach
                        </select>
                        @error('vault_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-check me-1"></i> تأكيد الموافقة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Reject Modal --}}
@if($order->status === 'pending' && Auth::user()->can_access('orders.approve'))
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('orders.reject', $order) }}">
                @csrf @method('PATCH')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger">
                        <i class="ti ti-x-circle me-1"></i> رفض الطلب #{{ $order->id }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">سبب الرفض <span class="text-danger">*</span></label>
                    <textarea name="rejected_reason" class="form-control" rows="4"
                              placeholder="اكتب سبب الرفض بوضوح..." required></textarea>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-x me-1"></i> تأكيد الرفض
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Dispatch Modal --}}
@if($order->status === 'processing' && Auth::user()->can_access('orders.deliver'))
<div class="modal fade" id="dispatchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">
                    <i class="ti ti-truck-delivery me-1 text-info"></i> إحالة الطلب #{{ $order->id }} للتوصيل
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="text-muted small mb-4">اختر جهة التوصيل</p>

                <div class="row g-3" id="dispatchOptions">

                    {{-- Musafir Option --}}
                    <div class="col-12 col-sm-6">
                        <div class="dispatch-card" onclick="selectDispatch('mosafir')" id="card-mosafir">
                            <img src="{{ asset('mosafer.svg') }}" alt="المسافر" class="dispatch-logo mb-3">
                            <div class="fw-bold fs-6">شركة المسافر</div>
                            <div class="text-muted small mt-1">إنشاء شحنة تلقائياً عبر API المسافر</div>
                        </div>
                    </div>

                    {{-- Local Agent Option --}}
                    <div class="col-12 col-sm-6">
                        <div class="dispatch-card" onclick="selectDispatch('agent')" id="card-agent">
                            <i class="ti ti-motorbike dispatch-icon mb-3"></i>
                            <div class="fw-bold fs-6">مندوب محلي</div>
                            <div class="text-muted small mt-1">تعيين أحد المندوبين لتوصيل الطلب</div>
                        </div>
                    </div>

                </div>

                {{-- Musafir Form --}}
                <form method="POST" action="{{ route('orders.dispatch', $order) }}" id="form-mosafir" class="dispatch-form mt-4 d-none">
                    @csrf
                    <input type="hidden" name="type" value="mosafir">
                    <input type="hidden" name="delivery_on" value="customer">

                    @php
                        $productNames = $order->items->map(fn($i) => $i->product_name)->implode('، ');
                    @endphp

                    <div class="row g-3 mb-3">

                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold">اسم الزبون <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control"
                                   value="{{ $order->customer_name }}" required>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_number" class="form-control"
                                   value="{{ $order->customer_phone }}" required inputmode="numeric">
                        </div>

                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold">المدينة <span class="text-danger">*</span></label>
                            <select id="ms-city" name="to_city_id" class="form-select" required>
                                <option value="">— اختر المدينة —</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city['id'] }}"
                                            data-subcities="{{ json_encode($city['sub_cities'] ?? []) }}"
                                            {{ (string)$order->city_id === (string)$city['id'] ? 'selected' : '' }}>
                                        {{ $city['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold">المنطقة الفرعية</label>
                            <select id="ms-subcity" name="sub_city_id" class="form-select">
                                <option value="">— اختر المنطقة —</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">الوصف <span class="text-danger">*</span></label>
                            <input type="text" name="desc" class="form-control"
                                   value="{{ $productNames }}" required>
                        </div>

                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-outline-secondary" onclick="resetDispatch()">رجوع</button>
                        <button type="submit" class="btn btn-info text-white">
                            <i class="ti ti-send me-1"></i> تأكيد الإحالة للمسافر
                        </button>
                    </div>
                </form>

                {{-- Agent Form --}}
                <form method="POST" action="{{ route('orders.dispatch', $order) }}" id="form-agent" class="dispatch-form mt-4 d-none">
                    @csrf
                    <input type="hidden" name="type" value="agent">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">اختر المندوب <span class="text-danger">*</span></label>
                        <select name="agent_id" class="form-select" required>
                            <option value="">— اختر مندوباً —</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }} — {{ $agent->phone }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-outline-secondary" onclick="resetDispatch()">رجوع</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i> تأكيد التعيين
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endif

{{-- Accept Return Modal --}}
@if($order->status === 'returning' && $order->agent_id && Auth::user()->can_access('orders.returned'))
<div class="modal fade" id="acceptReturnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('orders.accept-return', $order) }}">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">
                        <i class="ti ti-package-import me-1 text-secondary"></i> تأكيد استلام المسترد — طلب #{{ $order->id }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">هل تأكد من استلام الطلب المسترد؟</p>
                    <p class="text-muted small mb-0">سيتغير الطلب إلى حالة <strong>مسترد</strong> وستُعاد كميات المنتجات تلقائياً إلى المخزون.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-secondary">
                        <i class="ti ti-package-import me-1"></i> تأكيد الاستلام
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Deliver Modal --}}
@if($order->status === 'with_agent' && $order->agent_id && Auth::user()->can_access('orders.deliver'))
<div class="modal fade" id="deliverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('orders.deliver', $order) }}">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-success">
                        <i class="ti ti-circle-check me-1"></i> تأكيد التسليم — طلب #{{ $order->id }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">هل تأكد أن الطلب تم تسليمه للزبون؟</p>
                    <p class="text-muted small mb-0">سيتغير الطلب إلى حالة <strong>تم التسليم</strong> وسيُسجَّل في السجل.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-check me-1"></i> تأكيد التسليم
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Fail Delivery Modal --}}
@if($order->status === 'with_agent' && $order->agent_id && Auth::user()->can_access('orders.deliver'))
@php $reasons = \App\DTOs\FailDeliveryData::reasons(); @endphp
<div class="modal fade" id="failDeliveryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('orders.fail-delivery', $order) }}">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-warning">
                        <i class="ti ti-alert-triangle me-1"></i> تعذر التسليم — طلب #{{ $order->id }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">اختر سبب تعذر التسليم. سيتغير الطلب إلى حالة <strong>قيد الاسترداد</strong>.</p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">السبب <span class="text-danger">*</span></label>
                        <div class="d-flex flex-column gap-2" id="failReasonOptions">
                            @foreach($reasons as $value => $label)
                            <label class="fail-reason-option d-flex align-items-center gap-2 p-3 rounded-3 border cursor-pointer"
                                   style="cursor:pointer">
                                <input type="radio" name="reason" value="{{ $value }}"
                                       class="form-check-input mt-0 flex-shrink-0"
                                       onchange="toggleFailNotes(this.value)"
                                       required>
                                <span class="fw-semibold" style="font-size:.92rem">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-1" id="failNotesWrap" style="display:none">
                        <label class="form-label fw-semibold">التفاصيل <span class="text-danger">*</span></label>
                        <textarea name="notes" id="failNotes" class="form-control" rows="3"
                                  placeholder="اكتب تفاصيل إضافية..." maxlength="300"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="ti ti-arrow-back-up me-1"></i> تأكيد — قيد الاسترداد
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/admin/orders.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="{{ asset('assets/js/admin/orders.js') }}"></script>
@if($errors->has('vault_id'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('approveModal')).show();
    });
</script>
@endif
@endpush
