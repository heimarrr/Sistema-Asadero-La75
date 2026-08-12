<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('admin', function ($user) {
            return $user->id_rol == 1;
        });


        Gate::define('cajero', function ($user) {
            return $user->id_rol == 2;
        });

        Gate::define('compras', function ($user) {
            return $user->id_rol == 3;
        });
    }
}
