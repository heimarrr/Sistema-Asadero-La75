<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VentaController extends Controller
{
    //Listado de ventas
    public function index()
    {
        $ventas = Venta::with('usuario')->latest()->get();
        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        $productos = Producto::where('status', 1)
                            ->where('tipo', 'venta')
                            ->select('id_producto', 'nombre', 'precio_venta', 'stock_actual')
                            ->get();
        return view('ventas.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|integer|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $totalCalculado = 0;
            $productosVenta = [];

            foreach ($request->productos as $idProducto => $item) {
                $cantidad = (int) $item['cantidad'];
                
                $producto = Producto::findOrFail($idProducto);

                // Validación del stock del producto vendido
                if ($cantidad > $producto->stock_actual) {
                    throw new \Exception("Stock insuficiente para '{$producto->nombre}'.");
                }

                $precioUnitario = $producto->precio_venta;
                $subtotal = $precioUnitario * $cantidad;
                $totalCalculado += $subtotal;

                $productosVenta[] = [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotal,
                ];
            }

            $venta = Venta::create([
                'id_usuario' => Auth::id(),
                'fecha' => $request->get('fecha', Carbon::now()->format('Y-m-d')),
                'total' => $totalCalculado,
                'status' => 1,
            ]);

            foreach ($productosVenta as $item) {

                $venta->detalleVentas()->create([
                    'id_producto' => $item['producto']->id_producto,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['subtotal'],
                    'status' => 1,
                ]);

                // Descontar stock del producto vendido
                $item['producto']->decrement('stock_actual', $item['cantidad']);

                // 🔥----------------------------------------------------
                // 🔥 ACTUALIZAR POLLO CRUDO CUANDO VENDO POLLO ASADO
                // 🔥----------------------------------------------------
                if ($item['producto']->nombre === 'Pollo asado') {

                    $polloCrudo = Producto::where('nombre', 'Pollo crudo')->first();

                    if ($polloCrudo) {
                        // Descontar crudo 1 a 1
                        $polloCrudo->decrement('stock_actual', $item['cantidad']);
                    }
                }
                // 🔥----------------------------------------------------
            }

            DB::commit();
            return redirect()->route('ventas.index')->with('success', 'Venta registrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la venta: ' . $e->getMessage())
                         ->withInput();
        }
    }


    public function show(Venta $venta)
    {
        $venta->load(['usuario', 'detalleVentas.producto']);
        return view('ventas.show', compact('venta'));
    }

    public function destroy(Venta $venta)
    {
        DB::beginTransaction();

        try {
            if ($venta->status == 0) {
                return redirect()->back()->with('error', 'La venta ya está anulada.');
            }

            foreach ($venta->detalleVentas as $detalle) {
                $producto = Producto::find($detalle->id_producto);

                if ($producto) {

                    // Revertir stock normal
                    $producto->increment('stock_actual', $detalle->cantidad);

                    // 🔥-------------------------------------------------------
                    // 🔥 REVERTIR STOCK DE POLLO CRUDO SI VENTA ERA DE POLLO ASADO
                    // 🔥-------------------------------------------------------
                    if ($producto->nombre === 'Pollo asado') {

                        $polloCrudo = Producto::where('nombre', 'Pollo crudo')->first();

                        if ($polloCrudo) {
                            $polloCrudo->increment('stock_actual', $detalle->cantidad);
                        }
                    }
                    // 🔥-------------------------------------------------------
                }

                // Anular detalle
                $detalle->update(['status' => 0]);
            }

            $venta->update(['status' => 0]);

            DB::commit();
            return redirect()->route('ventas.index')->with('success', 'Venta anulada y stock revertido correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al anular: ' . $e->getMessage());
        }
    }
}
