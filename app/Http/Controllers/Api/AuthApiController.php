<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class AuthApiController extends Controller
{
    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        // Detectar si es correo o usuario
        $campo = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'correo' : 'usuario';
        $usuario = Usuario::where($campo, $request->login)
                          ->where('estado', 1)
                          ->first();

        // Validar credenciales
        if (!$usuario || !Hash::check($request->password, $usuario->contrasena)) {
            return response()->json([
                'message' => 'Credenciales incorrectas o usuario inactivo.'
            ], 401);
        }

        $usuario->tokens()->delete();

        // Crear token
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Inicio de sesión exitoso',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'     => $usuario->id_usuario,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'rol'    => $usuario->id_rol,
                'estado' => $usuario->estado,
            ]
        ], 200);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        // Elimina el token actual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ], 200);
    }

    
    //USUARIO AUTENTICADO
    
    public function me(Request $request)
    {
        $usuario = $request->user();

        return response()->json([
            'id'     => $usuario->id_usuario,
            'nombre' => $usuario->nombre,
            'correo' => $usuario->correo,
            'rol'    => $usuario->id_rol,
            'estado' => $usuario->estado,
        ]);
    }
}