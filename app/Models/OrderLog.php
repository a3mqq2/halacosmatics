<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLog extends Model
{
    protected $fillable = ['order_id', 'action', 'description'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
