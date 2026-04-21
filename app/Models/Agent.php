<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'phone2',
        'is_active',
        'balance',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'balance'   => 'decimal:2',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(AgentTransaction::class);
    }
}
