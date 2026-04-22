<?php

namespace App\Http\Controllers;

use App\DTOs\CreateMarketerDTO;
use App\DTOs\UpdateMarketerDTO;
use App\Http\Requests\RegisterMarketerRequest;
use App\Http\Requests\StoreMarketerRequest;
use App\Http\Requests\StoreMarketerTransactionRequest;
use App\Http\Requests\UpdateMarketerRequest;
use App\Models\Marketer;
use App\Services\MarketerService;
use Illuminate\Support\Facades\Auth;

class MarketerController extends Controller
{
    public function __construct(private MarketerService $marketerService) {}

    public function index()
    {
        $marketers = $this->marketerService->list();

        return view('marketers.index', compact('marketers'));
    }

    public function create()
    {
        return view('marketers.create');
    }

    public function register(RegisterMarketerRequest $request)
    {
        $marketer = $this->marketerService->create(CreateMarketerDTO::fromRequest($request, 'approved'));

        Auth::guard('marketer')->login($marketer);

        return redirect()->route('marketer.dashboard');
    }

    public function store(StoreMarketerRequest $request)
    {
        $this->marketerService->create(CreateMarketerDTO::fromRequest($request, 'pending'));

        return redirect()->route('marketers.index')
            ->with('success', 'تم إضافة المسوق بنجاح.');
    }

    public function show(Marketer $marketer)
    {
        $transactions = $this->marketerService->statement($marketer);

        return view('marketers.show', compact('marketer', 'transactions'));
    }

    public function storeTransaction(StoreMarketerTransactionRequest $request, Marketer $marketer)
    {
        $this->marketerService->addTransaction($marketer, $request->validated());

        return back()->with('success', 'تمت إضافة الحركة المالية بنجاح.');
    }

    public function edit(Marketer $marketer)
    {
        return view('marketers.edit', compact('marketer'));
    }

    public function update(UpdateMarketerRequest $request, Marketer $marketer)
    {
        $this->marketerService->update($marketer, UpdateMarketerDTO::fromRequest($request));

        return redirect()->route('marketers.index')
            ->with('success', 'تم تحديث بيانات المسوق بنجاح.');
    }

    public function approve(Marketer $marketer)
    {
        $this->marketerService->approve($marketer);

        return back()->with('success', "تم قبول المسوق: {$marketer->first_name} {$marketer->last_name}");
    }

    public function reject(Marketer $marketer)
    {
        $this->marketerService->reject($marketer);

        return back()->with('success', "تم رفض طلب المسوق: {$marketer->first_name} {$marketer->last_name}");
    }

    public function toggle(Marketer $marketer)
    {
        $this->marketerService->toggle($marketer);

        $status = $marketer->is_active ? 'تم تفعيل' : 'تم إيقاف';

        return back()->with('success', "{$status} المسوق: {$marketer->first_name} {$marketer->last_name}");
    }

    public function destroy(Marketer $marketer)
    {
        $this->marketerService->delete($marketer);

        return redirect()->route('marketers.index')
            ->with('success', 'تم حذف المسوق بنجاح.');
    }
}
