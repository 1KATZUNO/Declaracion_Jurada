<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionPersonalizada extends Notification
{
    use Queueable;

    protected string $titulo;
    protected string $mensaje;
    protected string $tipo;
    protected ?int $declaracionId;

    public function __construct(string $titulo, string $mensaje, string $tipo, ?int $declaracionId = null)
    {
        $this->titulo = $titulo;
        $this->mensaje = $mensaje;
        $this->tipo = $tipo;
        $this->declaracionId = $declaracionId;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $nombre = $notifiable->nombre ?? $notifiable->nombre_completo ?? 'usuario/a';

        $mailMessage = (new MailMessage)
            ->subject($this->titulo . ' - Declaraciones Juradas UCR')
            ->greeting('Hola, ' . $nombre . ':')
            ->line($this->mensaje);

        // Agregar acción si hay declaración asociada
        if ($this->declaracionId && $this->tipo !== 'eliminar') {
            $mailMessage->action('Ver Declaración', url("/declaraciones/{$this->declaracionId}"));
        }

        $mailMessage->line('Este es un aviso enviado automáticamente por la plataforma de Declaración Jurada UCR.');

        return $mailMessage;
    }

    public function toArray($notifiable): array
    {
        return [
            'titulo' => $this->titulo,
            'mensaje' => $this->mensaje,
            'tipo' => $this->tipo,
            'id_declaracion' => $this->declaracionId,
        ];
    }
}
