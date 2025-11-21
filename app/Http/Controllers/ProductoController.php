<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    // Listar productos
    public function index()
    {
        $productos = Producto::with('categoria')->get();
        $categorias = Categoria::all();
        return view('productos.index', compact('productos', 'categorias'));
    }

    // Mostrar formulario para crear
    public function create()
    {
        $categorias = Categoria::all();
        return view('productos.create', compact('categorias'));
    }

    // Guardar nuevo producto
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

        Producto::create($request->all());

        return redirect()->route('productos.index')->with('success', 'Producto agregado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::all();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    // Actualizar producto
    public function update(Request $request, $id)
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

        $producto = Producto::findOrFail($id);
        
        $producto->update($request->all()); 

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    // Eliminar producto
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }

    // Cambiar estado (activar/desactivar)
    public function toggleEstado($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->status = $producto->status ? 0 : 1;
        $producto->save();

        return redirect()->route('productos.index')->with('success', 'Estado del producto actualizado');
    }
}