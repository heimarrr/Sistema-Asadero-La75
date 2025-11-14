<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    // Listar productos
    public function index()
    {
        $productos = Producto::with(['categoria', 'proveedor'])->get();
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();
        return view('productos.index', compact('productos', 'categorias', 'proveedores'));
    }

    // Mostrar formulario para crear
    public function create()
    {
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();
        return view('productos.create', compact('categorias', 'proveedores'));
    }

    // Guardar nuevo producto
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'precio_compra' => 'required|numeric',
            'precio_venta' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
        ]);

        Producto::create($request->all());

        return redirect()->route('productos.index')->with('success', 'Producto agregado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();
        return view('productos.edit', compact('producto', 'categorias', 'proveedores'));
    }

    // Actualizar producto
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'precio_compra' => 'required|numeric',
            'precio_venta' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
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
}
