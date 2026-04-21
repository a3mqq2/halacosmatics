<?php

namespace App\Http\Controllers;

use App\DTOs\AdjustQuantityDTO;
use App\DTOs\CreateProductDTO;
use App\DTOs\UpdateProductDTO;
use App\Http\Requests\AddQuantityRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\SubtractQuantityRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index()
    {
        $products = $this->productService->list();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(StoreProductRequest $request)
    {
        $this->productService->create(CreateProductDTO::fromRequest($request));

        return redirect()->route('products.index')
            ->with('success', 'تم إضافة المنتج بنجاح.');
    }

    public function show(Product $product)
    {
        $quantityLogs = $product->quantityLogs()->with('user')->latest()->limit(10)->get();

        return view('products.show', compact('product', 'quantityLogs'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->productService->update($product, UpdateProductDTO::fromRequest($request));

        return redirect()->route('products.index')
            ->with('success', 'تم تحديث المنتج بنجاح.');
    }

    public function addQuantity(AddQuantityRequest $request, Product $product)
    {
        $this->productService->addQuantity($product, AdjustQuantityDTO::fromRequest($request));

        return back()->with('success', "تم إضافة {$request->quantity} وحدة للمنتج: {$product->name}");
    }

    public function subtractQuantity(SubtractQuantityRequest $request, Product $product)
    {
        $this->productService->subtractQuantity($product, AdjustQuantityDTO::fromRequest($request));

        return back()->with('success', "تم خصم {$request->quantity} وحدة من المنتج: {$product->name}");
    }

    public function toggle(Product $product)
    {
        $this->productService->toggle($product);

        $status = $product->is_active ? 'تم تفعيل' : 'تم إيقاف';

        return back()->with('success', "{$status} المنتج: {$product->name}");
    }

    public function destroy(Product $product)
    {
        $this->productService->delete($product);

        return redirect()->route('products.index')
            ->with('success', 'تم حذف المنتج بنجاح.');
    }
}
