<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeclaracionController;

// Registro / Login / Logout
Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
Route::post('/logout', [AuthController::class,'logout'])->middleware('auth:sanctum');

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Obtener usuario autenticado
    Route::get('/user', function(Request $r){ return $r->user(); });

    // CRUD de declaraciones
    Route::apiResource('declaraciones', DeclaracionController::class);

    // Exportar declaración a Excel
    Route::post('declaraciones/{declaracion}/export', [DeclaracionController::class,'export']);

    Route::get('/notificaciones', function(Request $req) {
    return $req->user()->notifications;
})->middleware('auth:sanctum');
  Route::apiResource('formularios', FormularioController::class);
});

