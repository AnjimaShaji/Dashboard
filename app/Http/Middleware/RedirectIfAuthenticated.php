<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if(Auth::guard($guard)->user()->role == 'ADMIN') {
                return redirect('/admin/reports');
            } else if(Auth::guard($guard)->user()->role == 'DOM') {
                return redirect('/dom/reports');
            } else if(Auth::guard($guard)->user()->role == 'RSM') {
                return redirect('/rsm/reports');
            } else if(Auth::guard($guard)->user()->role == 'DEALER') {
                return redirect('/dealer/reports');
            }
        }

        return $next($request);
    }
}
