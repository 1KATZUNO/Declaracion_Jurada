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
Route::resource('documentos', DocumentoController::class)->only(['index','show','destroy']);
Route::resource('notificaciones', NotificacionController::class);
// Ruta extra para "Marcar todas como leídas en notificacion
Route::post('/notificaciones/marcar-todas', [NotificacionController::class, 'marcarTodasLeidas'])
    ->name('notificaciones.marcar-todas');


// Exportación Excel
Route::get('/declaraciones/{id}/exportar', [DeclaracionExportController::class, 'exportar'])
     ->name('declaraciones.exportar');

// Exportación PDF
Route::get('/declaraciones/{id}/pdf', [DeclaracionExportController::class, 'exportarPdf'])
     ->name('declaraciones.pdf');

Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Formulario de cambio de contraseña
Route::get('/change-password', [LoginController::class, 'showChangePasswordForm'])->name('password.form');
Route::post('/change-password', [LoginController::class, 'changePassword'])->name('password.change');
Route::post('/perfil', [UsuarioController::class, 'updateProfile'])->name('perfil.update');
Route::get('/catalogos/unidades', [UnidadAcademicaController::class, 'catalogo'])
    ->name('unidades.catalogo');

