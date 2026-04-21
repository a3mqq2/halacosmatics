document.addEventListener('DOMContentLoaded', function () {

    const products = window.__marketerProducts      || {};
    const images   = window.__marketerProductImages || {};

    let currentMax          = 1;
    let currentCostPrice    = 0;
    const sellingPriceInput = document.getElementById('sellingPriceInput');

    const qtyInput    = document.getElementById('qtyInput');
    const qtyMinus    = document.getElementById('qtyMinus');
    const qtyPlus     = document.getElementById('qtyPlus');
    const qtyMaxNote  = document.getElementById('qtyMaxNote');
    const qtyTotal    = document.getElementById('qtyTotal');
    const qtyCostTotal = document.getElementById('qtyCostTotal');

    function getSellingPrice() {
        return parseFloat(sellingPriceInput.value) || 0;
    }

    function setQty(val) {
        val = Math.max(1, Math.min(currentMax, val));
        qtyInput.value    = val;
        qtyMinus.disabled = val <= 1;
        qtyPlus.disabled  = val >= currentMax;
        qtyTotal.textContent    = (val * getSellingPrice()).toLocaleString('ar') + ' د.ل';
        qtyCostTotal.textContent = (val * currentCostPrice).toLocaleString('ar') + ' د.ل';
    }

    sellingPriceInput.addEventListener('input', () => setQty(parseInt(qtyInput.value)));

    qtyMinus.addEventListener('click', () => setQty(parseInt(qtyInput.value) - 1));
    qtyPlus.addEventListener('click',  () => setQty(parseInt(qtyInput.value) + 1));

    function buildImagesHtml(urls, productName) {
        if (!urls || urls.length === 0) {
            return `<div class="modal-product-img-placeholder"><i class="ti ti-photo"></i></div>`;
        }
        if (urls.length === 1) {
            return `<img src="${urls[0]}" class="modal-product-img" alt="${productName}">`;
        }

        const uid = 'carousel-' + Date.now();

        const indicators = urls.map((_, i) => `
            <button type="button" data-bs-target="#${uid}" data-bs-slide-to="${i}"
                    class="${i === 0 ? 'active' : ''}" aria-label="صورة ${i + 1}"></button>
        `).join('');

        const slides = urls.map((url, i) => `
            <div class="carousel-item ${i === 0 ? 'active' : ''}">
                <img src="${url}" class="modal-product-img" alt="${productName}">
            </div>
        `).join('');

        return `
            <div id="${uid}" class="carousel slide mb-3" data-bs-ride="false">
                <div class="carousel-indicators">${indicators}</div>
                <div class="carousel-inner" style="border-radius:12px;overflow:hidden">${slides}</div>
                <button class="carousel-control-prev" type="button" data-bs-target="#${uid}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#${uid}" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        `;
    }

    function openProduct(id) {
        const p = products[id];
        if (!p) return;

        currentMax       = parseInt(p.quantity) || 1;
        currentCostPrice = parseFloat(p.price)  || 0;
        currentProductId = id;

        sellingPriceInput.value = currentCostPrice;

        document.getElementById('modalProductName').textContent  = p.name;
        document.getElementById('modalProductPrice').textContent = Number(p.price).toLocaleString('ar') + ' د.ل';
        document.getElementById('modalProductQty').textContent   = 'متوفر: ' + Number(p.quantity).toLocaleString('ar') + ' وحدة';
        document.getElementById('modalProductDesc').textContent  = p.description || '';
        document.getElementById('modalProductDesc').style.display = p.description ? '' : 'none';
        document.getElementById('modalProductCode').innerHTML    = p.code
            ? `<code class="text-muted" style="font-size:.8rem">${p.code}</code>`
            : '';

        document.getElementById('modalProductImgWrap').innerHTML = buildImagesHtml(images[id], p.name);

        qtyMaxNote.textContent = `الحد الأقصى: ${Number(p.quantity).toLocaleString('ar')} وحدة`;
        setQty(1);

        new bootstrap.Modal(document.getElementById('productModal')).show();
    }

    let currentProductId = null;

    document.getElementById('addToCartBtn').addEventListener('click', async function () {
        if (!currentProductId) return;

        const qty          = parseInt(qtyInput.value);
        const sellingPrice = getSellingPrice();
        const csrfMeta     = document.querySelector('meta[name="csrf-token"]');

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الإضافة...';

        try {
            const res  = await fetch('/marketer/cart/add', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfMeta?.content },
                body:    JSON.stringify({ product_id: currentProductId, quantity: qty, selling_price: sellingPrice }),
            });
            const data = await res.json();

            if (data.success) {
                this.innerHTML = '<i class="ti ti-check me-1"></i> تمت الإضافة!';
                this.style.background = '#16a34a';
                updateCartBadge(data.count);
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('productModal'))?.hide();
                    this.disabled = false;
                    this.innerHTML = '<i class="ti ti-shopping-cart me-1"></i> أضف إلى السلة';
                    this.style.background = '';
                }, 1200);
            }
        } catch {
            this.disabled = false;
            this.innerHTML = '<i class="ti ti-shopping-cart me-1"></i> أضف إلى السلة';
        }
    });

    function updateCartBadge(count) {
        const cartLink = document.querySelector('.bottom-nav a[href*="cart"]');
        if (!cartLink) return;
        let badge = cartLink.querySelector('.cart-badge');
        if (count > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'cart-badge';
                badge.style.cssText = 'position:absolute;top:6px;right:calc(50% - 18px);background:#4a2619;color:#fff;font-size:.55rem;font-weight:800;border-radius:50%;width:16px;height:16px;display:flex;align-items:center;justify-content:center;line-height:1';
                cartLink.appendChild(badge);
            }
            badge.textContent = count > 99 ? '99+' : count;
        } else {
            badge?.remove();
        }
    }

    window.openProduct = openProduct;
});
