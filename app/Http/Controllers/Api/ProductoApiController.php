<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:200',
            'stock_actual' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'tipo' => 'required|in:insumo,venta',
            'status' => 'nullable|boolean',
            'id_categoria' => 'required|exists:categorias,id_categoria',
        ]);

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

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:200',
            'stock_actual' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'tipo' => 'required|in:insumo,venta',
            'status' => 'nullable|boolean',
            'id_categoria' => 'required|exists:categorias,id_categoria',
        ]);

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