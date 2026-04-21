<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $key = 'login:' . Str::lower($request->username) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'username' => "محاولات كثيرة. حاول بعد {$seconds} ثانية.",
            ]);
        }

        $redirect = $this->authService->login($request);

        if ($redirect) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect()->intended($redirect);
        }

        RateLimiter::hit($key, 60);

        return back()->withErrors([
            'username' => 'بيانات تسجيل الدخول غير صحيحة.',
        ])->onlyInput('username');
    }

    public function logout()
    {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    }
}