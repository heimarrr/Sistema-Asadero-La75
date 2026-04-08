<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsuarioApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\RolesApiController;
use App\Http\Controllers\Api\ProveedorApiController;
use App\Http\Controllers\Api\CategoriaApiController;
use App\Http\Controllers\Api\ProductoApiController;
use App\Http\Controllers\Api\VentaApiController;



Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    //rutas de usuarios
    Route::apiResource('usuarios', UsuarioApiController::class);
    Route::post('usuarios/{id}/toggle-estado', [UsuarioApiController::class, 'toggleEstado'])
        ->name('usuarios.toggleEstado');

    //rutas de roles
    Route::apiResource('roles', RolesApiController::class);
    Route::post('roles/{id}/toggle-estado', [RolesApiController::class, 'toggleEstado'])
        ->name('roles.toggleEstado');
    
    //rutas de proveedores
    Route::apiResource('proveedores', ProveedorApiController::class);
    Route::post('proveedores/{id}/toggle-estado', [ProveedorApiController::class, 'toggleEstado'])
        ->name('proveedores.toggleEstado');

    //rutas de categorias
    Route::apiResource('categorias', CategoriaApiController::class);
    Route::post('categorias/{id}/toggle-estado', [CategoriaApiController::class, 'toggleEstado'])
        ->name('categorias.toggleEstado');

    //rutas de productos
    Route::apiResource('productos', ProductoApiController::class);
    Route::post('productos/{id}/toggle-estado', [ProductoApiController::class, 'toggleEstado'])
        ->name('productos.toggleEstado');

    //rutas de compras y ventas se agregarán aquí posteriormente
    Route::apiResource('ventas', VentaApiController::class);
    Route::post('ventas/{id}/toggle-estado', [VentaApiController::class, 'toggleEstado'])
        ->name('ventas.toggleEstado');
});

