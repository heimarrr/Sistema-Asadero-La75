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


Route::get('/', function () {
    return redirect('/login');
});

// Rutas de autenticación personalizadas
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Página principal después de login
Route::get('/home', [HomeController::class, 'index'])
    ->name('home')
    ->middleware('auth');

// Módulos protegidos


Route::get('/home', function () {
    return view('home');
})->name('home');

//login para que solo usuarios autenticados 

Route::middleware(['auth'])->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{id}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
});

//rutas de usuarios
Route::resource('usuarios', UsuarioController::class);
Route::post('usuarios/{id}/toggle-estado', [UsuarioController::class, 'toggleEstado'])
    ->name('usuarios.toggleEstado');

//rutas de categorias
Route::resource('categorias', CategoriaController::class);
Route::post('categorias/{id}/toggle-estado', [CategoriaController::class, 'toggleEstado'])
    ->name('categorias.toggleEstado');

//rutas de proveedores
Route::resource('proveedores', ProveedorController::class);
Route::post('proveedores/{id}/toggle-estado', [ProveedorController::class, 'toggleEstado'])
    ->name('proveedores.toggleEstado');

//rutas de productos
Route::resource('productos', ProductoController::class);
Route::post('productos/{id}/toggle-estado', [ProductoController::class, 'toggleEstado'])
    ->name('productos.toggleEstado');

//rutas de compras
Route::resource('compras', CompraController::class);
Route::post('compras/{id}/toggle-estado', [CompraController::class, 'toggleEstado'])
    ->name('compras.toggleEstado');
Route::patch('compras/{compra}/anular', [CompraController::class, 'anular'])->name('compras.anular');

//ruta de ventas
Route::resource('ventas', VentaController::class);
Route::post('ventas/{id}/toggle-estado', [VentaController::class, 'toggleEstado'])
    ->name('ventas.toggleEstado');
Route::patch('ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');