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
        $roles = Rol::all();

        return response()->json([
            'usuarios' => $usuarios,
            'roles' => $roles
        ]);
    }

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

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'usuario' => $request->usuario,
            'correo' => $request->correo,
            'contrasena' => bcrypt($request->contrasena),
            'id_rol' => $request->id_rol,
            'estado' => $request->estado,
        ]);

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'data' => $usuario
        ], 201);
    }

    public function show($id)
    {
        $usuario = Usuario::with('rol')->findOrFail($id);

        return response()->json($usuario);
    }

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

        return response()->json([
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
                'message' => 'Usuario eliminado correctamente'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => 'No se puede eliminar este usuario porque tiene datos asociados.'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al eliminar el usuario.'
            ], 500);
        }
    }

    public function toggleEstado($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->estado = $usuario->estado ? 0 : 1;
        $usuario->save();

        return response()->json([
            'message' => 'Estado actualizado',
            'data' => $usuario
        ]);
    }
}