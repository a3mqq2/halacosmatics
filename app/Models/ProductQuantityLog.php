<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['product_id', 'user_id', 'type', 'quantity', 'quantity_after', 'notes'])]
class ProductQuantityLog extends Model
{
    protected function casts(): array
    {
        return [
            'quantity'       => 'integer',
            'quantity_after' => 'integer',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
