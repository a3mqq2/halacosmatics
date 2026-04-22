@extends('layouts.app')

@section('title', $marketer->first_name . ' ' . $marketer->last_name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('marketers.index') }}">المسوقين</a></li>
    <li class="breadcrumb-item active">{{ $marketer->first_name }} {{ $marketer->last_name }}</li>
@endsection

@section('content')

{{-- Header Card --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3 flex-wrap">
           
            <div class="flex-grow-1">
                <h4 class="mb-1 fw-bold">{{ $marketer->first_name }} {{ $marketer->last_name }}</h4>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted fs-14">
                        <i class="ti ti-phone me-1"></i>{{ $marketer->phone }}
                    </span>
                    @if($marketer->is_active)
                        <span class="badge bg-success-subtle text-success">
                            <i class="ti ti-circle-check me-1"></i>مفعّل
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger">
                            <i class="ti ti-circle-x me-1"></i>موقوف
                        </span>
                    @endif
                </div>
                <small class="text-muted">
                    <i class="ti ti-calendar me-1"></i>أُضيف في {{ $marketer->created_at->format('Y/m/d') }}
                </small>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('marketers.edit', $marketer) }}" class="btn btn-warning btn-sm">
                    <i class="ti ti-pencil me-1"></i>تعديل
                </a>
                <form method="POST" action="{{ route('marketers.toggle', $marketer) }}" class="d-inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm {{ $marketer->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                        <i class="ti {{ $marketer->is_active ? 'ti-toggle-right' : 'ti-toggle-left' }} me-1"></i>
                        {{ $marketer->is_active ? 'إيقاف' : 'تفعيل' }}
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
                <button class="nav-link active fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-contact" type="button">
                    <i class="ti ti-phone me-1"></i> التواصل
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-account" type="button">
                    <i class="ti ti-id-badge me-1"></i> الحساب
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-balance" type="button">
                    <i class="ti ti-wallet me-1"></i> الرصيد
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-orders" type="button">
                    <i class="ti ti-clipboard-list me-1"></i> الطلبات
                    <span class="badge bg-secondary ms-1">{{ $marketer->orders()->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold py-3" data-bs-toggle="tab" data-bs-target="#tab-logs" type="button">
                    <i class="ti ti-history me-1"></i> السجل
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content">

            {{-- Tab: Contact --}}
            <div class="tab-pane fade show active" id="tab-contact" role="tabpanel">
                <ul class="list-group list-group-flush">

                    <li class="list-group-item d-flex align-items-center gap-3 py-3">
                        <span class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="ti ti-phone fs-16"></i>
                        </span>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">رقم الهاتف</small>
                            <span class="fw-semibold">{{ $marketer->phone }}</span>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="tel:{{ $marketer->phone }}" class="btn btn-sm btn-outline-primary" title="اتصال">
                                <i class="ti ti-phone"></i>
                            </a>
                            <a href="https://wa.me/218{{ substr(preg_replace('/\D/', '', $marketer->phone), -7) }}"
                               target="_blank" class="btn btn-sm btn-outline-success" title="واتساب">
                                <i class="ti ti-brand-whatsapp"></i>
                            </a>
                        </div>
                    </li>

                    <li class="list-group-item d-flex align-items-center gap-3 py-3">
                        <span class="avatar-xs bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="ti ti-phone-plus fs-16"></i>
                        </span>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">رقم احتياطي</small>
                            <span class="fw-semibold">{{ $marketer->backup_phone ?? '—' }}</span>
                        </div>
                        @if($marketer->backup_phone)
                            <div class="d-flex gap-1">
                                <a href="tel:{{ $marketer->backup_phone }}" class="btn btn-sm btn-outline-primary" title="اتصال">
                                    <i class="ti ti-phone"></i>
                                </a>
                                <a href="https://wa.me/218{{ substr(preg_replace('/\D/', '', $marketer->backup_phone), -7) }}"
                                   target="_blank" class="btn btn-sm btn-outline-success" title="واتساب">
                                    <i class="ti ti-brand-whatsapp"></i>
                                </a>
                            </div>
                        @endif
                    </li>

                    <li class="list-group-item d-flex align-items-center gap-3 py-3">
                        <span class="avatar-xs bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="ti ti-mail fs-16"></i>
                        </span>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">البريد الإلكتروني</small>
                            <span class="fw-semibold">{{ $marketer->email ?? '—' }}</span>
                        </div>
                        @if($marketer->email)
                            <a href="mailto:{{ $marketer->email }}" class="btn btn-sm btn-outline-warning" title="إرسال بريد">
                                <i class="ti ti-send"></i>
                            </a>
                        @endif
                    </li>

                </ul>
            </div>

            {{-- Tab: Account --}}
            <div class="tab-pane fade" id="tab-account" role="tabpanel">
                <ul class="list-group list-group-flush">


                    <li class="list-group-item d-flex align-items-center gap-3 py-3">
                        <span class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="ti ti-shield-check fs-16"></i>
                        </span>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">حالة الحساب</small>
                            @if($marketer->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning">قيد المراجعة</span>
                            @elseif($marketer->status === 'approved' && $marketer->is_active)
                                <span class="badge bg-success-subtle text-success">مفعّل</span>
                            @elseif($marketer->status === 'approved')
                                <span class="badge bg-secondary-subtle text-secondary">موقوف</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">مرفوض</span>
                            @endif
                        </div>
                    </li>

                    <li class="list-group-item d-flex align-items-center gap-3 py-3">
                        <span class="avatar-xs bg-purple-subtle text-purple rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="ti ti-passport fs-16"></i>
                        </span>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">جواز السفر</small>
                            @if($marketer->passport)
                                <span class="fw-semibold text-success"><i class="ti ti-circle-check me-1"></i>مرفق</span>
                            @else
                                <span class="text-muted">غير مرفق</span>
                            @endif
                        </div>
                        @if($marketer->passport)
                            <a href="{{ Storage::url($marketer->passport) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-eye me-1"></i>عرض
                            </a>
                        @endif
                    </li>

                    <li class="list-group-item d-flex align-items-center gap-3 py-3">
                        <span class="avatar-xs bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="ti ti-calendar-plus fs-16"></i>
                        </span>
                        <div>
                            <small class="text-muted d-block">تاريخ الإضافة</small>
                            <span class="fw-semibold">{{ dt($marketer->created_at) }}</span>
                        </div>
                    </li>

                    <li class="list-group-item d-flex align-items-center gap-3 py-3">
                        <span class="avatar-xs bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="ti ti-calendar-event fs-16"></i>
                        </span>
                        <div>
                            <small class="text-muted d-block">آخر تعديل</small>
                            <span class="fw-semibold">{{ dt($marketer->updated_at) }}</span>
                        </div>
                    </li>

                </ul>
            </div>

            {{-- Tab: Balance --}}
            <div class="tab-pane fade" id="tab-balance" role="tabpanel">

                {{-- Balance Header --}}
                <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom flex-wrap gap-2">
                    <div>
                        <div class="text-muted small fw-semibold mb-1">الرصيد الحالي</div>
                        <div class="fw-bold" style="font-size:1.7rem;color:#4a2619;line-height:1">
                            {{ number_format($marketer->balance, 2) }}
                            <span class="fs-6 text-muted fw-normal">د.ل</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#depositMarketerModal">
                            <i class="ti ti-arrow-bar-to-down me-1"></i> إيداع
                        </button>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#withdrawalMarketerModal">
                            <i class="ti ti-arrow-bar-up me-1"></i> سحب
                        </button>
                    </div>
                </div>

                {{-- Transactions List --}}
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
                                    <th>اسم المستلم</th>
                                    <th>الوصف</th>
                                    <th class="text-center">القيمة</th>
                                    <th class="text-center">الرصيد بعد</th>
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
                                    <td class="fw-semibold">{{ $tx->recipient_name }}</td>
                                    <td class="text-muted">{{ $tx->description }}</td>
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
                    <div class="d-md-none d-flex flex-column gap-3 p-3">
                        @foreach($transactions as $tx)
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div>
                                        @if($tx->type === 'deposit')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle mb-1">
                                                <i class="ti ti-arrow-bar-to-down me-1"></i>إيداع
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle mb-1">
                                                <i class="ti ti-arrow-bar-up me-1"></i>سحب
                                            </span>
                                        @endif
                                        <div class="fw-bold" style="font-size:.95rem">{{ $tx->recipient_name }}</div>
                                        <div class="text-muted small">{{ $tx->description }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold {{ $tx->type === 'deposit' ? 'text-success' : 'text-danger' }}" style="font-size:1rem">
                                            {{ $tx->type === 'deposit' ? '+' : '-' }}{{ number_format($tx->amount, 2) }} د.ل
                                        </div>
                                        <div class="text-muted small">{{ $tx->date->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="text-muted small">الرصيد بعد الحركة</span>
                                    <span class="fw-bold" style="color:#4a2619;font-size:.95rem">{{ number_format($tx->balance_after, 2) }} د.ل</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="px-3 pb-3">{{ $transactions->links() }}</div>
                @endif

            </div>

            {{-- Tab: Orders --}}
            <div class="tab-pane fade" id="tab-orders" role="tabpanel">
                @php $orders = $marketer->orders()->latest()->take(10)->get(); @endphp
                @if($orders->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-clipboard-list fs-1 d-block mb-2"></i>
                        لا توجد طلبات بعد
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الزبون</th>
                                    <th>المدينة</th>
                                    <th class="text-center">الإجمالي</th>
                                    <th class="text-center">الحالة</th>
                                    <th class="text-center">التاريخ</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td class="text-muted small">{{ $order->id }}</td>
                                    <td class="fw-semibold">{{ $order->customer_name }}</td>
                                    <td class="small text-muted">{{ $order->city_name }}</td>
                                    <td class="text-center fw-semibold" style="color:#4a2619">{{ number_format($order->grand_total) }} د.ل</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $order->status_color }}-subtle text-{{ $order->status_color }} border border-{{ $order->status_color }}-subtle">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-center text-muted small">{{ dt($order->created_at, false) }}</td>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Tab: Logs --}}
            <div class="tab-pane fade" id="tab-logs" role="tabpanel">
                @php
                    $logs = \App\Models\SystemLog::where('description', 'like', "%{$marketer->first_name} {$marketer->last_name}%")
                                ->latest()->take(20)->get();
                @endphp
                @if($logs->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-list-search fs-1 d-block mb-2"></i>
                        لا توجد عمليات مسجلة بعد
                    </div>
                @else
                    @foreach($logs as $log)
                        <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom">
                            <span class="avatar-xs bg-light text-muted rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                                <i class="ti ti-point fs-16"></i>
                            </span>
                            <div class="flex-grow-1">
                                <p class="mb-0 fw-semibold fs-14">{{ $log->description }}</p>
                                <small class="text-muted">
                                    بواسطة: <span class="fw-semibold">{{ optional($log->loggable)->name ?? '—' }}</span>
                                    · {{ $log->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <small class="text-muted text-nowrap">{{ dt($log->created_at, false) }}</small>
                        </div>
                    @endforeach
                @endif
            </div>

        </div>
    </div>
</div>

{{-- Deposit Modal --}}
<div class="modal fade" id="depositMarketerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('marketers.transactions.store', $marketer) }}">
                @csrf
                <input type="hidden" name="type" value="deposit">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-success">
                        <i class="ti ti-arrow-bar-to-down me-1"></i> إيداع — {{ $marketer->first_name }} {{ $marketer->last_name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold">اسم المستلم <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_name" class="form-control" required maxlength="100"
                                   value="{{ $marketer->first_name . ' ' . $marketer->last_name }}" placeholder="الاسم الكامل">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold">التاريخ <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" required
                                   value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">الوصف <span class="text-danger">*</span></label>
                            <input type="text" name="description" class="form-control" required maxlength="500"
                                   placeholder="وصف الحركة المالية">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">القيمة <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="amount" class="form-control" required min="0.01" step="0.01" placeholder="0.00">
                                <span class="input-group-text fw-semibold">د.ل</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-check me-1"></i> تأكيد الإيداع
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Withdrawal Modal --}}
<div class="modal fade" id="withdrawalMarketerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('marketers.transactions.store', $marketer) }}">
                @csrf
                <input type="hidden" name="type" value="withdrawal">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger">
                        <i class="ti ti-arrow-bar-up me-1"></i> سحب — {{ $marketer->first_name }} {{ $marketer->last_name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold">اسم المستلم <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_name" class="form-control" required maxlength="100"
                                   value="{{ $marketer->first_name . ' ' . $marketer->last_name }}" placeholder="الاسم الكامل">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold">التاريخ <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" required
                                   value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">الوصف <span class="text-danger">*</span></label>
                            <input type="text" name="description" class="form-control" required maxlength="500"
                                   placeholder="وصف الحركة المالية">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">القيمة <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="amount" class="form-control" required min="0.01" step="0.01" placeholder="0.00">
                                <span class="input-group-text fw-semibold">د.ل</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-check me-1"></i> تأكيد السحب
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
