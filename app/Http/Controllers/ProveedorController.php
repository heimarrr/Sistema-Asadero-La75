<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    // Listar proveedores
    public function index()
    {
        $proveedores = Proveedor::all();
        return view('proveedores.index', compact('proveedores'));
    }

    // Guardar nuevo proveedor
    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'direccion'=> 'nullable|string|max:150',
            'correo'   => 'nullable|email|max:100'
        ]);

        Proveedor::create($request->all());

        return redirect()->back()->with('success', 'Proveedor registrado con éxito.');
    }

    // Actualizar proveedor
    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $request->validate([
            'nombre'   => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'direccion'=> 'nullable|string|max:150',
            'correo'   => 'nullable|email|max:100'
        ]);

        $proveedor->update($request->all());

        return redirect()->back()->with('success', 'Proveedor actualizado correctamente.');
    }

    // Eliminar proveedor
    public function destroy($id)
    {
        Proveedor::destroy($id);

        return redirect()->back()->with('success', 'Proveedor eliminado.');
    }

    public function toggleEstado($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->status = $proveedor->status ? 0 : 1;
        $proveedor->save();

        return redirect()->route('proveedores.index')->with('success', 'Estado del proveedor actualizado');
    }
}
