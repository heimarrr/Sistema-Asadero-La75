<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProveedorRequest;
use App\Models\Proveedor;

class ProveedorApiController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::all();

        return response()->json([
            'success' => true,
            'data' => $proveedores
        ]);
    }

    public function store(ProveedorRequest $request)
    {
        $proveedor = Proveedor::create([
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'correo' => $request->correo,
            'status' => $request->status ?? 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proveedor creado correctamente',
            'data' => $proveedor
        ], 201);
    }

    public function show($id)
    {
        $proveedor = Proveedor::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $proveedor
        ]);
    }

    public function update(ProveedorRequest $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $proveedor->update($request->only([
            'nombre',
            'telefono',
            'direccion',
            'correo',
            'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Proveedor actualizado correctamente',
            'data' => $proveedor
        ]);
    }

    public function destroy($id)
    {
        try {
            $proveedor = Proveedor::findOrFail($id);
            $proveedor->delete();

            return response()->json([
                'success' => true,
                'message' => 'Proveedor eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el Proveedor porque está asociado Productos'
            ], 400);
        }
    }

    public function toggleEstado($id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $proveedor->update([
            'status' => !$proveedor->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del Proveedor actualizado correctamente',
            'data' => $proveedor
        ]);
    }
}