<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    public function created(User $user): void
    {
        if (!Auth::guard('web')->check()) return;

        Auth::guard('web')->user()->logs()->create([
            'description' => "أضاف مستخدماً جديداً: {$user->name}",
        ]);
    }

    public function updated(User $user): void
    {
        if (!Auth::guard('web')->check()) return;

        $changes = collect($user->getChanges())->except(['updated_at', 'password']);

        if ($changes->isEmpty()) return;

        $labels = [
            'name'           => 'الاسم',
            'phone'          => 'رقم الهاتف',
            'is_super'       => 'مدير عام',
            'perm_users'     => 'صلاحية المستخدمين',
            'perm_marketers' => 'صلاحية المسوقين',
            'perm_products'  => 'صلاحية المنتجات',
        ];

        $changed = $changes->keys()
            ->map(fn($key) => $labels[$key] ?? $key)
            ->implode(', ');

        Auth::guard('web')->user()->logs()->create([
            'description' => "عدّل بيانات المستخدم: {$user->name} | الحقول: ({$changed})",
        ]);
    }

    public function deleted(User $user): void
    {
        if (!Auth::guard('web')->check()) return;

        Auth::guard('web')->user()->logs()->create([
            'description' => "حذف المستخدم: {$user->name}",
        ]);
    }
}
