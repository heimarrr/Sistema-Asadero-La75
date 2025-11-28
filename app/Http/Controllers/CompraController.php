<?php

// app/Http/Controllers/CompraController.php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CompraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    public function index()
    {
        $compras = Compra::with(['proveedor', 'usuario'])->orderBy('fecha', 'desc')->paginate(10);
        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('status', 1)->get();
        $productos = Producto::where('status', 1)
                        ->where('tipo', 'insumo')
                        ->get();
        
        return view('compras.create', compact('proveedores', 'productos'));
    }

    public function store(Request $request)
    {
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
            
            $compra = Compra::create([
                'id_proveedor' => $request->id_proveedor,
                'id_usuario' => Auth::id(),
                'fecha' => $request->fecha,
                'total' => $request->total_compra,
                'status' => 1,
            ]);

            foreach ($request->productos as $item) {

                $subtotal = round($item['cantidad'] * $item['precio_unitario'], 2);
                
                $compra->detalles()->create([
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $subtotal,
                    'status' => 1,
                ]);

                // Obtener el producto comprado
                $producto = Producto::find($item['id_producto']);
                
                if ($producto) {
                    // SUMAR STOCK NORMAL DEL PRODUCTO
                    $producto->stock_actual += $item['cantidad'];
                    $producto->save();

                    // 🔥---------------------------------------------
                    // 🔥 ACTUALIZACIÓN AUTOMÁTICA DE POLLO ASADO
                    // 🔥---------------------------------------------
                    if ($producto->nombre === 'Pollo crudo') {

                        $polloAsado = Producto::where('nombre', 'Pollo asado')->first();

                        if ($polloAsado) {
                            $polloAsado->stock_actual += $item['cantidad']; // 1 crudo = 1 asado
                            $polloAsado->save();
                        }
                    }
                    // 🔥---------------------------------------------
                }
            }

            DB::commit();

            return redirect()->route('compras.index')
                ->with('success', 'Compra N° ' . $compra->id_compra . ' registrada con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar la compra: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'Error al registrar la compra: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $compra = Compra::with(['proveedor', 'usuario', 'detalles.producto'])
                    ->findOrFail($id);
        
        return view('compras.show', compact('compra'));
    }
    
    public function destroy($id)
    {
        $compra = Compra::with('detalles')->findOrFail($id);
        
        if ($compra->status != 1) {
            return redirect()->route('compras.index')
                ->with('error', 'La compra N° ' . $compra->id_compra . ' ya está anulada.');
        }

        try {
            DB::beginTransaction();
            
            foreach ($compra->detalles as $detalle) {

                $producto = Producto::find($detalle->id_producto);

                if ($producto) {

                    // Revertir stock del producto ORIGINAL
                    $producto->stock_actual -= $detalle->cantidad;
                    if ($producto->stock_actual < 0) $producto->stock_actual = 0;
                    $producto->save();

                    // 🔥---------------------------------------------
                    // 🔥 REVERTIR TAMBIÉN PARA POLLO ASADO
                    // 🔥---------------------------------------------
                    if ($producto->nombre === 'Pollo crudo') {

                        $polloAsado = Producto::where('nombre', 'Pollo asado')->first();

                        if ($polloAsado) {
                            $polloAsado->stock_actual -= $detalle->cantidad;

                            if ($polloAsado->stock_actual < 0)
                                $polloAsado->stock_actual = 0;

                            $polloAsado->save();
                        }
                    }
                    // 🔥---------------------------------------------
                }
            }

            $compra->status = 0;
            $compra->save();

            DB::commit();

            return redirect()->route('compras.index')
                ->with('success', 'Compra N° ' . $compra->id_compra . ' anulada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al anular la compra: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al anular la compra. Detalles: ' . $e->getMessage());
        }
    }
}
