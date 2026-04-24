<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
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
        $key = 'login:' . preg_replace('/\D/', '', $request->phone) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            Log::channel('single')->warning('[AUTH] تم حجب المحاولة بسبب تجاوز الحد المسموح', [
                'phone_input'       => $request->phone,
                'ip'                => $request->ip(),
                'retry_after_secs'  => $seconds,
            ]);

            throw ValidationException::withMessages([
                'phone' => "محاولات كثيرة. حاول بعد {$seconds} ثانية.",
            ]);
        }

        $redirect = $this->authService->login($request);

        if ($redirect) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            $intended        = $request->session()->pull('url.intended');
            $isMarketerLogin = str_contains($redirect, 'marketer');

            if ($intended) {
                $isMarketerUrl = str_starts_with($intended, url('/marketer'));
                if ($isMarketerLogin === $isMarketerUrl) {
                    return redirect($intended);
                }
            }

            return redirect($redirect);
        }

        RateLimiter::hit($key, 60);

        Log::channel('single')->warning('[AUTH] إرجاع خطأ للمستخدم: بيانات غير صحيحة', [
            'phone_input' => $request->phone,
            'ip'          => $request->ip(),
            'attempts'    => RateLimiter::attempts($key),
        ]);

        return back()->withErrors([
            'phone' => 'بيانات تسجيل الدخول غير صحيحة.',
        ])->onlyInput('phone');
    }

    public function logout()
    {
        $guard = auth('marketer')->check() ? 'marketer' : 'web';
        auth($guard)->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    }
}
