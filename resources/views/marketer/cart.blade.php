@extends('layouts.marketer')

@section('title', 'السلة')

@push('styles')
<link href="{{ asset('assets/css/marketer/cart.css') }}" rel="stylesheet">
@endpush

@section('content')

    <div class="page-header">
        <h4><i class="ti ti-shopping-cart"></i> سلة المشتريات</h4>
    </div>

    @if($items->isEmpty())
        <div class="empty-cart">
            <i class="ti ti-shopping-cart-off"></i>
            <p class="fw-bold">سلتك فارغة</p>
            <a href="{{ route('marketer.products') }}" class="btn-browse">تصفح المنتجات</a>
        </div>
    @else

        {{-- Items --}}
        <div id="cartItemsList">
            @foreach($items as $item)
            <div class="cart-item" id="cartItem{{ $item->id }}">
                @if($item->product->primaryImage)
                    <img src="{{ $item->product->primaryImage->url }}" alt="{{ $item->product->name }}" class="cart-item-img">
                @else
                    <div class="cart-item-img-placeholder"><i class="ti ti-photo"></i></div>
                @endif

                <div class="cart-item-info">
                    <div class="cart-item-name">{{ $item->product->name }}</div>
                    @if($item->product->code)
                        <span class="cart-item-code">{{ $item->product->code }}</span>
                    @endif
                    <div class="cart-item-price">{{ number_format($item->selling_price) }} د.ل / وحدة</div>
                </div>

                <div class="cart-item-actions">
                    <div class="cart-qty-selector">
                        <button type="button" class="cart-qty-btn" onclick="changeQty({{ $item->id }}, -1, {{ $item->product->quantity }})">
                            <i class="ti ti-minus"></i>
                        </button>
                        <span class="cart-qty-val" id="qty{{ $item->id }}">{{ $item->quantity }}</span>
                        <button type="button" class="cart-qty-btn" onclick="changeQty({{ $item->id }}, 1, {{ $item->product->quantity }})">
                            <i class="ti ti-plus"></i>
                        </button>
                    </div>
                    <div class="cart-item-total" id="total{{ $item->id }}">
                        {{ number_format($item->quantity * $item->selling_price) }} د.ل
                    </div>
                    <button type="button" class="cart-remove-btn" onclick="removeItem({{ $item->id }})">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Summary --}}
        <div class="cart-summary">
            <div class="cart-summary-row">
                <span>عدد المنتجات</span>
                <span id="summaryCount">{{ $items->count() }} صنف</span>
            </div>
            <div class="cart-summary-row">
                <span>إجمالي الوحدات</span>
                <span id="summaryUnits">{{ $items->sum('quantity') }} وحدة</span>
            </div>
            <div class="cart-summary-total">
                <span>الإجمالي الكلي</span>
                <span id="summaryTotal">{{ number_format($total) }} د.ل</span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="cart-footer">
            <form method="POST" action="{{ route('marketer.cart.clear') }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn-clear-cart" onclick="return confirm('هل تريدين تفريغ السلة؟')">
                    <i class="ti ti-trash me-1"></i> تفريغ السلة
                </button>
            </form>
            <a href="{{ route('marketer.checkout') }}" class="btn-order">
                <i class="ti ti-check me-1"></i> إتمام الطلب
            </a>
        </div>

    @endif

@endsection

@push('scripts')
<script>
    const cartRoutes = {
        update: (id) => `/marketer/cart/${id}`,
        remove: (id) => `/marketer/cart/${id}`,
    };
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const itemPrices = {
        @foreach($items as $item)
        {{ $item->id }}: {{ $item->selling_price }},
        @endforeach
    };

    async function changeQty(id, delta, max) {
        const qtyEl  = document.getElementById('qty' + id);
        const newQty = Math.max(0, Math.min(max, parseInt(qtyEl.textContent) + delta));

        if (newQty === 0) { removeItem(id); return; }

        const res = await fetch(cartRoutes.update(id), {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ quantity: newQty }),
        });

        if ((await res.json()).success) {
            qtyEl.textContent = newQty;
            document.getElementById('total' + id).textContent =
                (newQty * itemPrices[id]).toLocaleString('ar') + ' د.ل';
            recalcSummary();
        }
    }

    async function removeItem(id) {
        const res = await fetch(cartRoutes.remove(id), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        });

        if ((await res.json()).success) {
            document.getElementById('cartItem' + id)?.remove();
            delete itemPrices[id];
            recalcSummary();

            if (Object.keys(itemPrices).length === 0) location.reload();
        }
    }

    function recalcSummary() {
        let totalVal  = 0;
        let unitCount = 0;
        let itemCount = 0;

        document.querySelectorAll('.cart-item').forEach(el => {
            const id  = el.id.replace('cartItem', '');
            const qty = parseInt(document.getElementById('qty' + id)?.textContent || 0);
            totalVal  += qty * (itemPrices[id] || 0);
            unitCount += qty;
            itemCount++;
        });

        document.getElementById('summaryTotal').textContent = totalVal.toLocaleString('ar') + ' د.ل';
        document.getElementById('summaryUnits').textContent = unitCount + ' وحدة';
        document.getElementById('summaryCount').textContent = itemCount + ' صنف';
    }
</script>
@endpush
