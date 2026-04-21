<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'marketer_id',
        'customer_name',
        'customer_phone',
        'customer_phone2',
        'address',
        'notes',
        'city_id',
        'city_name',
        'sub_city_id',
        'sub_city_name',
        'delivery_cost',
        'products_total',
        'grand_total',
        'status',
        'agent_id',
        'mosafir_parcel_id',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejected_reason',
        'delivery_failure_reason',
        'has_deposit',
        'deposit_amount',
        'deposit_payer',
        'deposit_proof',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'has_deposit' => 'boolean',
        ];
    }

    public function marketer(): BelongsTo
    {
        return $this->belongsTo(Marketer::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(OrderLog::class)->latest();
    }

    public function getCollectionAmountAttribute(): float
    {
        $deposit = ($this->has_deposit && $this->deposit_amount) ? (float) $this->deposit_amount : 0.0;
        return max(0.0, (float) $this->grand_total - $deposit);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'قيد الموافقة',
            'processing' => 'قيد التجهيز',
            'with_agent' => 'بحوزة المندوب',
            'delivered'  => 'تم التسليم',
            'returning'  => 'قيد الاسترداد',
            'returned'   => 'مسترد',
            'rejected'   => 'مرفوض',
            default      => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'warning',
            'processing' => 'primary',
            'with_agent' => 'info',
            'delivered'  => 'success',
            'returning'  => 'orange',
            'returned'   => 'secondary',
            'rejected'   => 'danger',
            default      => 'secondary',
        };
    }
}
