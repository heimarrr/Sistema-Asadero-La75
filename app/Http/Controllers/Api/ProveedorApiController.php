<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proveedor;

class ProveedorApiController extends Controller
{
    public function index() {
        $proveedores = Proveedor::all();

        return response()-> json([
            'success' => true,
            'data' => $proveedores
        ]);
    }

    public function store(Request $request) {
       $request->validate([
            'nombre' => 'required|string|max:255|unique:proveedores,nombre',
            'telefono' => 'nullable|string|max:20',
            'direccion'=> 'nullable|string|max:150',
            'correo'   => 'nullable|email|max:100',
            'status' => 'nullable|boolean'
        ]);

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

    public function show($id) {
        $proveedor = Proveedor::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $proveedor
        ]);
    }

    public function update(Request $request, $id) {

        $proveedor = Proveedor::findOrFail($id);

        $request-> validate([
            'nombre' => 'required|string|max:255|unique:proveedores,nombre,' . $proveedor->id_proveedor . ',id_proveedor',
            'telefono' => 'nullable|string|max:20',
            'direccion'=> 'nullable|string|max:150',
            'correo'   => 'nullable|email|max:100',
            'status' => 'nullable|boolean'
        ]);

        $proveedor->update($request->only([
            'nombre',
            'telefono',
            'direccion',
            'correo',
            'status'
        ]));

        return response()-> json([
            'success' => true,
            'message' => 'Proveedor actualizado correctamente',
            'data' => $proveedor
        ]);
    }


    public function destroy($id) {
        try{
            $proveedor = Proveedor::FindOrFail($id);
            $proveedor->delete();

            return response() -> json([
                'success' => true,
                'message' => 'Proveedor eliminado correctamente'
            ]);
        }
        catch (\Exception $e) {
            return response() -> json([
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


