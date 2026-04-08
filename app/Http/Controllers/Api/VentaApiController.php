<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VentaApiController extends Controller
{
    public function index()
    {
        $ventas = Venta::with('usuario')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $ventas
        ]);
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

            foreach ($request->productos as $item) {

                $producto = Producto::findOrFail($item['id_producto']);
                $cantidad = (int) $item['cantidad'];

                if ($cantidad > $producto->stock_actual) {
                    throw new \Exception("Stock insuficiente para '{$producto->nombre}'");
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

                // 🔥 Descontar stock
                $item['producto']->decrement('stock_actual', $item['cantidad']);

                // 🔥 Regla especial (pollo)
                if ($item['producto']->nombre === 'Pollo asado') {
                    $polloCrudo = Producto::where('nombre', 'Pollo crudo')->first();
                    if ($polloCrudo) {
                        $polloCrudo->decrement('stock_actual', $item['cantidad']);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada correctamente',
                'data' => $venta
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function show($id)
    {
        $venta = Venta::with(['usuario', 'detalleVentas.producto'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $venta
        ]);
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $venta = Venta::with('detalleVentas')->findOrFail($id);

            if ($venta->status == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'La venta ya está anulada'
                ], 400);
            }

            foreach ($venta->detalleVentas as $detalle) {

                $producto = Producto::find($detalle->id_producto);

                if ($producto) {
                    $producto->increment('stock_actual', $detalle->cantidad);

                    if ($producto->nombre === 'Pollo asado') {
                        $polloCrudo = Producto::where('nombre', 'Pollo crudo')->first();
                        if ($polloCrudo) {
                            $polloCrudo->increment('stock_actual', $detalle->cantidad);
                        }
                    }
                }

                $detalle->update(['status' => 0]);
            }

            $venta->update(['status' => 0]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta anulada correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al anular: ' . $e->getMessage()
            ], 400);
        }
    }
}