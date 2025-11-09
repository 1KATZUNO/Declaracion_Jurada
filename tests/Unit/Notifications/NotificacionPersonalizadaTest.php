<?php

namespace Tests\Unit\Notifications;

use App\Notifications\NotificacionPersonalizada;
use PHPUnit\Framework\TestCase;

class NotificacionPersonalizadaTest extends TestCase
{
    public function test_via_devuelve_canales_mail_y_database(): void
    {
        $notificacion = new NotificacionPersonalizada('Mensaje de prueba', 'enviada');

        $channels = $notificacion->via((object) []);

        $this->assertSame(['mail', 'database'], $channels);
    }

    public function test_to_mail_construye_el_correo_con_datos_del_usuario(): void
    {
        $notificacion = new NotificacionPersonalizada('Mensaje importante', 'pendiente');
        $notifiable = new class {
            public string $nombre = 'María';
        };

        $mailMessage = $notificacion->toMail($notifiable);

        $this->assertSame('Nueva notificación del sistema', $mailMessage->subject);
        $this->assertSame('Hola, María:', $mailMessage->greeting);
        $this->assertContains('Mensaje importante', $mailMessage->introLines);
    }

    public function test_to_array_incluye_mensaje_y_estado(): void
    {
        $notificacion = new NotificacionPersonalizada('Recordatorio', 'leída');

        $data = $notificacion->toArray((object) []);

        $this->assertSame([
            'message' => 'Recordatorio',
            'estado'  => 'leída',
        ], $data);
    }

    public function test_estado_por_defecto_es_pendiente(): void
    {
        $notificacion = new NotificacionPersonalizada('Otro mensaje');

        $data = $notificacion->toArray((object) []);

        $this->assertSame('pendiente', $data['estado']);
    }
}
