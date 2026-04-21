@extends('layouts.app')

@section('title', 'المندوب — ' . $agent->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agents.index') }}">المندوبين</a></li>
    <li class="breadcrumb-item active">{{ $agent->name }}</li>
@endsection

@section('content')

{{-- Header --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="flex-grow-1">
                <h4 class="mb-1 fw-bold">{{ $agent->name }}</h4>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if($agent->is_active)
                        <span class="badge bg-success-subtle text-success">
                            <i class="ti ti-circle-check me-1"></i>مفعّل
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary">
                            <i class="ti ti-circle-x me-1"></i>معطّل
                        </span>
                    @endif
                    <span class="text-muted small">
                        <i class="ti ti-calendar me-1"></i>أُضيف في {{ $agent->created_at->format('Y/m/d') }}
                    </span>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('agents.edit', $agent) }}" class="btn btn-warning btn-sm">
                    <i class="ti ti-pencil me-1"></i> تعديل
                </a>
                <form method="POST" action="{{ route('agents.toggle', $agent) }}" class="d-inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm {{ $agent->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                        <i class="ti {{ $agent->is_active ? 'ti-toggle-right' : 'ti-toggle-left' }} me-1"></i>
                        {{ $agent->is_active ? 'إيقاف' : 'تفعيل' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header p-0 border-bottom">
        <ul class="nav nav-tabs card-header-tabs px-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-info" type="button">
                    <i class="ti ti-user me-1"></i> البيانات
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-balance" type="button">
                    <i class="ti ti-wallet me-1"></i> العهدة
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-orders" type="button">
                    <i class="ti ti-clipboard-list me-1"></i> الطلبات
                    <span class="badge bg-secondary ms-1">{{ $agent->orders_count }}</span>
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content">

            {{-- Tab: Info --}}
            <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
                <dl class="info-list">
                    <div class="info-row">
                        <dt>الاسم</dt>
                        <dd class="fw-semibold">{{ $agent->name }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>رقم الهاتف</dt>
                        <dd class="d-flex align-items-center gap-2">
                            <span>{{ $agent->phone }}</span>
                            @php
                                $last7 = substr(preg_replace('/\D/', '', $agent->phone), -7);
                            @endphp
                            <a href="https://wa.me/218{{ $last7 }}" target="_blank"
                               class="text-success text-decoration-none" title="واتساب">
                                <i class="ti ti-brand-whatsapp fs-5"></i>
                            </a>
                        </dd>
                    </div>
                    @if($agent->phone2)
                    <div class="info-row">
                        <dt>هاتف احتياطي</dt>
                        <dd class="d-flex align-items-center gap-2">
                            <span>{{ $agent->phone2 }}</span>
                            @php $last7b = substr(preg_replace('/\D/', '', $agent->phone2), -7); @endphp
                            <a href="https://wa.me/218{{ $last7b }}" target="_blank"
                               class="text-success text-decoration-none" title="واتساب">
                                <i class="ti ti-brand-whatsapp fs-5"></i>
                            </a>
                        </dd>
                    </div>
                    @endif
                    <div class="info-row">
                        <dt>الحالة</dt>
                        <dd>
                            @if($agent->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">مفعّل</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">معطّل</span>
                            @endif
                        </dd>
                    </div>
                    <div class="info-row">
                        <dt>عدد الطلبات</dt>
                        <dd>{{ $agent->orders_count }} طلب</dd>
                    </div>
                    <div class="info-row border-0">
                        <dt>تاريخ الإضافة</dt>
                        <dd class="text-muted">{{ dt($agent->created_at) }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Tab: Balance --}}
            <div class="tab-pane fade" id="tab-balance" role="tabpanel">

                {{-- Balance Header --}}
                <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom flex-wrap gap-2">
                    <div>
                        <div class="text-muted small fw-semibold mb-1">العهدة الحالية</div>
                        <div class="fw-bold" style="font-size:1.7rem;color:#4a2619;line-height:1">
                            {{ number_format($agent->balance, 2) }}
                            <span class="fs-6 text-muted fw-normal">د.ل</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#depositAgentModal">
                            <i class="ti ti-arrow-bar-to-down me-1"></i> إيداع
                        </button>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#withdrawalAgentModal">
                            <i class="ti ti-arrow-bar-up me-1"></i> سحب
                        </button>
                    </div>
                </div>

                {{-- Transactions --}}
                @if($transactions->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-file-text fs-1 d-block mb-2"></i>
                        لا توجد حركات مالية بعد
                    </div>
                @else
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>التاريخ</th>
                                    <th>النوع</th>
                                    <th>الوصف</th>
                                    <th>الخزينة</th>
                                    <th class="text-center">القيمة</th>
                                    <th class="text-center">العهدة بعد</th>
                                    <th class="text-muted small">بواسطة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $tx)
                                <tr>
                                    <td class="text-muted small">{{ $tx->date->format('Y-m-d') }}</td>
                                    <td>
                                        @if($tx->type === 'deposit')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="ti ti-arrow-bar-to-down me-1"></i>إيداع
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                <i class="ti ti-arrow-bar-up me-1"></i>سحب
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $tx->description }}</td>
                                    <td class="text-muted small">{{ $tx->vault?->name ?? '—' }}</td>
                                    <td class="text-center fw-bold {{ $tx->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                                        {{ $tx->type === 'deposit' ? '+' : '-' }}{{ number_format($tx->amount, 2) }} د.ل
                                    </td>
                                    <td class="text-center fw-semibold" style="color:#4a2619">
                                        {{ number_format($tx->balance_after, 2) }} د.ل
                                    </td>
                                    <td class="text-muted small">{{ $tx->user?->name ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile --}}
                    <div class="d-md-none">
                        @foreach($transactions as $tx)
                        <div class="border-bottom px-3 py-3">
                            <div class="d-flex align-items-start justify-content-between mb-1">
                                <div>
                                    @if($tx->type === 'deposit')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle mb-1">إيداع</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle mb-1">سحب</span>
                                    @endif
                                    <div class="text-muted small">{{ $tx->description }}</div>
                                    @if($tx->vault)
                                        <div class="text-muted small"><i class="ti ti-building-bank me-1"></i>{{ $tx->vault->name }}</div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold {{ $tx->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                                        {{ $tx->type === 'deposit' ? '+' : '-' }}{{ number_format($tx->amount, 2) }} د.ل
                                    </div>
                                    <div class="text-muted small">{{ $tx->date->format('Y-m-d') }}</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-1">
                                <span class="text-muted small">العهدة بعد الحركة</span>
                                <span class="fw-bold small" style="color:#4a2619">{{ number_format($tx->balance_after, 2) }} د.ل</span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="px-3 py-3">{{ $transactions->links() }}</div>
                @endif

            </div>

            {{-- Tab: Orders --}}
            <div class="tab-pane fade" id="tab-orders" role="tabpanel">
                @if($orders->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-clipboard-list fs-1 d-block mb-2"></i>
                        لا توجد طلبات مسندة لهذا المندوب
                    </div>
                @else
                    {{-- Desktop --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الزبون</th>
                                    <th>المسوقة</th>
                                    <th class="text-center">الحالة</th>
                                    <th class="text-center">الإجمالي</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr style="cursor:pointer" onclick="location.href='{{ route('orders.show', $order) }}'">
                                    <td class="text-muted small">#{{ $order->id }}</td>
                                    <td class="fw-semibold">{{ $order->customer_name }}</td>
                                    <td class="text-muted small">{{ $order->marketer->first_name }} {{ $order->marketer->last_name }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $order->status_color }}-subtle text-{{ $order->status_color }} border border-{{ $order->status_color }}-subtle">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-center fw-semibold" style="color:#4a2619">{{ number_format($order->grand_total) }} د.ل</td>
                                    <td class="text-muted small">{{ dt($order->created_at, false) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile --}}
                    <div class="d-md-none">
                        @foreach($orders as $order)
                        <a href="{{ route('orders.show', $order) }}" class="text-decoration-none d-block border-bottom px-3 py-3">
                            <div class="d-flex align-items-start justify-content-between mb-1">
                                <span class="fw-semibold text-dark">#{{ $order->id }} — {{ $order->customer_name }}</span>
                                <span class="badge bg-{{ $order->status_color }}-subtle text-{{ $order->status_color }} border border-{{ $order->status_color }}-subtle">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">{{ $order->marketer->first_name }} {{ $order->marketer->last_name }} · {{ dt($order->created_at, false) }}</span>
                                <span class="fw-bold" style="color:#4a2619">{{ number_format($order->grand_total) }} د.ل</span>
                            </div>
                        </a>
                        @endforeach
                    </div>

                    <div class="px-3 py-3">{{ $orders->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</div>

{{-- Deposit Modal --}}
<div class="modal fade" id="depositAgentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('agents.transactions.store', $agent) }}" class="modal-content">
            @csrf
            <input type="hidden" name="type" value="deposit">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-arrow-bar-to-down me-2 text-success"></i>إيداع في العهدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">الوصف <span class="text-danger">*</span></label>
                    <input type="text" name="description" class="form-control" placeholder="سبب الإيداع..." required maxlength="500">
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">القيمة <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="amount" class="form-control" placeholder="0.00" step="0.01" min="0.01" required>
                            <span class="input-group-text text-muted small">د.ل</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">التاريخ <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-success btn-sm"><i class="ti ti-check me-1"></i>تأكيد الإيداع</button>
            </div>
        </form>
    </div>
</div>

{{-- Withdrawal Modal --}}
<div class="modal fade" id="withdrawalAgentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('agents.transactions.store', $agent) }}" class="modal-content">
            @csrf
            <input type="hidden" name="type" value="withdrawal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-arrow-bar-up me-2 text-danger"></i>سحب من العهدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">الخزينة <span class="text-danger">*</span></label>
                    <select name="vault_id" class="form-select" required>
                        <option value="">اختر الخزينة...</option>
                        @foreach($vaults as $vault)
                            <option value="{{ $vault->id }}">{{ $vault->name }} — {{ number_format($vault->current_balance, 2) }} د.ل</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">الوصف <span class="text-danger">*</span></label>
                    <input type="text" name="description" class="form-control" placeholder="سبب السحب..." required maxlength="500">
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">القيمة <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="amount" class="form-control" placeholder="0.00" step="0.01" min="0.01" required>
                            <span class="input-group-text text-muted small">د.ل</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">التاريخ <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="alert alert-info mt-3 mb-0 small py-2">
                    <i class="ti ti-info-circle me-1"></i>
                    سيتم إيداع القيمة تلقائياً في الخزينة المحددة كتسوية عهدة.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-check me-1"></i>تأكيد السحب</button>
            </div>
        </form>
    </div>
</div>

@endsection

