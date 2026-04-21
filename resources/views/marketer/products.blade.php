@extends('layouts.marketer')

@section('title', 'المنتجات')

@push('styles')
<link href="{{ asset('assets/css/marketer/products.css') }}" rel="stylesheet">
@endpush

@section('content')

    <div class="page-header">
        <h4><i class="ti ti-shopping-bag"></i> المنتجات المتاحة</h4>
    </div>

    @include('marketer.partials._products_search')

    @include('marketer.partials._products_grid')

    @include('marketer.partials._products_modal')

@endsection

@push('scripts')
<script>
    window.__marketerProducts = @json($products->keyBy('id'));
    window.__marketerProductImages = @json(
        $products->keyBy('id')->map(fn($p) => $p->images->map(fn($img) => $img->url)->values())
    );
</script>
<script src="{{ asset('assets/js/marketer/products.js') }}"></script>
@endpush
