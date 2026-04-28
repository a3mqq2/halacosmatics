@extends('layouts.marketer')

@section('title', 'كشف الحساب')

@push('styles')
<style>
.filter-card {
    border-radius: 16px;
    border: 1.5px solid #f0ebe8;
    background: #fdfaf9;
    overflow: hidden;
}
.filter-toggle-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .85rem 1.1rem;
    background: none;
    border: none;
    font-family: inherit;
    font-size: .88rem;
    font-weight: 700;
    color: #4a2619;
    cursor: pointer;
}
.filter-toggle-btn i.arrow { transition: transform .2s; }
.filter-toggle-btn[aria-expanded="true"] i.arrow { transform: rotate(180deg); }
.filter-body {
    padding: 0 1.1rem 1rem;
    display: flex;
    flex-direction: column;
    gap: .75rem;
}
.filter-label {
    font-size: .75rem;
    font-weight: 700;
    color: #9ca3af;
    margin-bottom: .2rem;
}
.filter-input {
    width: 100%;
    border: 1.5px solid #e8e0dc;
    border-radius: 10px;
    padding: .5rem .75rem;
    font-family: inherit;
    font-size: .88rem;
    color: #111;
    background: #fff;
    outline: none;
    transition: border-color .15s;
}
.filter-input:focus { border-color: #4a2619; }
.type-chips {
    display: flex;
    flex-wrap: wrap;
    gap: .4rem;
}
.type-chip {
    padding: .3rem .85rem;
    border-radius: 20px;
    border: 1.5px solid #e8e0dc;
    background: #fff;
    font-size: .75rem;
    font-weight: 700;
    color: #6b7280;
    cursor: pointer;
    text-decoration: none;
    transition: all .15s;
    white-space: nowrap;
}
.type-chip:hover { border-color: #4a2619; color: #4a2619; }
.type-chip.active { background: #4a2619; color: #fff; border-color: #4a2619; }

.txn-list-card {
    border-radius: 16px;
    border: 1.5px solid #f0ebe8;
    background: #fff;
    overflow: hidden;
}
.txn-row {
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid #f5f0ee;
}
.txn-row:last-child { border-bottom: none; }
.txn-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.txn-icon.deposit    { background: #dcfce7; color: #15803d; }
.txn-icon.withdrawal { background: #fee2e2; color: #b91c1c; }
.txn-body { flex: 1; min-width: 0; }
.txn-desc {
    font-size: .88rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 3px;
    line-height: 1.3;
}
.txn-meta {
    display: flex;
    gap: 8px;
    align-items: center;
    font-size: .72rem;
    color: #9ca3af;
    font-weight: 600;
}
.txn-meta .balance {
    color: #6b7280;
}
.txn-side {
    text-align: left;
    flex-shrink: 0;
    direction: ltr;
}
.txn-amount {
    font-size: .98rem;
    font-weight: 800;
    line-height: 1.1;
}
.txn-amount.deposit    { color: #15803d; }
.txn-amount.withdrawal { color: #b91c1c; }
.empty-state {
    text-align: center;
    padding: 48px 16px;
    color: #9ca3af;
}
.empty-state i { font-size: 3rem; opacity: .35; display: block; margin-bottom: 8px; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('marketer.wallet') }}" class="btn btn-sm btn-light rounded-circle" style="width:36px;height:36px;padding:0;display:inline-flex;align-items:center;justify-content:center">
        <i class="ti ti-arrow-right"></i>
    </a>
    <h2 class="page-title mb-0">كشف الحساب</h2>
</div>

@php $hasFilter = array_filter($filters); @endphp

<div class="filter-card mb-4">
    <button class="filter-toggle-btn" type="button"
            data-bs-toggle="collapse" data-bs-target="#filterBody"
            aria-expanded="{{ $hasFilter ? 'true' : 'false' }}">
        <span>
            <i class="ti ti-filter me-1"></i> البحث والفلترة
            @if($hasFilter)
                <span style="font-size:.7rem;background:#4a2619;color:#fff;border-radius:20px;padding:.1rem .45rem;margin-right:.4rem">مفعّل</span>
            @endif
        </span>
        <i class="ti ti-chevron-down arrow"></i>
    </button>

    <div class="collapse {{ $hasFilter ? 'show' : '' }}" id="filterBody">
        <form method="GET" action="{{ route('marketer.wallet.transactions') }}">
            <div class="filter-body">

                {{-- النوع --}}
                <div>
                    <div class="filter-label">النوع</div>
                    <div class="type-chips">
                        <a href="{{ route('marketer.wallet.transactions', array_merge(request()->except(['type','page']))) }}"
                           class="type-chip {{ !($filters['type'] ?? null) ? 'active' : '' }}">الكل</a>
                        <a href="{{ route('marketer.wallet.transactions', array_merge(request()->except(['page']), ['type' => 'deposit'])) }}"
                           class="type-chip {{ ($filters['type'] ?? null) === 'deposit' ? 'active' : '' }}">إيداع</a>
                        <a href="{{ route('marketer.wallet.transactions', array_merge(request()->except(['page']), ['type' => 'withdrawal'])) }}"
                           class="type-chip {{ ($filters['type'] ?? null) === 'withdrawal' ? 'active' : '' }}">سحب</a>
                    </div>
                </div>

                {{-- البحث --}}
                <div>
                    <div class="filter-label">بحث في الوصف</div>
                    <input type="text" name="search" class="filter-input"
                           placeholder="ابحث..."
                           value="{{ $filters['search'] ?? '' }}">
                </div>

                {{-- التواريخ --}}
                <div class="d-flex gap-2">
                    <div style="flex:1">
                        <div class="filter-label">من تاريخ</div>
                        <input type="date" name="date_from" class="filter-input"
                               value="{{ $filters['date_from'] ?? '' }}">
                    </div>
                    <div style="flex:1">
                        <div class="filter-label">إلى تاريخ</div>
                        <input type="date" name="date_to" class="filter-input"
                               value="{{ $filters['date_to'] ?? '' }}">
                    </div>
                </div>

                <div class="d-flex gap-2 pt-1">
                    <button type="submit" class="btn btn-primary btn-sm" style="flex:1">
                        <i class="ti ti-search me-1"></i> بحث
                    </button>
                    <a href="{{ route('marketer.wallet.transactions') }}" class="btn btn-outline-secondary btn-sm" style="flex:1">
                        <i class="ti ti-x me-1"></i> مسح
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

@if($transactions->isEmpty())
    <div class="empty-state">
        <i class="ti ti-receipt-off"></i>
        <div class="fw-semibold">لا توجد معاملات</div>
    </div>
@else

    <div class="txn-list-card mb-4">
        @foreach($transactions as $t)
        <div class="txn-row">
            <div class="txn-icon {{ $t->type }}">
                <i class="ti ti-{{ $t->type === 'deposit' ? 'arrow-down-right' : 'arrow-up-left' }}"></i>
            </div>
            <div class="txn-body">
                <div class="txn-desc">{{ $t->description }}</div>
                <div class="txn-meta">
                    <span><i class="ti ti-calendar"></i> {{ $t->date->format('Y-m-d') }}</span>
                    <span class="balance">الرصيد: {{ number_format($t->balance_after, 2) }} د.ل</span>
                </div>
            </div>
            <div class="txn-side">
                <div class="txn-amount {{ $t->type }}">
                    {{ $t->type === 'deposit' ? '+' : '−' }} {{ number_format($t->amount, 2) }}
                </div>
                <div style="font-size:.7rem;color:#aaa;font-weight:600">د.ل</div>
            </div>
        </div>
        @endforeach
    </div>

    {{ $transactions->links() }}

@endif

@endsection
