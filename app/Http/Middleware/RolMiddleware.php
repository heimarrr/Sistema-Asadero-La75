<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RolMiddleware
{
    public function handle($request, Closure $next, $rol)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Usuario tiene un solo rol, no una lista
        if (Auth::user()->rol->nombre !== $rol) {
            return abort(403, 'No tienes permisos para acceder');
        }

        return $next($request);
    }
}
