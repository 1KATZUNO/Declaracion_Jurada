<?php

namespace Tests\Feature\Console;

use App\Models\Declaracion;
use App\Models\Cargo;
use App\Models\Formulario;
use App\Models\Sede;
use App\Models\UnidadAcademica;
use App\Models\Usuario;
use App\Notifications\RecordatorioPresentarDeclaracion;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EnviarRecordatorioDeclaracionesTest extends TestCase
{
    use RefreshDatabase;

    protected Formulario $formulario;
    protected UnidadAcademica $unidad;
    protected Cargo $cargo;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $sede = Sede::create([
            'nombre' => 'Sede Central',
            'ubicacion' => 'San José',
        ]);

        $this->formulario = Formulario::create([
            'titulo' => 'Declaración Anual',
            'descripcion' => 'Formulario base',
            'fecha_creacion' => Carbon::now()->toDateString(),
        ]);

        $this->cargo = Cargo::create([
            'nombre' => 'Profesor',
            'jornada' => 'Tiempo completo',
            'descripcion' => 'Docencia',
        ]);

        $this->unidad = UnidadAcademica::create([
            'nombre' => 'Escuela de Ingeniería',
            'id_sede' => $sede->id_sede,
        ]);
    }

    public function test_notifica_usuarios_con_declaracion_vencida(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Mario',
            'apellido' => 'Rojas',
            'correo' => 'mario@example.com',
            'contrasena' => 'secret',
            'telefono' => '555-111',
            'rol' => 'funcionario',
            'identificacion' => '123456789',
        ]);

        Declaracion::create([
            'id_usuario' => $usuario->id_usuario,
            'id_formulario' => $this->formulario->id_formulario,
            'id_unidad' => $this->unidad->id_unidad,
            'id_cargo' => $this->cargo->id_cargo,
            'fecha_desde' => Carbon::now()->subMonths(6)->toDateString(),
            'fecha_hasta' => Carbon::now()->subDay()->toDateString(),
            'horas_totales' => 40,
            'fecha_envio' => Carbon::now(),
        ]);

        $this->artisan('declaraciones:recordatorio', ['--dias' => 0])
            ->assertExitCode(0);

        Notification::assertSentTo($usuario, RecordatorioPresentarDeclaracion::class);
    }

    public function test_no_notifica_si_falta_mucho_tiempo(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Lucia',
            'apellido' => 'Vega',
            'correo' => 'lucia@example.com',
            'contrasena' => 'secret',
            'telefono' => '555-222',
            'rol' => 'funcionario',
            'identificacion' => '987654321',
        ]);

        Declaracion::create([
            'id_usuario' => $usuario->id_usuario,
            'id_formulario' => $this->formulario->id_formulario,
            'id_unidad' => $this->unidad->id_unidad,
            'id_cargo' => $this->cargo->id_cargo,
            'fecha_desde' => Carbon::now()->toDateString(),
            'fecha_hasta' => Carbon::now()->addMonths(3)->toDateString(),
            'horas_totales' => 40,
            'fecha_envio' => Carbon::now(),
        ]);

        $this->artisan('declaraciones:recordatorio', ['--dias' => 7])
            ->assertExitCode(0);

        Notification::assertNothingSent();
    }
}

