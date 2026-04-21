<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-product-card">

            <div class="modal-product-header">
                <h6 class="modal-product-title" id="modalProductName"></h6>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <div class="modal-body p-0">

                {{-- Image --}}
                <div id="modalProductImgWrap"></div>

                <div class="modal-product-body">

                    {{-- Meta row --}}
                    <div class="product-meta-row">
                        <span id="modalProductCode"></span>
                        <span class="badge bg-success-subtle text-success px-3 py-1" id="modalProductQty"></span>
                    </div>

                    {{-- Description --}}
                    <p class="product-desc-text" id="modalProductDesc" style="display:none"></p>

                    {{-- Price Section --}}
                    <div class="price-section">

                        {{-- Cost row (read-only reference) --}}
                        <div class="price-row-cost">
                            <div class="price-row-label">
                                <i class="ti ti-tag"></i>
                                <span>سعر التكلفة</span>
                            </div>
                            <span class="price-row-value cost-value" id="modalProductPrice"></span>
                        </div>

                        {{-- Selling price input --}}
                        <div class="price-row-selling">
                            <label class="price-row-label" for="sellingPriceInput">
                                <i class="ti ti-currency-dollar"></i>
                                <span>سعر البيع <span class="text-danger">*</span></span>
                            </label>
                            <div class="selling-input-wrap">
                                <input type="number" id="sellingPriceInput" class="selling-input"
                                       min="0" step="0.01" placeholder="0">
                                <span class="selling-input-unit">د.ل</span>
                            </div>
                        </div>

                    </div>

                    {{-- Quantity + Totals --}}
                    <div class="qty-block">
                        <div class="qty-block-header">
                            <span class="qty-block-label">الكمية</span>
                            <span class="qty-max-note" id="qtyMaxNote"></span>
                        </div>

                        <div class="qty-block-row">
                            <div class="qty-selector">
                                <button type="button" class="qty-btn" id="qtyMinus">
                                    <i class="ti ti-minus"></i>
                                </button>
                                <input type="number" id="qtyInput" class="qty-input" value="1" min="1" readonly>
                                <button type="button" class="qty-btn" id="qtyPlus">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>

                            <div class="totals-stack">
                                <div class="total-box total-box-sell">
                                    <span class="total-box-label">إجمالي البيع</span>
                                    <span class="total-box-value" id="qtyTotal">—</span>
                                </div>
                                <div class="total-box total-box-cost">
                                    <span class="total-box-label">إجمالي التكلفة</span>
                                    <span class="total-box-value" id="qtyCostTotal">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-product-footer">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn-add-cart" id="addToCartBtn">
                    <i class="ti ti-shopping-cart me-1"></i> أضف إلى السلة
                </button>
            </div>

        </div>
    </div>
</div>
