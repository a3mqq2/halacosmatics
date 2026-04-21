<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['marketer_id', 'product_id', 'quantity', 'selling_price'])]
class CartItem extends Model
{
    protected function casts(): array
    {
        return [
            'quantity'      => 'integer',
            'selling_price' => 'decimal:2',
        ];
    }

    public function marketer(): BelongsTo
    {
        return $this->belongsTo(Marketer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalAttribute(): float
    {
        return $this->quantity * (float) $this->selling_price;
    }
}
