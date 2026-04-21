<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Observers\UserObserver;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[ObservedBy(UserObserver::class)]
#[Fillable([
    'name', 'username', 'password', 'is_super',
    'perm_users',
    'perm_orders_pending', 'perm_orders_active', 'perm_orders_delivered',
    'perm_orders_returned', 'perm_orders_approve', 'perm_orders_deliver',
    'perm_agents', 'perm_vaults',
    'perm_products_view', 'perm_products_prices', 'perm_products_costs',
    'perm_products_edit', 'perm_products_stock',
    'perm_marketers_view', 'perm_marketers_manage', 'perm_marketers_finance',
    'perm_reports',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'password'                => 'hashed',
            'is_super'                => 'boolean',
            'perm_users'              => 'boolean',
            'perm_orders_pending'     => 'boolean',
            'perm_orders_active'      => 'boolean',
            'perm_orders_delivered'   => 'boolean',
            'perm_orders_returned'    => 'boolean',
            'perm_orders_approve'     => 'boolean',
            'perm_orders_deliver'     => 'boolean',
            'perm_agents'             => 'boolean',
            'perm_vaults'             => 'boolean',
            'perm_products_view'      => 'boolean',
            'perm_products_prices'    => 'boolean',
            'perm_products_costs'     => 'boolean',
            'perm_products_edit'      => 'boolean',
            'perm_products_stock'     => 'boolean',
            'perm_marketers_view'     => 'boolean',
            'perm_marketers_manage'   => 'boolean',
            'perm_marketers_finance'  => 'boolean',
            'perm_reports'            => 'boolean',
        ];
    }

    public function can_access(string $permission): bool
    {
        if ($this->is_super) return true;
        if ($permission === 'settings') return false;
        $column = 'perm_' . str_replace('.', '_', $permission);
        return (bool) ($this->{$column} ?? false);
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(SystemLog::class, 'loggable');
    }
}
