<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['product_id', 'path', 'is_primary', 'sort_order'])]
class ProductImage extends Model
{
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
