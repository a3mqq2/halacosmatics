<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $marketer = Auth::guard('marketer')->user();

        $stats = [
            'balance'         => (float) $marketer->balance,
            'total_deposits'  => (float) $marketer->transactions()->where('type', 'deposit')->sum('amount'),
            'total_withdrawals' => (float) $marketer->transactions()->where('type', 'withdrawal')->sum('amount'),
            'this_month'      => (float) $marketer->transactions()
                ->where('type', 'deposit')
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('amount'),
            'delivered_orders' => $marketer->orders()->where('status', 'delivered')->count(),
        ];

        $recentTransactions = $marketer->transactions()
            ->latest('date')
            ->latest('id')
            ->limit(5)
            ->get();

        return view('marketer.wallet.index', compact('stats', 'recentTransactions'));
    }

    public function transactions(Request $request)
    {
        $marketer = Auth::guard('marketer')->user();

        $filters = $request->only(['type', 'search', 'date_from', 'date_to']);

        $transactions = $marketer->transactions()
            ->when($filters['type'] ?? null,      fn($q, $v) => $q->where('type', $v))
            ->when($filters['search'] ?? null,    fn($q, $v) => $q->where('description', 'like', "%{$v}%"))
            ->when($filters['date_from'] ?? null, fn($q, $v) => $q->whereDate('date', '>=', $v))
            ->when($filters['date_to'] ?? null,   fn($q, $v) => $q->whereDate('date', '<=', $v))
            ->latest('date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('marketer.wallet.transactions', compact('transactions', 'filters'));
    }
}
