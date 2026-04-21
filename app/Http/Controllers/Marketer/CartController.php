<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketer\AddToCartRequest;
use App\Http\Requests\Marketer\UpdateCartRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index()
    {
        $marketer = Auth::guard('marketer')->user();
        ['items' => $items, 'total' => $total] = $this->cartService->getCartWithTotal($marketer);

        return view('marketer.cart', compact('items', 'total'));
    }

    public function add(AddToCartRequest $request)
    {
        $marketer = Auth::guard('marketer')->user();
        $product  = Product::findOrFail($request->product_id);

        $this->cartService->add($marketer, $product, $request->quantity, (float) $request->selling_price);

        return response()->json(['success' => true, 'count' => $this->cartService->count($marketer)]);
    }

    public function update(UpdateCartRequest $request, CartItem $cartItem)
    {
        $marketer = Auth::guard('marketer')->user();

        if ($cartItem->marketer_id !== $marketer->id) {
            abort(403);
        }

        $this->cartService->update($cartItem, $request->quantity);

        return response()->json(['success' => true]);
    }

    public function remove(CartItem $cartItem)
    {
        $marketer = Auth::guard('marketer')->user();

        if ($cartItem->marketer_id !== $marketer->id) {
            abort(403);
        }

        $this->cartService->remove($cartItem);

        return response()->json(['success' => true]);
    }

    public function clear()
    {
        $marketer = Auth::guard('marketer')->user();
        $this->cartService->clear($marketer);

        return back()->with('success', 'تم تفريغ السلة.');
    }
}
