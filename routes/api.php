<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\UsuarioApiController;
use App\Http\Controllers\Api\RolesApiController;
use App\Http\Controllers\Api\ProveedorApiController;
use App\Http\Controllers\Api\CategoriaApiController;
use App\Http\Controllers\Api\ProductoApiController;
use App\Http\Controllers\Api\VentaApiController;
use App\Http\Controllers\Api\CompraApiController;


// login
Route::post('/login', [AuthApiController::class, 'login']);


// rutas protegidas
Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRADOR
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:1')->group(function () {

        // usuarios
        Route::apiResource('usuarios', UsuarioApiController::class);

        Route::post(
            'usuarios/{id}/toggle-estado',
            [UsuarioApiController::class, 'toggleEstado']
        )->name('usuarios.toggleEstado');


        // roles
        Route::apiResource('roles', RolesApiController::class);

        Route::post(
            'roles/{id}/toggle-estado',
            [RolesApiController::class, 'toggleEstado']
        )->name('roles.toggleEstado');
    });



    /*
    |--------------------------------------------------------------------------
    | ADMINISTRADOR + COMPRAS
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:1,2')->group(function () {

        // proveedores
        Route::apiResource('proveedores', ProveedorApiController::class);

        Route::post(
            'proveedores/{id}/toggle-estado',
            [ProveedorApiController::class, 'toggleEstado']
        )->name('proveedores.toggleEstado');


        // categorias
        Route::apiResource('categorias', CategoriaApiController::class);

        Route::post(
            'categorias/{id}/toggle-estado',
            [CategoriaApiController::class, 'toggleEstado']
        )->name('categorias.toggleEstado');


        // productos
        Route::apiResource('productos', ProductoApiController::class);

        Route::post(
            'productos/{id}/toggle-estado',
            [ProductoApiController::class, 'toggleEstado']
        )->name('productos.toggleEstado');


        // compras
        Route::apiResource('compras', CompraApiController::class);
    });



    /*
    |--------------------------------------------------------------------------
    | ADMINISTRADOR + CAJERO
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:1,3')->group(function () {

        // ventas
        Route::apiResource('ventas', VentaApiController::class);
    });
});