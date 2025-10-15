<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UsuarioController,
    SedeController,
    UnidadAcademicaController,
    CargoController,
    FormularioController,
    DeclaracionController,
    HorarioController,
    DocumentoController,
    NotificacionController,
    DeclaracionExportController
};

// Dashboard simple
Route::get('/', [DeclaracionController::class, 'index'])->name('home');

// CRUDs
Route::resource('usuarios', UsuarioController::class);
Route::resource('sedes', SedeController::class);
Route::resource('unidades', UnidadAcademicaController::class);
Route::resource('cargos', CargoController::class);
Route::resource('formularios', FormularioController::class);
Route::resource('declaraciones', DeclaracionController::class);
Route::resource('horarios', HorarioController::class);
Route::resource('documentos', DocumentoController::class)->only(['index','show','destroy']);
Route::resource('notificaciones', NotificacionController::class);

// Exportación Excel desde plantilla
Route::get('/declaraciones/{id}/exportar', [DeclaracionExportController::class, 'exportar'])
    ->name('declaraciones.exportar');

