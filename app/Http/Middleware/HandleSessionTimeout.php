<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleSessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user was authenticated but session expired
        if (!auth()->check() && $request->expectsJson()) {
            return response()->json([
                'error' => 'Session expired',
                'redirect' => route('login')
            ], 401);
        }

        if (!auth()->check() && !$request->is('login') && !$request->is('register')) {
            return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
        }

        return $next($request);
    }
}
