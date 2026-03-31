<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
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

        if (in_array($role, ['admin', 'manager'], true)) {
            return redirect()->route('dashboard.index');
        }

        if ($role === 'rider') {
            return redirect()->route('rider.dashboard');
        }

        if ($role !== 'customer') {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
