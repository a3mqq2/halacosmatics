<?php

namespace App\Observers;

use App\Models\Marketer;
use Illuminate\Support\Facades\Auth;

class MarketerObserver
{
    public function created(Marketer $marketer): void
    {
        if (!Auth::check()) return;

        Auth::user()->logs()->create([
            'description' => "أضاف مسوقاً جديداً: {$marketer->first_name} {$marketer->last_name}",
        ]);
    }

    public function updated(Marketer $marketer): void
    {
        if (!Auth::check()) return;

        $changed = collect($marketer->getChanges())
            ->except(['updated_at'])
            ->keys()
            ->implode(', ');

        Auth::user()->logs()->create([
            'description' => "عدّل بيانات المسوق: {$marketer->first_name} {$marketer->last_name} | الحقول: ({$changed})",
        ]);
    }

    public function deleted(Marketer $marketer): void
    {
        if (!Auth::check()) return;

        Auth::user()->logs()->create([
            'description' => "حذف المسوق: {$marketer->first_name} {$marketer->last_name}",
        ]);
    }
}
