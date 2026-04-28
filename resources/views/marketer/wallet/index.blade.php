@extends('layouts.marketer')

@section('title', 'محفظتي')

@push('styles')
<style>
.wallet-hero {
    border-radius: 22px;
    background: linear-gradient(135deg, #1a0c08 0%, #2d1610 50%, #4a2619 100%);
    color: #fff;
    padding: 28px 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 28px rgba(74,38,25,.25);
}
.wallet-hero::before {
    content: '';
    position: absolute;
    top: -80px; left: -80px;
    width: 240px; height: 240px;
    border-radius: 50%;
    background: rgba(200, 80, 180, .14);
}
.wallet-hero::after {
    content: '';
    position: absolute;
    bottom: -60px; right: -40px;
    width: 180px; height: 180px;
    border-radius: 50%;
    background: rgba(255, 200, 100, .08);
}
.wallet-hero-inner { position: relative; z-index: 1; }
.wallet-label {
    font-size: .82rem;
    font-weight: 700;
    color: rgba(255,255,255,.75);
    letter-spacing: .04em;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.wallet-balance {
    font-size: 2.4rem;
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -.01em;
}
.wallet-balance small {
    font-size: 1rem;
    font-weight: 600;
    opacity: .8;
    margin-right: 4px;
}
.wallet-balance.negative { color: #fca5a5; }
.stat-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-top: 18px;
}
.stat-card {
    border-radius: 14px;
    background: #fff;
    border: 1.5px solid #f0ebe8;
    padding: 14px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.stat-card .icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.stat-card .icon.green { background: #dcfce7; color: #15803d; }
.stat-card .icon.red   { background: #fee2e2; color: #b91c1c; }
.stat-card .icon.blue  { background: #dbeafe; color: #1d4ed8; }
.stat-card .icon.brown { background: #fdeee8; color: #4a2619; }
.stat-card .label {
    font-size: .72rem;
    font-weight: 700;
    color: #9ca3af;
    margin-bottom: 2px;
}
.stat-card .value {
    font-size: .95rem;
    font-weight: 800;
    color: #1a1a1a;
    line-height: 1.1;
}
.stat-card .value small {
    font-size: .68rem;
    color: #aaa;
    font-weight: 600;
}
.section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 22px 0 12px;
}
.section-head h6 {
    font-size: .9rem;
    font-weight: 800;
    color: #1a1a1a;
    margin: 0;
}
.section-head a {
    font-size: .8rem;
    font-weight: 700;
    color: #4a2619;
    text-decoration: none;
}
.txn-card {
    border-radius: 14px;
    background: #fff;
    border: 1.5px solid #f0ebe8;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    gap: 12px;
}
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
    font-size: .85rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 2px;
    line-height: 1.25;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.txn-date {
    font-size: .7rem;
    color: #9ca3af;
    font-weight: 600;
}
.txn-amount {
    font-size: .92rem;
    font-weight: 800;
    flex-shrink: 0;
    text-align: left;
    direction: ltr;
}
.txn-amount.deposit    { color: #15803d; }
.txn-amount.withdrawal { color: #b91c1c; }
.empty-state {
    text-align: center;
    padding: 32px 16px;
    color: #9ca3af;
}
.empty-state i { font-size: 2.4rem; opacity: .35; display: block; margin-bottom: 8px; }
</style>
@endpush

@section('content')

<h2 class="page-title">محفظتي</h2>

{{-- Hero Balance --}}
<div class="wallet-hero">
    <div class="wallet-hero-inner">
        <div class="wallet-label">
            <i class="ti ti-wallet"></i> الرصيد الحالي
        </div>
        <div class="wallet-balance {{ $stats['balance'] < 0 ? 'negative' : '' }}">
            {{ number_format($stats['balance'], 2) }}
            <small>د.ل</small>
        </div>ث
    </div>
</div>

{{-- Stats Grid --}}
<div class="stat-grid">

    <div class="stat-card">
        <div class="icon green"><i class="ti ti-arrow-down-right"></i></div>
        <div>
            <div class="label">إجمالي الإيداعات</div>
            <div class="value">{{ number_format($stats['total_deposits'], 2) }} <small>د.ل</small></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="icon red"><i class="ti ti-arrow-up-left"></i></div>
        <div>
            <div class="label">إجمالي السحوبات</div>
            <div class="value">{{ number_format($stats['total_withdrawals'], 2) }} <small>د.ل</small></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="icon brown"><i class="ti ti-calendar-stats"></i></div>
        <div>
            <div class="label">إيرادات هذا الشهر</div>
            <div class="value">{{ number_format($stats['this_month'], 2) }} <small>د.ل</small></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="icon blue"><i class="ti ti-package"></i></div>
        <div>
            <div class="label">طلبات مسلّمة</div>
            <div class="value">{{ $stats['delivered_orders'] }} <small>طلب</small></div>
        </div>
    </div>

</div>

{{-- Recent transactions --}}
<div class="section-head">
    <h6>آخر المعاملات</h6>
    <a href="{{ route('marketer.wallet.transactions') }}">
        كشف الحساب الكامل <i class="ti ti-arrow-left"></i>
    </a>
</div>

@if($recentTransactions->isEmpty())
    <div class="empty-state">
        <i class="ti ti-receipt-off"></i>
        <div class="fw-semibold">لا توجد معاملات بعد</div>
    </div>
@else
    <div class="d-flex flex-column gap-2">
        @foreach($recentTransactions as $t)
        <div class="txn-card">
            <div class="txn-icon {{ $t->type }}">
                <i class="ti ti-{{ $t->type === 'deposit' ? 'arrow-down-right' : 'arrow-up-left' }}"></i>
            </div>
            <div class="txn-body">
                <div class="txn-desc">{{ $t->description }}</div>
                <div class="txn-date">{{ $t->date->format('Y-m-d') }}</div>
            </div>
            <div class="txn-amount {{ $t->type }}">
                {{ $t->type === 'deposit' ? '+' : '−' }} {{ number_format($t->amount, 2) }}
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection
