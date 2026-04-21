<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductObserver
{
    public function created(Product $product): void
    {
        if (!Auth::check()) return;

        Auth::user()->logs()->create([
            'description' => "أضاف منتجاً جديداً: {$product->name}",
        ]);
    }

    public function updated(Product $product): void
    {
        if (!Auth::check()) return;

        $changes = collect($product->getChanges())->except(['updated_at']);

        if ($changes->has('is_active') && $changes->count() === 1) {
            $status = $product->is_active ? 'فعّل' : 'أوقف';
            Auth::user()->logs()->create([
                'description' => "{$status} المنتج: {$product->name}",
            ]);
            return;
        }

        $labels = [
            'name'        => 'الاسم',
            'code'        => 'الكود',
            'price'       => 'السعر',
            'quantity'    => 'الكمية',
            'description' => 'الوصف',
            'is_active'   => 'الحالة',
        ];

        $changed = $changes->keys()
            ->map(fn($key) => $labels[$key] ?? $key)
            ->implode(', ');

        Auth::user()->logs()->create([
            'description' => "عدّل بيانات المنتج: {$product->name} | الحقول: ({$changed})",
        ]);
    }

    public function deleted(Product $product): void
    {
        if (!Auth::check()) return;

        Auth::user()->logs()->create([
            'description' => "حذف المنتج: {$product->name}",
        ]);
    }
}
