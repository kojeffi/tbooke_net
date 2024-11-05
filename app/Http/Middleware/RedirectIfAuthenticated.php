<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null)
{
    if (Auth::guard($guard)->check() && !Auth::user()->hasVerifiedEmail()) {
        return redirect()->route('login'); // Redirect to your login route
    }

    return $next($request);
}

}
