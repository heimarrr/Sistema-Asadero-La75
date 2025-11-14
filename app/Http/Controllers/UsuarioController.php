<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Rol;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('rol')->get();
        $roles = Rol::all(); // Necesario para los modales
        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'usuario' => 'required|string|max:255|unique:usuarios,usuario',
            'correo' => 'required|email|unique:usuarios,correo',
            'contrasena' => 'required|string|min:4',
            'id_rol' => 'required|integer',
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'usuario' => $request->usuario,
            'correo' => $request->correo,
            'contrasena' => bcrypt($request->contrasena), // encripta
            'id_rol' => $request->id_rol,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente');
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'usuario' => 'required|string|max:255|unique:usuarios,usuario,' . $usuario->id_usuario . ',id_usuario',
            'correo' => 'required|email|unique:usuarios,correo,' . $usuario->id_usuario . ',id_usuario',
            'id_rol' => 'required|integer',
        ]);

        $usuario->nombre = $request->nombre;
        $usuario->usuario = $request->usuario;
        $usuario->correo = $request->correo;
        $usuario->id_rol = $request->id_rol;

        if ($request->filled('contrasena')) {
            $usuario->contrasena = bcrypt($request->contrasena);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente');
    }
}
