<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionPersonalizada extends Notification
{
    use Queueable;

    protected string $mensaje;
    protected string $estado;

    public function __construct(string $mensaje, string $estado = 'pendiente')
    {
        $this->mensaje = $mensaje;
        $this->estado = $estado;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $nombre = $notifiable->nombre ?? $notifiable->nombre_completo ?? 'usuario/a';

        return (new MailMessage)
            ->subject('Nueva notificación del sistema')
            ->greeting('Hola, ' . $nombre . ':')
            ->line($this->mensaje)
            ->line('Este es un aviso enviado automáticamente por la plataforma de Declaración Jurada.');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => $this->mensaje,
            'estado'  => $this->estado,
        ];
    }
}
