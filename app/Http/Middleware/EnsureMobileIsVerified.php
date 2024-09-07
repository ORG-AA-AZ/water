<?php

namespace App\Http\Middleware;

use Closure;

class EnsureMobileIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (! $request->user() || ! $request->user()->mobile_verified_at) {
            return response()->json(['message' => 'Your mobile number is not verified'], 403);
        }

        return $next($request);
    }
}
