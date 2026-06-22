<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RolRequest;
use App\Models\Rol;

class RolesApiController extends Controller
{
    public function index()
    {
        $roles = Rol::all();

        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    public function store(RolRequest $request)
    {
        $rol = Rol::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'status' => $request->status ?? 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rol creado correctamente',
            'data' => $rol
        ], 201);
    }

    public function show($id)
    {
        $rol = Rol::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $rol
        ]);
    }

    public function update(RolRequest $request, $id)
    {
        $rol = Rol::findOrFail($id);

        $rol->update($request->only([
            'nombre',
            'descripcion',
            'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado correctamente',
            'data' => $rol
        ]);
    }

    public function destroy($id)
    {
        try {
            $rol = Rol::findOrFail($id);
            $rol->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rol eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el rol porque está asociado a usuarios'
            ], 400);
        }
    }

    public function toggleEstado($id)
    {
        $rol = Rol::findOrFail($id);

        $rol->update([
            'status' => !$rol->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del rol actualizado correctamente',
            'data' => $rol
        ]);
    }
}