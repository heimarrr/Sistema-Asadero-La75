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
    /**
     * Listado de ventas
     */
    public function index()
    {
        // Carga la relación 'usuario' y ordena por fecha de creación descendente.
        $ventas = Venta::with('usuario')->latest()->get();
        return view('ventas.index', compact('ventas'));
    }

    /**
     * Muestra el formulario para crear una nueva venta
     */
    public function create()
    {
        // Trae productos activos con el stock para el frontend.
        $productos = Producto::where('status', 1)
                            ->where('tipo', 'venta')
                            ->select('id_producto', 'nombre', 'precio_venta', 'stock_actual')
                            ->get();
        return view('ventas.create', compact('productos'));
    }

    /**
     * Almacena una nueva venta en la base de datos (REFORZADO EN VALIDACIÓN Y SEGURIDAD)
     */
    public function store(Request $request)
    {
        // 1. Validar las claves críticas
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|integer|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1', 
        ]);

        DB::beginTransaction();

        try {
            $totalCalculado = 0;
            $productosVenta = [];

            // 2. Pre-validación, cálculo y verificación de stock
            foreach ($request->productos as $idProducto => $item) {
                $cantidad = (int) $item['cantidad']; 
                
                $producto = Producto::findOrFail($idProducto);

                // Validación de stock
                if ($cantidad > $producto->stock_actual) {
                    throw new \Exception("Stock insuficiente para el producto '{$producto->nombre}'. Solicitado: {$cantidad}, Stock actual: {$producto->stock_actual}");
                }

                // Recalcular subtotal y total (seguridad)
                $precioUnitario = $producto->precio_venta;
                $subtotal = $precioUnitario * $cantidad;
                $totalCalculado += $subtotal;

                // Almacenar datos limpios y verificados
                $productosVenta[] = [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotal,
                ];
            }
            
            // 3. Crear la venta
            $venta = Venta::create([
                'id_usuario' => Auth::id(), 
                // Usa la fecha del request o la actual por defecto
                'fecha' => $request->get('fecha', Carbon::now()->format('Y-m-d')), 
                'total' => $totalCalculado,
                'status' => 1,
            ]);

            // 4. Guardar detalles y actualizar stock de forma atómica
            foreach ($productosVenta as $item) {
                // Registrar detalle (usando la relación)
                $venta->detalleVentas()->create([
                    'id_producto' => $item['producto']->id_producto,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['subtotal'],
                    'status' => 1,
                ]);

                // Actualizar stock de forma atómica (decremento)
                $item['producto']->decrement('stock_actual', $item['cantidad']);
            }

            DB::commit();
            return redirect()->route('ventas.index')->with('success', 'Venta registrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Retorna con el mensaje de error y los inputs para que no pierda los datos estáticos
            return back()->with('error', 'Error al registrar la venta: ' . $e->getMessage())->withInput();
        }
    }
    
    
    /**
     * Muestra los detalles de una venta específica.
     *
     * @param \App\Models\Venta $venta El modelo Venta inyectado por Route Model Binding.
     * @return \Illuminate\View\View
     */
    public function show(Venta $venta)
    {
        // Carga las relaciones necesarias para la vista de detalle:
        // - usuario (vendedor)
        // - detalleVentas (productos en la venta)
        // - producto (relación dentro de detalleVentas)
        $venta->load(['usuario', 'detalleVentas.producto']);

        return view('ventas.show', compact('venta'));
    }

    /**
     * Anular venta
     */
    public function destroy(Venta $venta)
    {
        DB::beginTransaction();

        try {
            // Verifica si ya está anulada
            if ($venta->status == 0) {
                return redirect()->back()->with('error', 'La venta ya está anulada.');
            }

            // 1. Revertir stock y anular detalles
            foreach ($venta->detalleVentas as $detalle) {
                $producto = Producto::find($detalle->id_producto);
                
                if ($producto) {
                    // Reversión de stock de forma atómica
                    $producto->increment('stock_actual', $detalle->cantidad);
                }

                // Anular detalle
                $detalle->update(['status' => 0]); 
            }

            // 2. Marcar venta como anulada
            $venta->update(['status' => 0]);

            DB::commit();
            return redirect()->route('ventas.index')->with('success', 'La venta fue anulada exitosamente y el stock revertido.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al anular la venta: ' . $e->getMessage());
        }
    }
}