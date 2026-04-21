@extends('layouts.app')

@section('title', 'كشف حساب — ' . $vault->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('vaults.index') }}">الخزائن المالية</a></li>
    <li class="breadcrumb-item active">{{ $vault->name }}</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div class="vault-balance-badge">
        <span class="text-muted small fw-semibold">الرصيد الحالي</span>
        <span class="fw-bold fs-5" style="color:#4a2619">{{ number_format($vault->current_balance, 2) }} د.ل</span>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#depositModal">
            <i class="ti ti-arrow-bar-to-down me-1"></i> إيداع
        </button>
        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#withdrawalModal">
            <i class="ti ti-arrow-bar-up me-1"></i> سحب
        </button>
    </div>
</div>

@if($transactions->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="ti ti-file-text fs-1 d-block mb-2"></i>
        لا توجد حركات مالية بعد
    </div>
@else

{{-- Desktop --}}
<div class="d-none d-md-block">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
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
                                    <i class="ti ti-arrow-bar-to-down me-1"></i> إيداع
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="ti ti-arrow-bar-up me-1"></i> سحب
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
    </div>
</div>

{{-- Mobile --}}
<div class="d-md-none d-flex flex-column gap-3">
    @foreach($transactions as $tx)
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-3">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div>
                    @if($tx->type === 'deposit')
                        <span class="badge bg-success-subtle text-success border border-success-subtle mb-1">
                            <i class="ti ti-arrow-bar-to-down me-1"></i> إيداع
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle mb-1">
                            <i class="ti ti-arrow-bar-up me-1"></i> سحب
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

<div class="mt-4">{{ $transactions->links() }}</div>

@endif

{{-- Deposit Modal --}}
<div class="modal fade" id="depositModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('vaults.transactions.store', $vault) }}">
                @csrf
                <input type="hidden" name="type" value="deposit">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-success">
                        <i class="ti ti-arrow-bar-to-down me-1"></i> إيداع — {{ $vault->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('vaults.partials._transaction_fields')
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
<div class="modal fade" id="withdrawalModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('vaults.transactions.store', $vault) }}">
                @csrf
                <input type="hidden" name="type" value="withdrawal">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger">
                        <i class="ti ti-arrow-bar-up me-1"></i> سحب — {{ $vault->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('vaults.partials._transaction_fields')
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
