<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureApiAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('access_token')) {
            return redirect('/');
        }

        return $next($request);
    }
}
