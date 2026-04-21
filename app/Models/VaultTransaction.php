<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaultTransaction extends Model
{
    protected $fillable = [
        'vault_id',
        'user_id',
        'type',
        'recipient_name',
        'description',
        'amount',
        'date',
        'balance_after',
    ];

    protected function casts(): array
    {
        return [
            'amount'        => 'decimal:2',
            'balance_after' => 'decimal:2',
            'date'          => 'date',
        ];
    }

    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'deposit'    => 'إيداع',
            'withdrawal' => 'سحب',
            default      => $this->type,
        };
    }
}
