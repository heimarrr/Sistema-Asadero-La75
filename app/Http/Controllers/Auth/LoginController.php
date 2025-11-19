<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class LoginController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        // Detectar si ingresó correo o usuario
        $campo = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'correo' : 'usuario';

        // Buscar usuario activo
        $usuario = Usuario::where($campo, $request->login)
                          ->where('estado', 1)
                          ->first();

        // Verificar contraseña
        if ($usuario && Hash::check($request->password, $usuario->contrasena)) {
            Auth::login($usuario);

            // Regenerar sesión para seguridad
            $request->session()->regenerate();

            return redirect()->intended('/home');
        }

        // Si falla
        return back()->withErrors(['login' => 'Credenciales incorrectas o usuario inactivo'])
                     ->withInput();
    }

    /**
     * Logout seguro
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
