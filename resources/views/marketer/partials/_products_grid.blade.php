@if($products->isEmpty())
    <div class="empty-state">
        <i class="ti ti-box-off"></i>
        <p>لا توجد منتجات متاحة</p>
    </div>
@else
    <div class="results-count">
        <i class="ti ti-list"></i>
        <span>عدد النتائج: <strong>{{ $products->total() }}</strong> منتج</span>
    </div>

    <div class="row g-3 mb-3">
        @foreach($products as $product)
        <div class="col-6">
            <div class="product-card" onclick="openProduct({{ $product->id }})" data-id="{{ $product->id }}">
                @if($product->primaryImage)
                    <img src="{{ $product->primaryImage->url }}"
                         alt="{{ $product->name }}" class="product-img" loading="lazy">
                @else
                    <div class="product-img-placeholder">
                        <i class="ti ti-photo"></i>
                    </div>
                @endif
                <div class="card-body">
                    <div class="product-name">{{ $product->name }}</div>
                    @if($product->code)
                        <span class="product-code">{{ $product->code }}</span>
                    @endif
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <span class="product-price">{{ number_format($product->price) }} <small style="font-size:.7rem;font-weight:600">د.ل</small></span>
                        <span class="product-qty-badge">{{ number_format($product->quantity) }} متوفر</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($products->hasPages())
        <div class="d-flex justify-content-center mt-2 mb-4">
            {{ $products->links() }}
        </div>
    @endif
@endif
