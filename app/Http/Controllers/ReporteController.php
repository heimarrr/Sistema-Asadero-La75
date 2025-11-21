<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use DB;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    // ============================
    //     REPORTE DE VENTAS
    // ============================
    public function ventas(Request $request)
    {
        $inicio = $request->inicio;
        $fin = $request->fin;

        $ventas = Venta::with(['detalles.producto'])
            ->when($inicio && $fin, function($q) use($inicio, $fin){
                $q->whereBetween('fecha', [$inicio, $fin]);
            })
            ->get();

        $total = $ventas->sum('total');

        // Producto más vendido
        $productoMasVendido = DetalleVenta::select(
                'id_producto',
                DB::raw('SUM(cantidad) as cantidad_vendida')
            )
            ->when($inicio && $fin, function($q) use($inicio, $fin){
                $q->whereHas('venta', function($query) use($inicio, $fin){
                    $query->whereBetween('fecha', [$inicio, $fin]);
                });
            })
            ->groupBy('id_producto')
            ->orderByDesc('cantidad_vendida')
            ->first();

        return view('reportes.ventas', compact(
            'ventas', 'total', 'productoMasVendido'
        ));
    }

    // ============================
    //     REPORTE DE COMPRAS
    // ============================
    public function compras(Request $request)
    {
        $inicio = $request->inicio;
        $fin = $request->fin;

        $compras = Compra::with(['detalles.producto', 'proveedor'])
            ->when($inicio && $fin, function($q) use($inicio, $fin){
                $q->whereBetween('fecha', [$inicio, $fin]);
            })
            ->get();

        $total = $compras->sum('total');

        // Producto más comprado
        $productoMasComprado = DetalleCompra::select(
                'id_producto',
                DB::raw('SUM(cantidad) as total_comprado')
            )
            ->when($inicio && $fin, function($q) use($inicio, $fin){
                $q->whereHas('compra', function($query) use($inicio, $fin){
                    $query->whereBetween('fecha', [$inicio, $fin]);
                });
            })
            ->groupBy('id_producto')
            ->orderByDesc('total_comprado')
            ->first();

        return view('reportes.compras', compact(
            'compras', 'total', 'productoMasComprado'
        ));
    }

    // ============================
    //     REPORTE DE INVENTARIO
    // ============================
    public function inventario()
    {
        $productos = Producto::all();

        return view('reportes.inventario', compact('productos'));
    }
}
