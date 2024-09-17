<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckStatusCompte
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (Auth::guard()->check() && Auth::user()->is_valide == false || Auth::guard()->check() && Auth::user()->status_compte == false || Auth::guard()->check() && Auth::user()->is_deleted == true) {
        //     Auth::logout();
        //     return redirect("/login");
        // }
        return $next($request);
    }
}
