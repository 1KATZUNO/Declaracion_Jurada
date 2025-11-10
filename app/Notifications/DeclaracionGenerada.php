<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Declaracion;

class DeclaracionGenerada extends Notification
{
    use Queueable;

    protected $declaracion;

    public function __construct(Declaracion $declaracion)
    {
        $this->declaracion = $declaracion;
    }

    // ✅ Correo + panel (BD)
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $url = route('declaraciones.show', $this->declaracion->id_declaracion);

        return (new MailMessage)
            ->subject('Declaración jurada generada correctamente')
            ->greeting('Hola, ' . ($notifiable->nombre ?? 'profesor/a') . ':')
            ->line('Tu Declaración Jurada fue creada correctamente en el sistema de la UCR.')
            ->action('Ver Declaración', $url)
            ->line('Si tienes dudas, contacta al administrador del sistema.');
    }

    public function toArray($notifiable)
    {
        return [
            'declaracion_id' => $this->declaracion->id_declaracion,
            'message' => 'Tu Declaración Jurada fue generada correctamente.',
            'url' => route('declaraciones.show', $this->declaracion->id_declaracion),
        ];
    }
}
