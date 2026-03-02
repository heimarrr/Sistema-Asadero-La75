<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $hoy = Carbon::now()->toDateString();
        $mes = Carbon::now()->month;
        $anio = Carbon::now()->year;

        $ventasHoy = Venta::whereDate('fecha', $hoy)->sum('total');
        $totalComprasDia = Compra::whereDate('fecha', $hoy)->sum('total');
        $comprasMes = Compra::whereMonth('fecha', $mes)
                            ->whereYear('fecha', $anio)
                            ->sum('total');
        $totalProductos = Producto::count();
        $productosCriticos = Producto::where('stock_actual', '<', 5)->count();
        $totalVentas = Venta::whereMonth('fecha', $mes)
                            ->whereYear('fecha', $anio)
                            ->sum('total');
        $totalCompras = Compra::whereMonth('fecha', $mes)
                              ->whereYear('fecha', $anio)
                              ->sum('total');
        $ventasPorUsuario = Venta::select('id_usuario', DB::raw('SUM(total) as total'))
            ->groupBy('id_usuario')
            ->with('usuario')
            ->get()
            ->map(function ($item) {
                $item->nombre = $item->usuario->nombre ?? 'Usuario Eliminado';
                return $item;
            });
        $topVendidos = DB::table('detalle_ventas')
            ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
            ->select('productos.nombre', DB::raw('SUM(detalle_ventas.cantidad) as cantidad'))
            ->groupBy('productos.nombre')
            ->orderByDesc('cantidad')
            ->limit(5)
            ->get();
        $topComprados = DB::table('detalle_compras')
            ->join('productos', 'detalle_compras.id_producto', '=', 'productos.id_producto')
            ->select('productos.nombre', DB::raw('SUM(detalle_compras.cantidad) as cantidad'))
            ->groupBy('productos.nombre')
            ->orderByDesc('cantidad')
            ->limit(5)
            ->get();

        return view('home', compact(
            'ventasHoy',
            'totalComprasDia',
            'comprasMes',
            'totalProductos',
            'productosCriticos',
            'totalVentas',
            'totalCompras',
            'ventasPorUsuario',
            'topVendidos',
            'topComprados'
        ));
    }
}
