<?php

namespace Tests\Unit\Notifications;

use App\Notifications\RecordatorioPresentarDeclaracion;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Tests\TestCase;

class RecordatorioPresentarDeclaracionTest extends TestCase
{
    public function test_puede_generar_mensaje_de_correo_con_fecha_limite(): void
    {
        $fecha = Carbon::create(2025, 3, 15);
        $notificacion = new RecordatorioPresentarDeclaracion($fecha);

        $correo = $notificacion->toMail((object) ['nombre' => 'Ana', 'apellido' => 'Soto']);

        $this->assertInstanceOf(MailMessage::class, $correo);
        $this->assertSame('Recordatorio: presentación de Declaración Jurada', $correo->subject);
        $this->assertStringContainsString('Ana Soto', $correo->greeting);
        $lineasIntroductorias = $correo->introLines;
        $this->assertStringContainsString('15 de marzo de 2025', end($lineasIntroductorias));
    }

    public function test_los_datos_del_payload_estan_disponibles_para_la_base_de_datos(): void
    {
        $fecha = Carbon::parse('2025-04-01');
        $notificacion = new RecordatorioPresentarDeclaracion($fecha);

        $datos = $notificacion->toArray((object) []);

        $this->assertSame('Debes presentar una nueva Declaración Jurada.', $datos['message']);
        $this->assertSame('2025-04-01', $datos['fecha_limite']);
        $this->assertNotEmpty($datos['action_url']);
    }

    public function test_los_canales_incluyen_correo_y_base_de_datos(): void
    {
        $notificacion = new RecordatorioPresentarDeclaracion();

        $this->assertEquals(['mail', 'database'], $notificacion->via((object) []));
    }
}
