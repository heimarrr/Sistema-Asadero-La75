<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompraRequest;
use App\Models\Compra;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CompraApiController extends Controller
{
    public function index()
    {
        $compras = Compra::with([
            'proveedor',
            'usuario'
        ])->orderBy('fecha', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $compras
        ]);
    }

    public function store(CompraRequest $request)
    {
        try {

            DB::beginTransaction();

            $compra = Compra::create([
                'id_proveedor' => $request->id_proveedor,
                'id_usuario' => Auth::id(),
                'fecha' => $request->fecha,
                'total' => $request->total_compra,
                'status' => 1,
            ]);

            foreach ($request->productos as $item) {

                $subtotal = round(
                    $item['cantidad'] * $item['precio_unitario'],
                    2
                );

                $compra->detalles()->create([
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $subtotal,
                    'status' => 1,
                ]);

                $producto = Producto::find($item['id_producto']);

                if ($producto) {

                    // 🔥 Aumentar stock
                    $producto->stock_actual += $item['cantidad'];

                    $producto->save();

                    // 🔥 Regla especial
                    if ($producto->nombre === 'Pollo crudo') {

                        $polloAsado = Producto::where(
                            'nombre',
                            'Pollo asado'
                        )->first();

                        if ($polloAsado) {

                            $polloAsado->stock_actual += $item['cantidad'];

                            $polloAsado->save();
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Compra registrada correctamente.',
                'data' => $compra
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error(
                'Error al registrar la compra: ' . $e->getMessage()
            );

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la compra.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show($id)
    {
        $compra = Compra::with([
            'proveedor',
            'usuario',
            'detalles.producto'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $compra
        ]);
    }

    public function destroy($id)
    {
        $compra = Compra::with('detalles')->findOrFail($id);

        if ($compra->status != 1) {

            return response()->json([
                'success' => false,
                'message' => 'La compra ya está anulada.'
            ], 400);
        }

        try {

            DB::beginTransaction();

            foreach ($compra->detalles as $detalle) {

                $producto = Producto::find($detalle->id_producto);

                if ($producto) {

                    // 🔥 Revertir stock
                    $producto->stock_actual =
                        $producto->stock_actual - $detalle->cantidad;

                    if ($producto->stock_actual < 0) {
                        $producto->stock_actual = 0;
                    }

                    $producto->save();
                }
            }

            $compra->status = 0;

            $compra->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Compra anulada correctamente.'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error(
                'Error al anular la compra: ' . $e->getMessage()
            );

            return response()->json([
                'success' => false,
                'message' => 'Error al anular la compra.',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
