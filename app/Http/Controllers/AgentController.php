<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\StoreAgentTransactionRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\Agent;
use App\Models\Vault;
use App\Services\AgentService;

class AgentController extends Controller
{
    public function __construct(private AgentService $agentService) {}

    public function index()
    {
        $agents = $this->agentService->list();

        return view('agents.index', compact('agents'));
    }

    public function create()
    {
        return view('agents.create');
    }

    public function store(StoreAgentRequest $request)
    {
        $this->agentService->create($request->validated());

        return redirect()->route('agents.index')->with('success', 'تم إضافة المندوب بنجاح.');
    }

    public function show(Agent $agent)
    {
        $agent->loadCount('orders');
        $orders = $agent->orders()
            ->with('marketer:id,first_name,last_name')
            ->select('id', 'agent_id', 'marketer_id', 'customer_name', 'status', 'grand_total', 'created_at')
            ->latest()
            ->paginate(15);

        $transactions = $agent->transactions()->with('vault:id,name')->latest('date')->latest('id')->paginate(20);
        $vaults       = Vault::orderBy('name')->get(['id', 'name', 'current_balance']);

        return view('agents.show', compact('agent', 'orders', 'transactions', 'vaults'));
    }

    public function storeTransaction(StoreAgentTransactionRequest $request, Agent $agent)
    {
        $this->agentService->addTransaction($agent, $request->validated());

        return back()->with('success', 'تمت إضافة الحركة المالية بنجاح.');
    }

    public function edit(Agent $agent)
    {
        return view('agents.edit', compact('agent'));
    }

    public function update(UpdateAgentRequest $request, Agent $agent)
    {
        $this->agentService->update($agent, $request->validated());

        return redirect()->route('agents.index')->with('success', 'تم تحديث بيانات المندوب بنجاح.');
    }

    public function destroy(Agent $agent)
    {
        $this->agentService->delete($agent);

        return redirect()->route('agents.index')->with('success', 'تم حذف المندوب.');
    }

    public function toggle(Agent $agent)
    {
        $this->agentService->toggle($agent);

        return back()->with('success', $agent->is_active ? 'تم تعطيل المندوب.' : 'تم تفعيل المندوب.');
    }
}
