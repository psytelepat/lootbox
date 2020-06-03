<?php

namespace Psytelepat\Lootbox\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class LootboxAdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guest()) {
            $user = auth()->user();
            if (isset($user->locale)) {
                app()->setLocale($user->locale);
            }

            return $user->hasPermission('browse_admin') ? $next($request) : redirect('/');
        }

        $login_url = route('lootbox.login');
        return redirect()->guest($login_url);
    }
}
