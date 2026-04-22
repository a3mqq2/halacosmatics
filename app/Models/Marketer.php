<?php

namespace App\Models;

use App\Observers\MarketerObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[ObservedBy(MarketerObserver::class)]
#[Fillable(['first_name', 'last_name', 'phone', 'backup_phone', 'email', 'password', 'passport', 'is_active', 'status', 'balance'])]
#[Hidden(['password', 'remember_token'])]
class Marketer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'is_active' => 'boolean',
            'balance'   => 'decimal:2',
        ];
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(SystemLog::class, 'loggable');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(MarketerTransaction::class);
    }

    public function scopeSearch($query, string $value)
    {
        return $query->where(function ($q) use ($value) {
            $q->where('first_name', 'like', "%{$value}%")
              ->orWhere('last_name', 'like', "%{$value}%")
              ->orWhere('phone', 'like', "%{$value}%")
              ->orWhere('email', 'like', "%{$value}%");
        });
    }
}
