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


Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRADOR
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:1')->group(function () {

        // Usuarios
        Route::apiResource('usuarios', UsuarioApiController::class);
        Route::post(
            'usuarios/{id}/toggle-estado',
            [UsuarioApiController::class, 'toggleEstado']
        );

        // Roles
        Route::apiResource('roles', RolesApiController::class);
        Route::post(
            'roles/{id}/toggle-estado',
            [RolesApiController::class, 'toggleEstado']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | INVENTARIO
    | ADMIN + COMPRAS
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:1,2')->group(function () {

        // Proveedores
        Route::apiResource('proveedores', ProveedorApiController::class);
        Route::post(
            'proveedores/{id}/toggle-estado',
            [ProveedorApiController::class, 'toggleEstado']
        );

        // Categorías
        Route::apiResource('categorias', CategoriaApiController::class);
        Route::post(
            'categorias/{id}/toggle-estado',
            [CategoriaApiController::class, 'toggleEstado']
        );


        // Compras
        Route::apiResource('compras', CompraApiController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | VENTAS
    | ADMIN + CAJERO
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:1,3')->group(function () {

        Route::apiResource('ventas', VentaApiController::class);
    });

    // PRODUCTOS

    Route::middleware('role:1,2,3')->group(function () {

        // Productos
        Route::apiResource('productos', ProductoApiController::class);
        Route::post(
            'productos/{id}/toggle-estado',
            [ProductoApiController::class, 'toggleEstado']
        );


    });
});