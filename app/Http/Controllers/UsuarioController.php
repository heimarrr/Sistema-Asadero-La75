<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Rol;

class UsuarioController extends Controller
{
    // Listar usuarios
    public function index()
    {
        $usuarios = Usuario::with('rol')->get();
        $roles = Rol::all(); // Para los modales
        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    // Crear usuario
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'usuario' => 'required|string|max:255|unique:usuarios,usuario',
            'correo' => 'required|email|unique:usuarios,correo',
            'contrasena' => 'required|string|min:4',
            'id_rol' => 'required|integer',
            'estado' => 'required|boolean',
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'usuario' => $request->usuario,
            'correo' => $request->correo,
            'contrasena' => bcrypt($request->contrasena),
            'id_rol' => $request->id_rol,
            'estado' => $request->estado,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente');
    }

    // Actualizar usuario
    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'usuario' => 'required|string|max:255|unique:usuarios,usuario,' . $usuario->id_usuario . ',id_usuario',
            'correo' => 'required|email|unique:usuarios,correo,' . $usuario->id_usuario . ',id_usuario',
            'id_rol' => 'required|integer',
            'estado' => 'required|boolean',
        ]);

        $usuario->nombre = $request->nombre;
        $usuario->usuario = $request->usuario;
        $usuario->correo = $request->correo;
        $usuario->id_rol = $request->id_rol;
        $usuario->estado = $request->estado;

        if ($request->filled('contrasena')) {
            $usuario->contrasena = bcrypt($request->contrasena);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }

    // Eliminar usuario
    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente');
    }

    // Cambiar estado (activar/desactivar)
    public function toggleEstado($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->estado = $usuario->estado ? 0 : 1;
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Estado del usuario actualizado');
    }
}
