<?php

namespace Psytelepat\Lootbox\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class LootboxMiddleware
{
    public function handle($request, Closure $next)
    {
        $next($request);
    }
}
