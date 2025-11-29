<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RolMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        // Verificar si está logueado
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debe iniciar sesión');
        }

        $user = Auth::user();

        // Verificar si el rol del usuario está permitido
        if (!in_array($user->id_rol, $roles)) {
            return redirect('/')->with('error', 'No tiene permisos para acceder a esta sección');
        }

        return $next($request);
    }
}
