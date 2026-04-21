<?php

namespace App\Services;

use App\DTOs\AdjustQuantityDTO;
use App\DTOs\CreateProductDTO;
use App\DTOs\UpdateProductDTO;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductQuantityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductService
{
    public function __construct(private UploadService $uploadService) {}

    public function list(): LengthAwarePaginator
    {
        return QueryBuilder::for(Product::class)
            ->allowedFilters(
                AllowedFilter::partial('name'),
                AllowedFilter::partial('code'),
                AllowedFilter::exact('is_active'),
            )
            ->allowedSorts('name', 'code', 'quantity', 'price', 'created_at', 'is_active')
            ->defaultSort('-created_at')
            ->with('primaryImage')
            ->paginate(20)
            ->withQueryString();
    }

    public function create(CreateProductDTO $dto): Product
    {
        $product = Product::create([
            'name'        => $dto->name,
            'code'        => $dto->code,
            'quantity'    => $dto->quantity,
            'price'       => $dto->price,
            'cost_price'  => $dto->costPrice,
            'description' => $dto->description,
            'is_active'   => $dto->isActive,
        ]);

        foreach ($dto->images as $index => $file) {
            $product->images()->create([
                'path'       => $this->uploadService->store($file, 'products'),
                'is_primary' => $index === $dto->primaryIndex,
                'sort_order' => $index,
            ]);
        }

        if ($dto->quantity > 0) {
            ProductQuantityLog::create([
                'product_id'     => $product->id,
                'user_id'        => Auth::id(),
                'type'           => 'add',
                'quantity'       => $dto->quantity,
                'quantity_after' => $dto->quantity,
                'notes'          => 'كمية مبدئية عند إنشاء المنتج',
            ]);
        }

        return $product;
    }

    public function update(Product $product, UpdateProductDTO $dto): void
    {
        $product->update([
            'name'        => $dto->name,
            'code'        => $dto->code,
            'price'       => $dto->price,
            'cost_price'  => $dto->costPrice,
            'description' => $dto->description,
            'is_active'   => $dto->isActive,
        ]);

        // حذف الصور المحددة للحذف
        if ($dto->deleteImageIds) {
            $toDelete = $product->images()->whereIn('id', $dto->deleteImageIds)->get();
            foreach ($toDelete as $img) {
                Storage::disk('public')->delete($img->path);
                $img->delete();
            }
        }

        // رفع صور جديدة
        $sortStart = $product->images()->max('sort_order') + 1;
        foreach ($dto->images as $index => $file) {
            $product->images()->create([
                'path'       => $this->uploadService->store($file, 'products'),
                'is_primary' => false,
                'sort_order' => $sortStart + $index,
            ]);
        }

        // تعيين الصورة الرئيسية
        if ($dto->primaryExistingId) {
            $product->images()->update(['is_primary' => false]);
            $product->images()->where('id', $dto->primaryExistingId)->update(['is_primary' => true]);
        } elseif ($dto->images) {
            $newPrimary = $product->images()
                ->orderByDesc('sort_order')
                ->skip(count($dto->images) - 1 - $dto->primaryIndex)
                ->first();
            if ($newPrimary) {
                $product->images()->update(['is_primary' => false]);
                $newPrimary->update(['is_primary' => true]);
            }
        } elseif (!$product->images()->where('is_primary', true)->exists()) {
            $product->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        }
    }

    public function addQuantity(Product $product, AdjustQuantityDTO $dto): void
    {
        $quantityAfter = $product->quantity + $dto->quantity;

        $product->update(['quantity' => $quantityAfter]);

        ProductQuantityLog::create([
            'product_id'     => $product->id,
            'user_id'        => Auth::id(),
            'type'           => 'add',
            'quantity'       => $dto->quantity,
            'quantity_after' => $quantityAfter,
            'notes'          => $dto->notes,
        ]);
    }

    public function subtractQuantity(Product $product, AdjustQuantityDTO $dto): void
    {
        $quantityAfter = $product->quantity - $dto->quantity;

        $product->update(['quantity' => $quantityAfter]);

        ProductQuantityLog::create([
            'product_id'     => $product->id,
            'user_id'        => Auth::id(),
            'type'           => 'subtract',
            'quantity'       => $dto->quantity,
            'quantity_after' => $quantityAfter,
            'notes'          => $dto->notes,
        ]);
    }

    public function toggle(Product $product): void
    {
        $product->update(['is_active' => !$product->is_active]);
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}
