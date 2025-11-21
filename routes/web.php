<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ReporteController;


Route::get('/', function () {
    return redirect('/login');
});

// 1. Rutas de AUTENTICACIÓN (Estas NO deben estar protegidas)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// 2. GRUPO DE RUTAS PROTEGIDAS (Middleware 'auth')
Route::middleware(['auth'])->group(function () {
    
    // Página principal
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Rutas de USUARIOS
    Route::resource('usuarios', UsuarioController::class);
    Route::post('usuarios/{id}/toggle-estado', [UsuarioController::class, 'toggleEstado'])->name('usuarios.toggleEstado');

    // Rutas de CATEGORIAS
    Route::resource('categorias', CategoriaController::class);
    Route::post('categorias/{id}/toggle-estado', [CategoriaController::class, 'toggleEstado'])->name('categorias.toggleEstado');

    // Rutas de PROVEEDORES
    Route::resource('proveedores', ProveedorController::class);
    Route::post('proveedores/{id}/toggle-estado', [ProveedorController::class, 'toggleEstado'])->name('proveedores.toggleEstado');

    // Rutas de PRODUCTOS
    Route::resource('productos', ProductoController::class);
    Route::post('productos/{id}/toggle-estado', [ProductoController::class, 'toggleEstado'])->name('productos.toggleEstado');

    // Rutas de COMPRAS
    Route::resource('compras', CompraController::class);
    Route::patch('compras/{compra}/anular', [CompraController::class, 'anular'])->name('compras.anular');
    // Nota: 'toggle-estado' en compras/ventas no es común, pero si lo necesitas, déjalo.
    Route::post('compras/{id}/toggle-estado', [CompraController::class, 'toggleEstado'])->name('compras.toggleEstado');


    // Rutas de VENTAS
    Route::resource('ventas', VentaController::class);
    Route::patch('ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');
    Route::post('ventas/{id}/toggle-estado', [VentaController::class, 'toggleEstado'])->name('ventas.toggleEstado');

    //ruta de reportes

     Route::get('/reportes', [ReporteController::class, 'index'])
        ->name('reportes.index');

    Route::get('/reportes/ventas', [ReporteController::class, 'ventas'])
        ->name('reportes.ventas');

    Route::get('/reportes/compras', [ReporteController::class, 'compras'])
        ->name('reportes.compras');

    Route::get('/reportes/inventario', [ReporteController::class, 'inventario'])
        ->name('reportes.inventario');


});