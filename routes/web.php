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


// =============================================
// LOGIN
// =============================================
Route::get('/', fn() => redirect('/login'));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// =============================================
// RUTAS PROTEGIDAS
// =============================================
Route::middleware(['auth'])->group(function () {

    // HOME (lo pueden ver todos los roles)
    Route::get('/home', [HomeController::class, 'index'])->name('home');


    // =========================================
    // USUARIOS (SOLO ADMIN) -> rol:1
    // =========================================
    Route::middleware(['rol:1'])->group(function () {

        Route::resource('usuarios', UsuarioController::class);
        Route::post('usuarios/{id}/toggle-estado', [UsuarioController::class, 'toggleEstado'])
            ->name('usuarios.toggleEstado');
    });


    // =========================================
    // CATEGORÍAS, PROVEEDORES, PRODUCTOS  
    // (ACCESO LECTURA: 1, 2, 3 | ESCRITURA: SOLO 1)  
    // =========================================

    // Rutas de SOLO LECTURA (index, show) para Categorías, Proveedores y Productos
    Route::middleware(['rol:1,2,3'])->group(function () {

        // Todos pueden ver el listado de Productos
        Route::resource('productos', ProductoController::class)->only(['index', 'show']);
        
        // Todos pueden ver el listado de Categorías
        Route::resource('categorias', CategoriaController::class)->only(['index', 'show']);

        // Todos pueden ver el listado de Proveedores
        Route::resource('proveedores', ProveedorController::class)->only(['index', 'show']);
    });
    
    // Rutas de ESCRITURA (create, store, edit, update, destroy) SOLO para Admin (rol:1)
    Route::middleware(['rol:1'])->group(function () {

        // ESCRITURA para Categorías
        Route::resource('categorias', CategoriaController::class)->except(['index', 'show']);
        Route::post('categorias/{id}/toggle-estado', [CategoriaController::class, 'toggleEstado'])
            ->name('categorias.toggleEstado');

        // ESCRITURA para Proveedores
        Route::resource('proveedores', ProveedorController::class)->except(['index', 'show']);
        Route::post('proveedores/{id}/toggle-estado', [ProveedorController::class, 'toggleEstado'])
            ->name('proveedores.toggleEstado');

        // ESCRITURA para Productos
        Route::resource('productos', ProductoController::class)->except(['index', 'show']);
        Route::post('productos/{id}/toggle-estado', [ProductoController::class, 'toggleEstado'])
            ->name('productos.toggleEstado');
    });


    // =========================================
    // COMPRAS (ADMIN + COMPRADOR)
    // Roles permitidos: 1,3
    // =========================================
    Route::middleware(['rol:1,3'])->group(function () {

        Route::resource('compras', CompraController::class);
        Route::patch('compras/{compra}/anular', [CompraController::class, 'anular'])
            ->name('compras.anular');

        Route::post('compras/{id}/toggle-estado', [CompraController::class, 'toggleEstado'])
            ->name('compras.toggleEstado');
    });


    // =========================================
    // VENTAS (ADMIN + CAJERO)
    // Roles permitidos: 1,2
    // =========================================
    Route::middleware(['rol:1,2'])->group(function () {

        Route::resource('ventas', VentaController::class);
        Route::patch('ventas/{venta}/anular', [VentaController::class, 'anular'])
            ->name('ventas.anular');

        Route::post('ventas/{id}/toggle-estado', [VentaController::class, 'toggleEstado'])
            ->name('ventas.toggleEstado');
    });


    // =========================================
    // REPORTES (ADMIN + CAJERO + COMPRAS)
    // Roles permitidos: 1,2,3
    // =========================================
    // Hacemos que todos los roles operativos puedan ver los reportes
    Route::middleware(['rol:1,2,3'])->group(function () { 

        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

        Route::get('/reportes/ventas', [ReporteController::class, 'ventas'])
            ->name('reportes.ventas');

        Route::get('/reportes/compras', [ReporteController::class, 'compras'])
            ->name('reportes.compras');

        Route::get('/reportes/inventario', [ReporteController::class, 'inventario'])
            ->name('reportes.inventario');
    });

});