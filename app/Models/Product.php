<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use App\Observers\ProductObserver;

#[ObservedBy(ProductObserver::class)]
#[Fillable(['name', 'price', 'cost_price', 'code', 'quantity', 'image', 'description', 'is_active'])]
class Product extends Model
{
    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'quantity'   => 'integer',
            'price'      => 'integer',
            'cost_price' => 'integer',
        ];
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function quantityLogs()
    {
        return $this->hasMany(ProductQuantityLog::class)->latest();
    }
}
