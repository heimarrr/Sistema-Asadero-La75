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
            'password' => 'required|string|min:8',
        ]);

        $campo = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'correo' : 'usuario';


        $usuario = Usuario::where($campo, $request->login)
                          ->where('estado', 1)
                          ->first();

        if ($usuario && Hash::check($request->password, $usuario->contrasena)) {
            Auth::login($usuario);

            $request->session()->regenerate();

            return redirect()->intended('/home');
        }

        return back()->withErrors(['login' => 'Credenciales incorrectas o usuario inactivo'])
                     ->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
