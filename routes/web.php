<?php

use App\Http\Middleware\VerificarRol;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UsuarioController,
    SedeController,
    UnidadAcademicaController,
    CargoController,
    FormularioController,
    JornadaController,
    DeclaracionController,
    HorarioController,
    DocumentoController,
    NotificacionController,
    DeclaracionExportController,
    LoginController
};

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Home
Route::get('/', [LoginController::class, 'showLoginForm'])->name('home');

// CRUDs
Route::middleware([VerificarRol::class . ':admin'])->group(function () {
    Route::resource('usuarios', UsuarioController::class);
});
Route::resource('sedes', SedeController::class);
Route::resource('unidades', UnidadAcademicaController::class);
Route::resource('cargos', CargoController::class);
Route::resource('formularios', FormularioController::class);
Route::resource('declaraciones', DeclaracionController::class);
Route::resource('jornadas', JornadaController::class)->except(['show']);
Route::resource('horarios', HorarioController::class);
Route::resource('documentos', DocumentoController::class)->only(['index','show','destroy']);
Route::resource('notificaciones', NotificacionController::class);

// Exportación Excel
Route::get('/declaraciones/{id}/exportar', [DeclaracionExportController::class, 'exportar'])
     ->name('declaraciones.exportar');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Catálogo JSON para selects
Route::get('/catalogos/unidades', [UnidadAcademicaController::class, 'catalogo'])
     ->name('catalogos.unidades');
