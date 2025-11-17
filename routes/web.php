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
    LoginController,
    ComentarioController,
    ComentarioRespuestaController,
    ActividadLogController,
};


// ======================
// 🔐 AUTH ROUTES
// ======================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 🔸 Home redirige al login
Route::get('/', [LoginController::class, 'showLoginForm'])->name('home');


// ======================
// 🧑‍💼 CRUDs PRINCIPALES
// ======================
Route::middleware([VerificarRol::class . ':admin'])->group(function () {
    Route::resource('usuarios', UsuarioController::class);
});

Route::resource('sedes', SedeController::class);
Route::resource('unidades', UnidadAcademicaController::class);
Route::resource('cargos', CargoController::class);
Route::resource('formularios', FormularioController::class);
Route::resource('declaraciones', DeclaracionController::class);
Route::resource('jornadas', JornadaController::class)->except(['show']);


// ======================
// 📄 DOCUMENTOS (YA LISTO)
// ======================
// index, show y destroy solamente
Route::resource('documentos', DocumentoController::class)
    ->only(['index', 'show', 'destroy']);

// Ruta para descargar documentos
Route::get('/documentos/{id}/download', [DocumentoController::class, 'download'])
    ->name('documentos.download');


// ======================
// 🔔 NOTIFICACIONES
// ======================
Route::resource('notificaciones', NotificacionController::class);

// Marcar todas como leídas
Route::post('/notificaciones/marcar-todas', 
    [NotificacionController::class, 'marcarTodasLeidas']
)->name('notificaciones.marcar-todas');

// Notificaciones no leídas vía AJAX
Route::get('/notificaciones-unread', 
    [NotificacionController::class, 'getUnread']
)->name('notificaciones.unread');


// ======================
// 📤 EXPORTACIONES
// ======================
Route::get('/declaraciones/{id}/exportar', 
    [DeclaracionExportController::class, 'exportar']
)->name('declaraciones.exportar');

Route::get('/declaraciones/{id}/pdf', 
    [DeclaracionExportController::class, 'exportarPdf']
)->name('declaraciones.pdf');


// ======================
// 🔗 API INTERNAS
// ======================
Route::get('/api/unidades-por-sede/{id_sede}', 
    [DeclaracionController::class, 'getUnidadesPorSede']
)->name('api.unidades-por-sede');


// ======================
// 🔑 LOGIN extra
// ======================
Route::post('/login', [LoginController::class, 'login'])
    ->name('login.submit');


// ======================
// 🔐 CAMBIO DE CONTRASEÑA
// ======================
Route::get('/change-password', 
    [LoginController::class, 'showChangePasswordForm']
)->name('password.form');

Route::post('/change-password', 
    [LoginController::class, 'changePassword']
)->name('password.change');

Route::post('/perfil', 
    [UsuarioController::class, 'updateProfile']
)->name('perfil.update');


// ======================
// 📚 CATÁLOGOS
// ======================
Route::get('/catalogos/unidades', 
    [UnidadAcademicaController::class, 'catalogo']
)->name('unidades.catalogo');


// ======================
// 💬 COMENTARIOS FUNCIONARIO
// ======================
Route::resource('comentarios', ComentarioController::class);


// ======================
// 🛠 ADMIN COMENTARIOS
// ======================
// Admin: ver todos + responder + cerrar/reabrir
Route::get('admin/comentarios', [ComentarioController::class, 'adminIndex'])
    ->name('admin.comentarios.index');

Route::post('admin/comentarios/{comentario}/respuestas', [ComentarioRespuestaController::class, 'store'])
    ->name('admin.comentarios.respuestas.store');

Route::put('admin/respuestas/{respuesta}', [ComentarioRespuestaController::class, 'update'])
    ->name('admin.respuestas.update');

Route::delete('admin/respuestas/{respuesta}', [ComentarioRespuestaController::class, 'destroy'])
    ->name('admin.respuestas.destroy');

Route::patch('admin/comentarios/{comentario}/estado', [ComentarioController::class, 'cambiarEstado'])
    ->name('admin.comentarios.estado');


// ======================
// 📊 LOGS DE ACTIVIDAD
// ======================
Route::middleware([VerificarRol::class . ':admin'])->group(function () {
    Route::get('/actividad-logs', [ActividadLogController::class, 'index'])
        ->name('actividad-logs.index');
    Route::get('/actividad-logs/{id}', [ActividadLogController::class, 'show'])
        ->name('actividad-logs.show');
    Route::delete('/actividad-logs/{id}', [ActividadLogController::class, 'destroy'])
        ->name('actividad-logs.destroy');
    Route::post('/actividad-logs/limpiar', [ActividadLogController::class, 'limpiar'])
        ->name('actividad-logs.limpiar');
});

