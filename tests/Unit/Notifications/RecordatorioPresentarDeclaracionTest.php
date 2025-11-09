<?php

namespace Tests\Unit\Notifications;

use App\Notifications\RecordatorioPresentarDeclaracion;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use PHPUnit\Framework\TestCase;

class RecordatorioPresentarDeclaracionTest extends TestCase
{
    public function test_can_generate_mail_message_with_fecha_limite(): void
    {
        $fecha = Carbon::create(2025, 3, 15);
        $notification = new RecordatorioPresentarDeclaracion($fecha);

        $mail = $notification->toMail((object) ['nombre' => 'Ana', 'apellido' => 'Soto']);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertSame('Recordatorio: presentación de Declaración Jurada', $mail->subject);
        $this->assertStringContainsString('Ana Soto', $mail->greeting);
        $introLines = $mail->introLines;
        $this->assertStringContainsString('15 de marzo de 2025', end($introLines));
    }

    public function test_payload_data_is_available_for_database(): void
    {
        $fecha = Carbon::parse('2025-04-01');
        $notification = new RecordatorioPresentarDeclaracion($fecha);

        $payload = $notification->toArray((object) []);

        $this->assertSame('Debes presentar una nueva Declaración Jurada.', $payload['message']);
        $this->assertSame('2025-04-01', $payload['fecha_limite']);
        $this->assertNotEmpty($payload['action_url']);
    }

    public function test_channels_include_mail_and_database(): void
    {
        $notification = new RecordatorioPresentarDeclaracion();

        $this->assertEquals(['mail', 'database'], $notification->via((object) []));
    }
}

