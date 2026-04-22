<?php

namespace App\Services;

use App\Models\Marketer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(Request $request): ?string
    {
        if ($this->attemptWeb($request)) {
            return route('dashboard');
        }

        if ($this->attemptMarketer($request)) {
            return route('marketer.dashboard');
        }

        return null;
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        return substr($digits, -9);
    }

    private function attemptWeb(Request $request): bool
    {
        $last9 = $this->normalizePhone($request->phone);

        $user = User::where('phone', 'like', '%' . $last9)
            ->select('id', 'phone', 'password')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return false;
        }

        Auth::guard('web')->login($user, $request->boolean('remember'));

        return true;
    }

    private function attemptMarketer(Request $request): bool
    {
        $last9 = $this->normalizePhone($request->phone);

        $marketer = Marketer::where('phone', 'like', '%' . $last9)
            ->select('id', 'phone', 'password', 'status', 'is_active')
            ->first();

        if (!$marketer || !Hash::check($request->password, $marketer->password)) {
            return false;
        }

        if (!$marketer->is_active || $marketer->status !== 'approved') {
            return false;
        }

        Auth::guard('marketer')->login($marketer, $request->boolean('remember'));

        return true;
    }
}
