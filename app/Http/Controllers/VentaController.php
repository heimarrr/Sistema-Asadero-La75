<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    /**
     * Listado de ventas
     */
    public function index()
    {
        $ventas = Venta::with('usuario')->orderBy('id_venta', 'DESC')->get();
        return view('ventas.index', compact('ventas'));
    }

    /**
     * Formulario crear venta
     */
    public function create()
    {
        $productos = Producto::where('status', 1)->get();
        return view('ventas.create', compact('productos'));
    }

    /**
     * Guardar venta
     */
    public function store(Request $request)
    {
        $request->validate([
            'total' => 'required|numeric|min:0',
            'productos' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {

            // Crear la venta
            $venta = Venta::create([
                'id_usuario' => Auth::user()->id_usuario,
                'fecha' => date('Y-m-d'),
                'total' => $request->total,
                'status' => 1,
            ]);

            // Guardar detalles y actualizar stock
            foreach ($request->productos as $p) {

                // Registrar detalle
                DetalleVenta::create([
                    'id_venta' => $venta->id_venta,
                    'id_producto' => $p['id_producto'],
                    'cantidad' => $p['cantidad'],
                    'precio_unitario' => $p['precio'], // debe llamarse igual que en BD
                    'subtotal' => $p['subtotal'],
                    'status' => 1,
                ]);

                // Actualizar stock
                $producto = Producto::find($p['id_producto']);
                if ($producto) {
                    $producto->stock = $producto->stock - $p['cantidad'];
                    $producto->save();
                }
            }

            DB::commit();
            return redirect()->route('ventas.index')->with('success', 'Venta registrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la venta.');
        }
    }

    /**
     * Mostrar detalles de una venta
     */
    public function show($id)
    {
        $venta = Venta::with(['usuario', 'detalleVentas.producto'])
                        ->where('id_venta', $id)
                        ->firstOrFail();

        return view('ventas.show', compact('venta'));
    }

    /**
     * Anular venta (status = 0)
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $venta = Venta::with('detalleVentas')->findOrFail($id);

            // Si ya está anulada no hacemos nada
            if ($venta->status == 0) {
                return redirect()->back()->with('error', 'La venta ya está anulada.');
            }

            // Revertir stock
            foreach ($venta->detalleVentas as $detalle) {
                $producto = Producto::find($detalle->id_producto);
                if ($producto) {
                    $producto->stock += $detalle->cantidad;
                    $producto->save();
                }

                $detalle->status = 0;
                $detalle->save();
            }

            // Marcar venta como anulada
            $venta->status = 0;
            $venta->save();

            DB::commit();
            return redirect()->route('ventas.index')->with('success', 'La venta fue anulada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al anular la venta.');
        }
    }
}
