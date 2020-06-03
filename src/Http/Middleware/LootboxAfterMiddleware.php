<?php

namespace Psytelepat\Lootbox\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class LootboxAfterMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        return $response;
    }
}
