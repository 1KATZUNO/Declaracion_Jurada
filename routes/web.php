<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeclaracionController;
use App\Http\Controllers\FormularioController;

// Grupo de rutas API
Route::middleware('api')->group(function () {

    // Registro / Login / Logout (sin protección)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Rutas protegidas con Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function(Request $request){
            return $request->user();
        });

        // CRUD declaraciones
        Route::resource('declaraciones', DeclaracionController::class);
        Route::post('declaraciones/{declaracion}/export', [DeclaracionController::class, 'export']);

        // Notificaciones
        Route::get('/notificaciones', function(Request $request){
            return $request->user()->notifications;
        });

        // CRUD formularios
        Route::resource('formularios', FormularioController::class);
    });
});

