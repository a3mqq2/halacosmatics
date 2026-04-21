<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\AgentTransaction;
use App\Models\MarketerTransaction;
use App\Models\Vault;
use App\Models\VaultTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AgentService
{
    public function list()
    {
        return QueryBuilder::for(Agent::class)
            ->allowedFilters(
                AllowedFilter::partial('name'),
                AllowedFilter::partial('phone'),
                AllowedFilter::exact('is_active'),
            )
            ->allowedSorts('name', 'created_at')
            ->defaultSort('-created_at')
            ->paginate(20)
            ->withQueryString();
    }

    public function create(array $data): Agent
    {
        return Agent::create($data);
    }

    public function update(Agent $agent, array $data): void
    {
        $agent->update($data);
    }

    public function delete(Agent $agent): void
    {
        $agent->delete();
    }

    public function toggle(Agent $agent): void
    {
        $agent->update(['is_active' => ! $agent->is_active]);
    }

    public function addTransaction(Agent $agent, array $data): AgentTransaction
    {
        return DB::transaction(function () use ($agent, $data) {
            $amount       = (float) $data['amount'];
            $balanceAfter = $data['type'] === 'deposit'
                ? (float) $agent->balance + $amount
                : (float) $agent->balance - $amount;

            $agent->update(['balance' => $balanceAfter]);

            $tx = AgentTransaction::create([
                'agent_id'     => $agent->id,
                'user_id'      => Auth::guard('web')->id(),
                'vault_id'     => $data['vault_id'] ?? null,
                'type'         => $data['type'],
                'description'  => $data['description'],
                'amount'       => $amount,
                'date'         => $data['date'],
                'balance_after'=> $balanceAfter,
            ]);

            if ($data['type'] === 'withdrawal' && ! empty($data['vault_id'])) {
                $vault        = Vault::findOrFail($data['vault_id']);
                $vaultBalance = (float) $vault->current_balance + $amount;

                $vault->update(['current_balance' => $vaultBalance]);

                VaultTransaction::create([
                    'vault_id'       => $vault->id,
                    'user_id'        => Auth::guard('web')->id(),
                    'type'           => 'deposit',
                    'recipient_name' => $agent->name,
                    'description'    => "تسوية عهدة المندوب: {$agent->name}",
                    'amount'         => $amount,
                    'date'           => $data['date'],
                    'balance_after'  => $vaultBalance,
                ]);
            }

            return $tx;
        });
    }
}
