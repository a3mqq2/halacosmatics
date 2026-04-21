<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Marketer;
use App\Models\Product;

class CartService
{
    public function add(Marketer $marketer, Product $product, int $quantity, float $sellingPrice): void
    {
        $max  = $product->quantity;
        $item = $marketer->cartItems()->where('product_id', $product->id)->first();

        if ($item) {
            $newQty = min($item->quantity + $quantity, $max);
            $item->update(['quantity' => $newQty, 'selling_price' => $sellingPrice]);
        } else {
            $marketer->cartItems()->create([
                'product_id'    => $product->id,
                'quantity'      => min($quantity, $max),
                'selling_price' => $sellingPrice,
            ]);
        }
    }

    public function update(CartItem $item, int $quantity): void
    {
        $max = $item->product->quantity;

        if ($quantity <= 0) {
            $item->delete();
            return;
        }

        $item->update(['quantity' => min($quantity, $max)]);
    }

    public function remove(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(Marketer $marketer): void
    {
        $marketer->cartItems()->delete();
    }

    public function getCartWithTotal(Marketer $marketer): array
    {
        $items = $marketer->cartItems()
            ->with(['product.primaryImage'])
            ->get();

        $total = $items->sum(fn($item) => $item->quantity * (float) $item->selling_price);

        return compact('items', 'total');
    }

    public function count(Marketer $marketer): int
    {
        return $marketer->cartItems()->sum('quantity');
    }
}
