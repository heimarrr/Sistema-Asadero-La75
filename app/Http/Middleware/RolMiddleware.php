<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RolMiddleware
{
    /**
     * Manejar una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        // 1. Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return response()->json([
                'error' => 'No autenticado.',
                'message' => 'Debe iniciar sesión para acceder a este recurso.'
            ], 401); // 401 Unauthorized
        }

        $user = Auth::user();

        // 2. Verificar si el rol del usuario está en la lista de permitidos
        // Nota: Asegúrate de que $user->id_rol coincida con el tipo de dato enviado (int/string)
        if (!in_array($user->id_rol, $roles)) {
            return response()->json([
                'error' => 'Acceso denegado.',
                'message' => 'No tienes los permisos (rol: ' . $user->id_rol . ') necesarios para esta acción.'
            ], 403); // 403 Forbidden
        }

        return $next($request);
    }
}