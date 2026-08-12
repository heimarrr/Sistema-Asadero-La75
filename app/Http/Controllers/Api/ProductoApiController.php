<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductoRequest;
use App\Models\Producto;

class ProductoApiController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')->get();

        return response()->json([
            'success' => true,
            'data' => $productos
        ]);
    }

    public function store(ProductoRequest $request)
    {
        $producto = Producto::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'stock_actual' => $request->stock_actual,
            'unidad_medida' => $request->unidad_medida,
            'precio_compra' => $request->precio_compra,
            'precio_venta' => $request->precio_venta,
            'tipo' => $request->tipo,
            'status' => $request->status ?? 1,
            'id_categoria' => $request->id_categoria,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto creado correctamente',
            'data' => $producto
        ], 201);
    }

    public function show($id)
    {
        $producto = Producto::with('categoria')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $producto
        ]);
    }

    public function update(ProductoRequest $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $producto->update($request->only([
            'nombre',
            'descripcion',
            'stock_actual',
            'unidad_medida',
            'precio_compra',
            'precio_venta',
            'tipo',
            'status',
            'id_categoria'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado correctamente',
            'data' => $producto
        ]);
    }

    public function destroy($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $producto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el producto'
            ], 400);
        }
    }

    public function toggleEstado($id)
    {
        $producto = Producto::findOrFail($id);

        $producto->update([
            'status' => !$producto->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del producto actualizado correctamente',
            'data' => $producto
        ]);
    }
}
