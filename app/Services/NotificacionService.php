<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\Usuario;
use App\Models\Declaracion;
use App\Notifications\DeclaracionGenerada;
use App\Notifications\NotificacionPersonalizada;
use Carbon\Carbon;

class NotificacionService
{
    /**
     * Crear y enviar notificación
     */
    public function crearNotificacion($usuarioId, $titulo, $mensaje, $tipo, $declaracionId = null)
    {
        // Crear notificación en base de datos
        $notificacion = Notificacion::create([
            'id_usuario' => $usuarioId,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => $tipo,
            'id_declaracion' => $declaracionId,
            'fecha_envio' => now(),
            'estado' => 'enviada',
            'leida' => false
        ]);

        // 📧 Enviar por email (solo si está habilitado)
        $usuario = Usuario::find($usuarioId);
        if ($usuario && config('mail.mailer') !== 'log') {
            try {
                // Crear notificación de Laravel SOLO para email, sin persistir en BD
                $mailNotification = new NotificacionPersonalizada(
                    $titulo,
                    $mensaje,
                    $tipo,
                    $declaracionId
                );
                
                // Enviar solo por email, no guardar en Laravel notifications
                \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($usuario, $titulo, $mensaje) {
                    $message->to($usuario->correo)
                        ->subject($titulo)
                        ->html("<h3>{$titulo}</h3><p>{$mensaje}</p>");
                });
            } catch (\Exception $e) {
                \Log::error('Error enviando email de notificación: ' . $e->getMessage());
                // Continuar aunque falle el email, la notificación en BD ya se guardó
            }
        }

        return $notificacion;
    }

    /**
     * Notificación para crear declaración
     */
    public function notificarCrearDeclaracion($declaracion)
    {
        return $this->crearNotificacion(
            $declaracion->id_usuario,
            'Declaración Jurada Creada',
            "Se ha creado exitosamente una nueva declaración jurada para el período {$declaracion->fecha_desde} - {$declaracion->fecha_hasta}.",
            Notificacion::TIPO_CREAR,
            $declaracion->id_declaracion
        );
    }

    /**
     * Notificación para editar declaración
     */
    public function notificarEditarDeclaracion($declaracion)
    {
        return $this->crearNotificacion(
            $declaracion->id_usuario,
            'Declaración Jurada Actualizada',
            "Se ha actualizado su declaración jurada para el período {$declaracion->fecha_desde} - {$declaracion->fecha_hasta}.",
            Notificacion::TIPO_EDITAR,
            $declaracion->id_declaracion
        );
    }

    /**
     * Notificación para eliminar declaración
     */
    public function notificarEliminarDeclaracion($declaracion)
    {
        return $this->crearNotificacion(
            $declaracion->id_usuario,
            'Declaración Jurada Eliminada',
            "Se ha eliminado su declaración jurada para el período {$declaracion->fecha_desde} - {$declaracion->fecha_hasta}.",
            Notificacion::TIPO_ELIMINAR,
            $declaracion->id_declaracion
        );
    }

    /**
     * Notificación para exportar declaración
     */
    public function notificarExportarDeclaracion($declaracion, $formato)
    {
        return $this->crearNotificacion(
            $declaracion->id_usuario,
            'Declaración Exportada',
            "Se ha generado exitosamente la exportación en formato {$formato} de su declaración jurada.",
            Notificacion::TIPO_EXPORTAR,
            $declaracion->id_declaracion
        );
    }

    /**
     * Notificación para declaraciones próximas a vencer
     */
    public function notificarVencimientoProximo($usuario, $diasRestantes)
    {
        return $this->crearNotificacion(
            $usuario->id_usuario,
            'Recordatorio: Declaración Próxima a Vencer',
            "Le recordamos que tiene {$diasRestantes} días para presentar su declaración jurada de horarios.",
            Notificacion::TIPO_VENCIMIENTO
        );
    }

    /**
     * Notificación específica para vencimiento de declaración con fecha exacta
     */
    public function notificarVencimientoDeclaracion($usuario, $declaracion, $diasRestantes)
    {
        $fechaVencimiento = Carbon::parse($declaracion->fecha_hasta)->format('d/m/Y');
        
        $titulo = "⚠️ Declaración Jurada próxima a vencer";
        $mensaje = "Estimado/a {$usuario->nombre} {$usuario->apellido}, " .
                   "su Declaración Jurada del período {$declaracion->fecha_desde} al {$declaracion->fecha_hasta} " .
                   "vencerá en {$diasRestantes} días (el {$fechaVencimiento}). " .
                   "Por favor, revise que toda la información esté actualizada antes del vencimiento.";

        return $this->crearNotificacion(
            $usuario->id_usuario,
            $titulo,
            $mensaje,
            Notificacion::TIPO_VENCIMIENTO,
            $declaracion->id_declaracion
        );
    }

    /**
     * Obtener notificaciones no leídas de un usuario
     */
    public function obtenerNoLeidasPorUsuario($usuarioId)
    {
        return Notificacion::where('id_usuario', $usuarioId)
            ->noLeidas()
            ->orderBy('fecha_envio', 'desc')
            ->get();
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function marcarTodasComoLeidas($usuarioId)
    {
        return Notificacion::where('id_usuario', $usuarioId)
            ->noLeidas()
            ->update(['leida' => true]);
    }

    /**
     * Obtener conteo de notificaciones no leídas
     */
    public function contarNoLeidas($usuarioId)
    {
        return Notificacion::where('id_usuario', $usuarioId)
            ->noLeidas()
            ->count();
    }
}