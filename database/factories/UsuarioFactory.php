<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'usuario' => fake()->unique()->userName(),
            'correo' => fake()->unique()->safeEmail(),
            'contrasena' => Hash::make('12345678'),
            'id_rol' => 1,
            'estado' => 1,
        ];
    }
}
