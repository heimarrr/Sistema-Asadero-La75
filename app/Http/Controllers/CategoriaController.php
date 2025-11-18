<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::all();
        return view('categorias.index', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:100'
        ]);

        Categoria::create($request->all());

        return redirect()->back()->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'nombre' => 'required|max:100'
        ]);

        $categoria->update($request->all());

        return redirect()->back()->with('success', 'Categoría actualizada.');
    }

    public function destroy($id)
    {
        try {
            Categoria::destroy($id);
            return redirect()->back()->with('success', 'Categoría eliminada.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se puede eliminar esta categoría porque tiene productos asociados.');
        }
    }

      // Cambiar estado (activar/desactivar)
    public function toggleEstado($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->status = $categoria->status ? 0 : 1;
        $categoria->save();

        return redirect()->route('categorias.index')->with('success', 'Estado de la categoría actualizado');
    }
}
