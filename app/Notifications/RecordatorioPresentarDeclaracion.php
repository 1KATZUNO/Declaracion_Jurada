<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecordatorioPresentarDeclaracion extends Notification
{
    use Queueable;

    protected ?Carbon $fechaLimite;

    public function __construct(?Carbon $fechaLimite = null)
    {
        $this->fechaLimite = $fechaLimite?->copy();
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $nombre = trim(($notifiable->nombre ?? '') . ' ' . ($notifiable->apellido ?? ''));
        $saludo = $nombre !== '' ? "Hola, {$nombre}:" : 'Hola funcionario:';

        $mensajeFecha = $this->fechaLimite
            ? 'Recuerda presentar tu Declaración Jurada antes del ' . $this->fechaLimite->locale('es')->isoFormat('D [de] MMMM [de] YYYY') . '.'
            : 'Recuerda presentar tu Declaración Jurada a la brevedad.';

        return (new MailMessage)
            ->subject('Recordatorio: presentación de Declaración Jurada')
            ->greeting($saludo)
            ->line('El sistema detectó que es necesario que presentes una nueva Declaración Jurada.')
            ->line($mensajeFecha)
            ->action('Presentar Declaración', route('declaraciones.create'))
            ->line('Si ya realizaste el trámite, ignora este mensaje.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Debes presentar una nueva Declaración Jurada.',
            'fecha_limite' => $this->fechaLimite?->toDateString(),
            'action_url' => route('declaraciones.create'),
        ];
    }
}

