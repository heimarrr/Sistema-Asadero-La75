<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Producto;
use Carbon\Carbon;
use DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard principal
     */
    public function index()
    {
        $hoy = Carbon::now()->toDateString();
        $mes = Carbon::now()->month;
        $anio = Carbon::now()->year;

        // 1️⃣ Ventas del día
        $ventasHoy = Venta::whereDate('fecha', $hoy)->sum('total');

        // 2️⃣ Compras del día
        $totalComprasDia = Compra::whereDate('fecha', $hoy)->sum('total');

        // 3️⃣ Compras del mes
        $comprasMes = Compra::whereMonth('fecha', $mes)
                            ->whereYear('fecha', $anio)
                            ->sum('total');

        // 4️⃣ Total productos
        $totalProductos = Producto::count();

        // 5️⃣ Productos con stock bajo
        $productosCriticos = Producto::where('stock_actual', '<', 5)->count();

        // 6️⃣ Total ventas del mes
        $totalVentas = Venta::whereMonth('fecha', $mes)
                            ->whereYear('fecha', $anio)
                            ->sum('total');

        // 7️⃣ Total compras del mes
        $totalCompras = Compra::whereMonth('fecha', $mes)
                              ->whereYear('fecha', $anio)
                              ->sum('total');

        // 8️⃣ Ventas por usuario
        $ventasPorUsuario = Venta::select('id_usuario', DB::raw('SUM(total) as total'))
            ->groupBy('id_usuario')
            ->with('usuario')
            ->get()
            ->map(function ($item) {
                $item->nombre = $item->usuario->nombre ?? 'Usuario Eliminado';
                return $item;
            });
            

        // 9️⃣ Top 5 más vendidos
        $topVendidos = DB::table('detalle_ventas')
            ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
            ->select('productos.nombre', DB::raw('SUM(detalle_ventas.cantidad) as cantidad'))
            ->groupBy('productos.nombre')
            ->orderByDesc('cantidad')
            ->limit(5)
            ->get();

        // 🔟 Top 5 más comprados
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
