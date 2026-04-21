<?php

namespace App\Services;

use App\Models\Vault;
use App\Models\VaultTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VaultService
{
    public function list(): LengthAwarePaginator
    {
        return Vault::latest()->paginate(20);
    }

    public function create(array $data): Vault
    {
        $opening = (float) ($data['opening_balance'] ?? 0);

        return Vault::create([
            'name'            => $data['name'],
            'opening_balance' => $opening,
            'current_balance' => $opening,
        ]);
    }

    public function update(Vault $vault, array $data): void
    {
        $vault->update(['name' => $data['name']]);
    }

    public function addTransaction(Vault $vault, array $data): VaultTransaction
    {
        return DB::transaction(function () use ($vault, $data) {
            $amount  = (float) $data['amount'];
            $balance = (float) $vault->current_balance;

            $balanceAfter = $data['type'] === 'deposit'
                ? $balance + $amount
                : $balance - $amount;

            $vault->update(['current_balance' => $balanceAfter]);

            return $vault->transactions()->create([
                'user_id'        => Auth::guard('web')->id(),
                'type'           => $data['type'],
                'recipient_name' => $data['recipient_name'],
                'description'    => $data['description'],
                'amount'         => $amount,
                'date'           => $data['date'],
                'balance_after'  => $balanceAfter,
            ]);
        });
    }

    public function statement(Vault $vault): LengthAwarePaginator
    {
        return $vault->transactions()->latest('date')->latest('id')->paginate(30);
    }

}
