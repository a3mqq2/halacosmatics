<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVaultRequest;
use App\Http\Requests\UpdateVaultRequest;
use App\Models\Vault;
use App\Services\VaultService;

class VaultController extends Controller
{
    public function __construct(private VaultService $service) {}

    public function index()
    {
        $vaults = $this->service->list();

        return view('vaults.index', compact('vaults'));
    }

    public function create()
    {
        return view('vaults.create');
    }

    public function store(StoreVaultRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('vaults.index')->with('success', 'تم إنشاء الخزينة بنجاح.');
    }

    public function edit(Vault $vault)
    {
        return view('vaults.edit', compact('vault'));
    }

    public function update(UpdateVaultRequest $request, Vault $vault)
    {
        $this->service->update($vault, $request->validated());

        return redirect()->route('vaults.index')->with('success', 'تم تحديث الخزينة بنجاح.');
    }

}
