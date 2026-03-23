<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsuarioApiController;
use App\Http\Controllers\Api\AuthApiController;



Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('usuarios', UsuarioApiController::class);
    Route::post('usuarios/{id}/toggle-estado', [UsuarioApiController::class, 'toggleEstado'])
            ->name('usuarios.toggleEstado');
});
