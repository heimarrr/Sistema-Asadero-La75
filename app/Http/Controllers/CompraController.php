<?php

// app/Http/Controllers/CompraController.php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Agregamos Log para mejor manejo de errores

class CompraController extends Controller
{
    /**
     * Aplica el middleware 'auth' a todos los métodos.
     */
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    /**
     * Muestra una lista de compras.
     */
    public function index()
    {
        // Carga las compras con sus proveedores y usuarios para evitar el N+1 problem
        $compras = Compra::with(['proveedor', 'usuario'])->orderBy('fecha', 'desc')->paginate(10);
        
        return view('compras.index', compact('compras'));
    }

    /**
     * Muestra el formulario para crear una nueva compra.
     */
    public function create()
    {
        // Se seleccionan solo proveedores y productos activos
        $proveedores = Proveedor::where('status', 1)->get();
        $productos = Producto::where('status', 1)
                        ->where('tipo', 'insumo')   // ← AQUÍ EL FILTRO
                        ->get();
        
        return view('compras.create', compact('proveedores', 'productos'));
    }

    /**
     * Almacena una nueva compra en la base de datos (Transacción).
     */
    public function store(Request $request)
    {
        // 1. Validación
        $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
            'fecha' => 'required|date',
            'total_compra' => 'required|numeric|min:0.01', 
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio_unitario' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();
            
            // 2. Crear la cabecera de la compra
            $compra = Compra::create([
                'id_proveedor' => $request->id_proveedor,
                'id_usuario' => Auth::id(), // Aseguramos que el usuario esté autenticado
                'fecha' => $request->fecha,
                'total' => $request->total_compra,
                'status' => 1, // Compra activa/válida
            ]);

            // 3. Crear los detalles de la compra e incrementar el stock
            foreach ($request->productos as $item) {
                // Cálculo del subtotal
                $subtotal = round($item['cantidad'] * $item['precio_unitario'], 2);
                
                $compra->detalles()->create([
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $subtotal,
                    'status' => 1,
                ]);

                // 4. Actualizar el stock del producto
                $producto = Producto::find($item['id_producto']);
                if ($producto) {
                    // Usamos stock_actual como definiste en tu BD
                    $producto->stock_actual += $item['cantidad']; 
                    $producto->save();
                }
            }

            DB::commit();

            return redirect()->route('compras.index')->with('success', 'Compra N° ' . $compra->id_compra . ' registrada con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar la compra: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al registrar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Muestra una compra específica.
     */
    public function show($id)
    {
        // Carga la compra con todos sus detalles y relaciones
        $compra = Compra::with(['proveedor', 'usuario', 'detalles.producto'])->findOrFail($id);
        
        return view('compras.show', compact('compra'));
    }
    
    /**
     * Anula una compra y revierte el stock (Usando el método DELETE/destroy).
     */
    public function destroy($id)
    {
        $compra = Compra::with('detalles')->findOrFail($id);
        
        // Solo permitir anular si la compra está activa (status = 1)
        if ($compra->status != 1) {
            return redirect()->route('compras.index')->with('error', 'La compra N° ' . $compra->id_compra . ' ya está anulada.');
        }

        try {
            DB::beginTransaction();
            
            // 1. Revertir el stock de cada producto
            foreach ($compra->detalles as $detalle) {
                $producto = Producto::find($detalle->id_producto);
                if ($producto) {
                    // Resta la cantidad que se había agregado
                    $producto->stock_actual = $producto->stock_actual - $detalle->cantidad; 
                    
                    // Asegurar que el stock no sea negativo
                    if ($producto->stock_actual < 0) {
                        $producto->stock_actual = 0; 
                    }
                    $producto->save();
                }
            }

            // 2. Anular la cabecera de la compra
            $compra->status = 0; // 0 = Anulada
            $compra->save();

            DB::commit();

            return redirect()->route('compras.index')->with('success', 'La compra N° ' . $compra->id_compra . ' ha sido anulada y el stock revertido.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al anular la compra: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al anular la compra. Detalles: ' . $e->getMessage());
        }
    }
}