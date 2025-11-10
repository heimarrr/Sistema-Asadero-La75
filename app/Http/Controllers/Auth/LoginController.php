<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string|min:4',
        ]);

        // Detectar si el usuario ingresó correo o nombre de usuario
        $campo = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'correo' : 'usuario';

        // Buscar usuario
        $usuario = Usuario::where($campo, $request->login)->first();

        // Verificar credenciales
        if ($usuario && $usuario->contrasena === $request->password) {
            Auth::login($usuario);
            return redirect()->intended('/home');
        }

        // Si falla
        return back()->withErrors(['login' => 'Credenciales incorrectas'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}
