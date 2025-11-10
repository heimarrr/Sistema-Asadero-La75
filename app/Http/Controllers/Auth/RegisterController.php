<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario; // 👈 Usamos tu modelo personalizado
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Redirección después del registro.
     *
     * @var string
     */
    protected $redirectTo = '/home'; // 👈 Puedes cambiarlo según tu ruta principal

    /**
     * Crear una nueva instancia del controlador.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Validador para una solicitud de registro entrante.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nombre' => ['required', 'string', 'max:255'],
            'usuario' => ['required', 'string', 'max:50', 'unique:usuarios'],
            'correo' => ['required', 'string', 'email', 'max:255', 'unique:usuarios'],
            'contrasena' => ['required', 'string', 'min:8', 'confirmed'], // Debe venir con confirmación
        ]);
    }

    /**
     * Crear una nueva instancia de usuario después de un registro válido.
     *
     * @param  array  $data
     * @return \App\Models\Usuario
     */
    protected function create(array $data)
    {
        return Usuario::create([
            'nombre' => $data['nombre'],
            'usuario' => $data['usuario'],
            'correo' => $data['correo'],
            'contrasena' => Hash::make($data['contrasena']),
            'id_rol' => 2, // 👈 Puedes definir un rol por defecto (ej: 2 = Usuario estándar)
        ]);
    }
}
