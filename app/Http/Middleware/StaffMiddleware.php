<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Allows authenticated users with role {@see admin} or {@see manager} (store back office).
 */
class StaffMiddleware
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $role = Auth::user()->role;

        if ($role === 'rider') {
            return redirect()->route('rider.dashboard');
        }

        if (! in_array($role, ['admin', 'manager'], true)) {
            return redirect()->route('account.index');
        }

        return $next($request);
    }
}
