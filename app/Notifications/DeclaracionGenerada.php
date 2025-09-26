<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DeclaracionGenerada extends Notification
{
    use Queueable;

    protected $declaracion;

    public function __construct($declaracion)
    {
        $this->declaracion = $declaracion;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // email + tabla notifications
    }

    public function toMail($notifiable)
    {
        $url = config('app.url') . '/declaraciones/' . $this->declaracion->id;

        return (new MailMessage)
            ->subject('Declaración jurada generada')
            ->line('Tu declaración jurada fue creada correctamente.')
            ->action('Ver declaración', $url)
            ->line('Si tienes dudas, contacta al administrador.');
    }

    public function toArray($notifiable)
    {
        return [
            'declaracion_id' => $this->declaracion->id,
            'message' => 'Declaración generada correctamente'
        ];
    }
}
