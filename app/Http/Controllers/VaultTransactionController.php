<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVaultTransactionRequest;
use App\Models\Vault;
use App\Services\VaultService;

class VaultTransactionController extends Controller
{
    public function __construct(private VaultService $service) {}

    public function index(Vault $vault)
    {
        $transactions = $this->service->statement($vault);

        return view('vaults.transactions', compact('vault', 'transactions'));
    }

    public function store(StoreVaultTransactionRequest $request, Vault $vault)
    {
        $this->service->addTransaction($vault, $request->validated());

        return back()->with('success', 'تمت إضافة الحركة المالية بنجاح.');
    }
}
