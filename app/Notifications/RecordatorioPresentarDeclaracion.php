<?php

namespace App\Notifications;

use App\Models\Declaracion;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecordatorioPresentarDeclaracion extends Notification
{
    use Queueable;

    protected ?Carbon $fechaLimite;
    protected ?Declaracion $declaracion;

    /**
     * Crear una nueva instancia de notificación.
     *
     * @param  \Carbon\Carbon|null  $fechaLimite
     * @param  \App\Models\Declaracion|null  $declaracion
     */
    public function __construct(?Carbon $fechaLimite = null, ?Declaracion $declaracion = null)
    {
        $this->fechaLimite = $fechaLimite?->copy();
        $this->declaracion = $declaracion;
    }

    /**
     * Canales por los que se enviará la notificación.
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Formato del correo electrónico.
     */
    public function toMail($notifiable)
    {
        $nombre = trim(($notifiable->nombre ?? '') . ' ' . ($notifiable->apellido ?? ''));
        $saludo = $nombre !== '' ? "Hola, {$nombre}:" : 'Hola funcionario:';

        $mensajeFecha = $this->fechaLimite
            ? 'Recuerda presentar tu Declaración Jurada antes del ' .
              $this->fechaLimite->locale('es')->isoFormat('D [de] MMMM [de] YYYY') . '.'
            : 'Recuerda presentar tu Declaración Jurada a la brevedad.';

        $url = $this->declaracion
            ? route('declaraciones.show', $this->declaracion->id_declaracion)
            : route('declaraciones.create');

        return (new MailMessage)
            ->subject('Recordatorio: presentación de Declaración Jurada')
            ->greeting($saludo)
            ->line('El sistema detectó que tienes una Declaración Jurada próxima a vencer.')
            ->line($mensajeFecha)
            ->action('Ver Declaración', $url)
            ->line('Si ya realizaste el trámite, ignora este mensaje.');
    }

    /**
     * Representación de la notificación en base de datos.
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'Debes presentar una nueva Declaración Jurada.',
            'fecha_limite' => $this->fechaLimite?->toDateString(),
            'declaracion_id' => $this->declaracion?->id_declaracion,
            'action_url' => $this->declaracion
                ? route('declaraciones.show', $this->declaracion->id_declaracion)
                : route('declaraciones.create'),
        ];
    }
}
