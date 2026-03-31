<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts routes to users with role {@see manager} only (orders & logistics).
 */
class EnsureManagerRole
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'manager') {
            return redirect()
                ->route('dashboard.index')
                ->with('status', 'That area is only available to store managers.');
        }

        return $next($request);
    }
}
