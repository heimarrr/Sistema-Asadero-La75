<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoriaRequest;
use App\Models\Categoria;

class CategoriaApiController extends Controller
{
    public function index()
    {
        $categorias = Categoria::all();

        return response()->json([
            'success' => true,
            'data' => $categorias
        ]);
    }

    public function store(CategoriaRequest $request)
    {
        $categoria = Categoria::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion ?? null,
            'status' => $request->status ?? 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada correctamente',
            'data' => $categoria
        ], 201);
    }

    public function show($id)
    {
        $categoria = Categoria::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $categoria
        ]);
    }

    public function update(CategoriaRequest $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $categoria->update($request->only([
            'nombre',
            'descripcion',
            'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada correctamente',
            'data' => $categoria
        ]);
    }

    public function destroy($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            $categoria->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la categoría porque está asociada a productos'
            ], 400);
        }
    }

    public function toggleEstado($id)
    {
        $categoria = Categoria::findOrFail($id);

        $categoria->update([
            'status' => !$categoria->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de la categoría actualizado correctamente',
            'data' => $categoria
        ]);
    }
}