<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí registras las rutas de tu API. Estas rutas están protegidas por el
| middleware "api". 
|
*/

// Rutas públicas (login, register)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // Aquí puedes ir agregando más endpoints de tu aplicación protegidos
    // Ejemplo:
    // Route::get('/declaraciones', [DeclaracionController::class, 'index']);
});




