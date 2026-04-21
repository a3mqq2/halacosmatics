@extends('layouts.app')

@section('title', 'الخزائن المالية')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">الخزائن المالية</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <a href="{{ route('vaults.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> خزينة جديدة
    </a>
</div>

@if($vaults->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="ti ti-cash-banknote fs-1 d-block mb-2"></i>
        لا توجد خزائن بعد
    </div>
@else

{{-- Desktop --}}
<div class="d-none d-md-block">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>اسم الخزينة</th>
                        <th class="text-center">الرصيد الافتتاحي</th>
                        <th class="text-center">الرصيد الحالي</th>
                        <th class="text-center">تاريخ الإنشاء</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vaults as $vault)
                    <tr>
                        <td class="text-muted small">{{ $vault->id }}</td>
                        <td class="fw-semibold">{{ $vault->name }}</td>
                        <td class="text-center">{{ number_format($vault->opening_balance, 2) }} د.ل</td>
                        <td class="text-center fw-bold" style="color:#4a2619">
                            {{ number_format($vault->current_balance, 2) }} د.ل
                        </td>
                        <td class="text-center text-muted small">{{ dt($vault->created_at) }}</td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('vaults.transactions.index', $vault) }}"
                                   class="btn btn-sm btn-outline-primary" title="كشف الحساب">
                                    <i class="ti ti-file-text"></i>
                                </a>
                                <button class="btn btn-sm btn-success"
                                        onclick="openTxModal('deposit', {{ $vault->id }}, '{{ addslashes($vault->name) }}')"
                                        title="إيداع">
                                    <i class="ti ti-arrow-bar-to-down"></i>
                                </button>
                                <button class="btn btn-sm btn-danger"
                                        onclick="openTxModal('withdrawal', {{ $vault->id }}, '{{ addslashes($vault->name) }}')"
                                        title="سحب">
                                    <i class="ti ti-arrow-bar-up"></i>
                                </button>
                                <a href="{{ route('vaults.edit', $vault) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                    <i class="ti ti-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Mobile --}}
<div class="d-md-none d-flex flex-column gap-3">
    @foreach($vaults as $vault)
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="fw-bold fs-6">{{ $vault->name }}</div>
                <a href="{{ route('vaults.edit', $vault) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ti ti-edit"></i>
                </a>
            </div>
            <div class="d-flex gap-3 mb-3">
                <div class="flex-1">
                    <div class="text-muted" style="font-size:.72rem;font-weight:700">الرصيد الافتتاحي</div>
                    <div class="fw-semibold" style="font-size:.9rem">{{ number_format($vault->opening_balance, 2) }} د.ل</div>
                </div>
                <div class="flex-1">
                    <div class="text-muted" style="font-size:.72rem;font-weight:700">الرصيد الحالي</div>
                    <div class="fw-bold" style="font-size:1rem;color:#4a2619">{{ number_format($vault->current_balance, 2) }} د.ل</div>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('vaults.transactions.index', $vault) }}" class="btn btn-sm btn-outline-primary flex-1">
                    <i class="ti ti-file-text me-1"></i> كشف الحساب
                </a>
                <button class="btn btn-sm btn-success flex-1"
                        onclick="openTxModal('deposit', {{ $vault->id }}, '{{ addslashes($vault->name) }}')">
                    <i class="ti ti-arrow-bar-to-down me-1"></i> إيداع
                </button>
                <button class="btn btn-sm btn-danger flex-1"
                        onclick="openTxModal('withdrawal', {{ $vault->id }}, '{{ addslashes($vault->name) }}')">
                    <i class="ti ti-arrow-bar-up me-1"></i> سحب
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-4">{{ $vaults->links() }}</div>

@endif

{{-- Shared Transaction Modal --}}
<div class="modal fade" id="txModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="txForm" action="">
                @csrf
                <input type="hidden" name="type" id="txType">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="txModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('vaults.partials._transaction_fields')
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn" id="txSubmitBtn">تأكيد</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openTxModal(type, vaultId, vaultName) {
    const isDeposit = type === 'deposit';
    document.getElementById('txType').value  = type;
    document.getElementById('txForm').action = '/vaults/' + vaultId + '/transactions';
    document.getElementById('txModalTitle').innerHTML = isDeposit
        ? '<i class="ti ti-arrow-bar-to-down text-success me-1"></i> إيداع — ' + vaultName
        : '<i class="ti ti-arrow-bar-up text-danger me-1"></i> سحب — ' + vaultName;
    const btn = document.getElementById('txSubmitBtn');
    btn.className = 'btn ' + (isDeposit ? 'btn-success' : 'btn-danger');
    btn.innerHTML = isDeposit
        ? '<i class="ti ti-check me-1"></i> تأكيد الإيداع'
        : '<i class="ti ti-check me-1"></i> تأكيد السحب';
    new bootstrap.Modal(document.getElementById('txModal')).show();
}
</script>
@endpush

@endsection
