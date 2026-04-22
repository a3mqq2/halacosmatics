<?php

namespace App\Services;

use App\Models\Marketer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function login(Request $request): ?string
    {
        $phone  = $request->phone;
        $last9  = $this->normalizePhone($phone);
        $ip     = $request->ip();

        Log::channel('single')->info('[AUTH] بدء محاولة تسجيل دخول', [
            'phone_input' => $phone,
            'phone_last9' => $last9,
            'ip'          => $ip,
        ]);

        if ($this->attemptWeb($request, $last9)) {
            Log::channel('single')->info('[AUTH] نجح الدخول كمستخدم إداري', [
                'phone_last9' => $last9,
                'ip'          => $ip,
            ]);
            return route('dashboard');
        }

        if ($this->attemptMarketer($request, $last9)) {
            Log::channel('single')->info('[AUTH] نجح الدخول كمسوق', [
                'phone_last9' => $last9,
                'ip'          => $ip,
            ]);
            return route('marketer.dashboard');
        }

        Log::channel('single')->warning('[AUTH] فشل تسجيل الدخول - لم يطابق أي حساب', [
            'phone_input' => $phone,
            'phone_last9' => $last9,
            'ip'          => $ip,
        ]);

        return null;
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        return substr($digits, -9);
    }

    private function attemptWeb(Request $request, string $last9): bool
    {
        Log::channel('single')->info('[AUTH][ADMIN] البحث عن مستخدم إداري', ['phone_last9' => $last9]);

        $user = User::where('phone', 'like', '%' . $last9)
            ->select('id', 'phone', 'password')
            ->first();

        if (!$user) {
            Log::channel('single')->info('[AUTH][ADMIN] لم يوجد مستخدم بهذا الرقم في جدول users', ['phone_last9' => $last9]);
            return false;
        }

        Log::channel('single')->info('[AUTH][ADMIN] وُجد المستخدم، جاري التحقق من كلمة المرور', [
            'user_id'    => $user->id,
            'user_phone' => $user->phone,
        ]);

        if (!Hash::check($request->password, $user->password)) {
            Log::channel('single')->warning('[AUTH][ADMIN] كلمة المرور غير صحيحة', [
                'user_id'    => $user->id,
                'user_phone' => $user->phone,
            ]);
            return false;
        }

        Auth::guard('web')->login($user, $request->boolean('remember'));

        Log::channel('single')->info('[AUTH][ADMIN] تم تسجيل الدخول بنجاح', [
            'user_id'    => $user->id,
            'user_phone' => $user->phone,
        ]);

        return true;
    }

    private function attemptMarketer(Request $request, string $last9): bool
    {
        Log::channel('single')->info('[AUTH][MARKETER] البحث عن مسوق', ['phone_last9' => $last9]);

        $marketer = Marketer::where('phone', 'like', '%' . $last9)
            ->select('id', 'phone', 'password', 'status', 'is_active')
            ->first();

        if (!$marketer) {
            Log::channel('single')->info('[AUTH][MARKETER] لم يوجد مسوق بهذا الرقم في جدول marketers', ['phone_last9' => $last9]);
            return false;
        }

        Log::channel('single')->info('[AUTH][MARKETER] وُجد المسوق، جاري التحقق من كلمة المرور', [
            'marketer_id'    => $marketer->id,
            'marketer_phone' => $marketer->phone,
            'status'         => $marketer->status,
            'is_active'      => $marketer->is_active,
        ]);

        if (!Hash::check($request->password, $marketer->password)) {
            Log::channel('single')->warning('[AUTH][MARKETER] كلمة المرور غير صحيحة', [
                'marketer_id'    => $marketer->id,
                'marketer_phone' => $marketer->phone,
            ]);
            return false;
        }

        if (!$marketer->is_active) {
            Log::channel('single')->warning('[AUTH][MARKETER] الحساب غير مفعّل (is_active = false)', [
                'marketer_id'    => $marketer->id,
                'marketer_phone' => $marketer->phone,
            ]);
            return false;
        }

        if ($marketer->status !== 'approved') {
            Log::channel('single')->warning('[AUTH][MARKETER] الحساب غير معتمد', [
                'marketer_id'    => $marketer->id,
                'marketer_phone' => $marketer->phone,
                'status'         => $marketer->status,
            ]);
            return false;
        }

        Auth::guard('marketer')->login($marketer, $request->boolean('remember'));

        Log::channel('single')->info('[AUTH][MARKETER] تم تسجيل الدخول بنجاح', [
            'marketer_id'    => $marketer->id,
            'marketer_phone' => $marketer->phone,
        ]);

        return true;
    }
}
