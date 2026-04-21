<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::where('is_active', true)->where('quantity', '>', 0);

        if (request('search')) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . request('search') . '%')
                  ->orWhere('code', 'like', '%' . request('search') . '%');
            });
        }

        $sort = request('sort', 'name_asc');
        match ($sort) {
            'name_asc'   => $query->orderBy('name'),
            'name_desc'  => $query->orderByDesc('name'),
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default      => $query->orderBy('name'),
        };

        $products = $query->with(['primaryImage', 'images'])->paginate(12)->withQueryString();

        return view('marketer.products', compact('products'));
    }
}
