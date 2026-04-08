<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Rol;

class UsuarioApiController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('rol')->get();

        return response()->json([
            'success' => true,
            'data' => $usuarios
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'usuario' => 'required|string|max:255|unique:usuarios,usuario',
            'correo' => 'required|email|unique:usuarios,correo',
            'contrasena' => 'required|string|min:6',
            'id_rol' => 'required|exists:roles,id_rol',
            'estado' => 'required|boolean',
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'usuario' => $request->usuario,
            'correo' => $request->correo,
            'contrasena' => bcrypt($request->contrasena),
            'id_rol' => $request->id_rol,
            'estado' => $request->estado,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente',
            'data' => $usuario
        ], 201);
    }

    public function show($id)
    {
        $usuario = Usuario::with('rol')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $usuario
        ]);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'usuario' => 'required|string|max:255|unique:usuarios,usuario,' . $usuario->id_usuario . ',id_usuario',
            'correo' => 'required|email|unique:usuarios,correo,' . $usuario->id_usuario . ',id_usuario',
            'id_rol' => 'required|exists:roles,id_rol',
            'estado' => 'required|boolean',
        ]);

        $usuario->update($request->only([
            'nombre',
            'usuario',
            'correo',
            'id_rol',
            'estado'
        ]));

        if ($request->filled('contrasena')) {
            $usuario->update([
                'contrasena' => bcrypt($request->contrasena)
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente',
            'data' => $usuario
        ]);
    }

    public function destroy($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'No se puede eliminar el usuario'
            ], 400);
        }
    }

    public function toggleEstado($id)
    {
        $usuario = Usuario::findOrFail($id);

        $usuario->update([
            'estado' => !$usuario->estado
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado',
            'data' => $usuario
        ]);
    }
}