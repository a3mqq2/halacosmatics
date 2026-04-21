<?php

namespace App\Services;

use App\Models\Marketer;
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

    private function attemptWeb(Request $request): bool
    {
        return Auth::guard('web')->attempt(
            $request->only('username', 'password'),
            $request->boolean('remember')
        );
    }

    private function attemptMarketer(Request $request): bool
    {
        $marketer = Marketer::where('username', $request->username)
            ->select('id', 'username', 'password', 'status', 'is_active')
            ->first();

        if (! $marketer || ! Hash::check($request->password, $marketer->password)) {
            return false;
        }

        if (!$marketer || !$marketer->is_active || $marketer->status !== 'approved') {
            return false;
        }

        Auth::guard('marketer')->login($marketer, $request->boolean('remember'));

        return true;
    }
}