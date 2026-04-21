<form method="GET" action="{{ route('marketer.products') }}">
<div class="search-wrap">

    {{-- Search Input --}}
    <div class="search-input-wrap">
        <i class="ti ti-search search-icon"></i>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="ابحث باسم المنتج أو الكود...">
    </div>

    {{-- Sort Pills --}}
    <div class="sort-row">
        <span class="sort-label"><i class="ti ti-arrows-sort me-1"></i>ترتيب:</span>
        <div class="sort-pills">
            @php $sort = request('sort', 'name_asc'); @endphp
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name_asc']) }}"
               class="sort-pill {{ $sort === 'name_asc' ? 'active' : '' }}">
                <i class="ti ti-sort-ascending-letters"></i> الاسم أ-ي
            </a>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name_desc']) }}"
               class="sort-pill {{ $sort === 'name_desc' ? 'active' : '' }}">
                <i class="ti ti-sort-descending-letters"></i> الاسم ي-أ
            </a>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}"
               class="sort-pill {{ $sort === 'price_asc' ? 'active' : '' }}">
                <i class="ti ti-arrow-up"></i> الأقل سعراً
            </a>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}"
               class="sort-pill {{ $sort === 'price_desc' ? 'active' : '' }}">
                <i class="ti ti-arrow-down"></i> الأعلى سعراً
            </a>
            @if(request('search') || request('sort'))
                <a href="{{ route('marketer.products') }}" class="clear-btn">
                    <i class="ti ti-x"></i> مسح
                </a>
            @endif
        </div>
    </div>

    {{-- Search Button (hidden, triggered by Enter) --}}
    <button type="submit" style="display:none"></button>

</div>
</form>
