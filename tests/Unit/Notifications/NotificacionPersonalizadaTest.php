<?php

namespace Tests\Unit\Notifications;

use App\Notifications\NotificacionPersonalizada;
use PHPUnit\Framework\TestCase;

class NotificacionPersonalizadaTest extends TestCase
{
    public function test_via_devuelve_canales_mail_y_database(): void
    {
        $notificacion = new NotificacionPersonalizada(
            'Título de Prueba',
            'Mensaje de prueba',
            'crear'
        );

        $channels = $notificacion->via((object) []);

        $this->assertSame(['mail', 'database'], $channels);
    }

    public function test_to_mail_construye_el_correo_con_datos_del_usuario(): void
    {
        $notificacion = new NotificacionPersonalizada(
            'Notificación Importante',
            'Mensaje importante',
            'editar'
        );
        $notifiable = new class {
            public string $nombre = 'María';
        };

        $mailMessage = $notificacion->toMail($notifiable);

        // El subject incluye el sufijo " - Declaraciones Juradas UCR"
        $this->assertSame('Notificación Importante - Declaraciones Juradas UCR', $mailMessage->subject);
        $this->assertSame('Hola, María:', $mailMessage->greeting);
        $this->assertContains('Mensaje importante', $mailMessage->introLines);
    }

    public function test_to_array_incluye_mensaje_y_estado(): void
    {
        $notificacion = new NotificacionPersonalizada(
            'Recordatorio',
            'Contenido del recordatorio',
            'vencimiento'
        );

        $data = $notificacion->toArray((object) []);

        // Verificar que las keys sean las correctas según la implementación
        $this->assertArrayHasKey('titulo', $data);
        $this->assertArrayHasKey('mensaje', $data);
        $this->assertArrayHasKey('tipo', $data);
        $this->assertArrayHasKey('id_declaracion', $data);
        $this->assertSame('Recordatorio', $data['titulo']);
        $this->assertSame('Contenido del recordatorio', $data['mensaje']);
        $this->assertSame('vencimiento', $data['tipo']);
    }

    public function test_constructor_acepta_declaracion_id_opcional(): void
    {
        $notificacion = new NotificacionPersonalizada(
            'Test',
            'Mensaje de test',
            'crear',
            123
        );

        $data = $notificacion->toArray((object) []);

        $this->assertArrayHasKey('id_declaracion', $data);
        $this->assertSame(123, $data['id_declaracion']);
    }
}
