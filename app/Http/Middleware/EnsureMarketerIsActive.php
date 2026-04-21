<?php

namespace App\Http\Middleware;

use Closure;

class EnsureMarketerIsActive
{
    public function handle($request, Closure $next)
    {
        $user = auth('marketer')->user();

        if (!$user || !$user->is_active || $user->status !== 'approved') {
            auth('marketer')->logout();
            return redirect('/login');
        }

        return $next($request);
    }
}