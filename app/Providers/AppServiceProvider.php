<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Cambiamos 'administrador' a 'admin' para coincidir con el menú
        Gate::define('admin', function ($user) {
            return $user->id_rol == 1;
        });

        // Corregimos la sintaxis de la función flecha
        Gate::define('cajero', function ($user) {
            return $user->id_rol == 2;
        });

        // Corregimos la sintaxis de la función flecha
        Gate::define('compras', function ($user) {
            return $user->id_rol == 3;
        });
    }
}
