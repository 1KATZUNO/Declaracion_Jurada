<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeclaracionController;

// Auth
Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
Route::post('/logout', [AuthController::class,'logout'])->middleware('auth:sanctum');

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function(Request $r){ return $r->user(); });

    // Declaraciones (CRUD + export)
    Route::apiResource('declaraciones', DeclaracionController::class);
    Route::post('declaraciones/{declaracion}/export', [DeclaracionController::class,'export']);
});
