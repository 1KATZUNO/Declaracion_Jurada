<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Declaracion, Documento, Usuario, Formulario, UnidadAcademica, Cargo, Sede};

class DeclaracionDocumentosTest extends TestCase
{
    use RefreshDatabase;

    public function test_declaracion_has_many_documentos_and_count_is_correct()
    {
        // Crear entidades mínimas requeridas por las migraciones
        $sede = Sede::create(['nombre' => 'Sede X', 'ubicacion' => 'Ubicacion X']);
        $form = Formulario::create(['titulo' => 'Form X', 'descripcion' => null, 'fecha_creacion' => now()]);
        $unidad = UnidadAcademica::create([
            'nombre' => 'Unidad X',
            'id_sede' => $sede->id_sede,
            'estado' => 'ACTIVA'
        ]);
        $cargo = Cargo::create(['nombre' => 'Cargo X', 'jornada' => 'full', 'descripcion' => '']);

        $usuario = Usuario::factory()->create();

        $declaracion = Declaracion::create([
            'id_usuario' => $usuario->id_usuario,
            'id_formulario' => $form->id_formulario,
            'id_unidad' => $unidad->id_unidad,
            'id_cargo' => $cargo->id_cargo,
            'fecha_desde' => now()->subDays(5)->toDateString(),
            'fecha_hasta' => now()->toDateString(),
            'horas_totales' => 12,
            'fecha_envio' => now()
        ]);

        // Crear varios documentos asociados
        Documento::create([
            'id_declaracion' => $declaracion->id_declaracion,
            'archivo' => 'docs/a.pdf',
            'formato' => 'PDF',
            'fecha_generacion' => now()
        ]);

        Documento::create([
            'id_declaracion' => $declaracion->id_declaracion,
            'archivo' => 'docs/b.pdf',
            'formato' => 'PDF',
            'fecha_generacion' => now()
        ]);

        Documento::create([
            'id_declaracion' => $declaracion->id_declaracion,
            'archivo' => 'docs/c.pdf',
            'formato' => 'PDF',
            'fecha_generacion' => now()
        ]);

        // Refrescar relación y comprobar
        $declaracion->load('documentos');

        $this->assertCount(3, $declaracion->documentos);
        $this->assertEquals(3, $declaracion->documentos()->count());
        $this->assertTrue($declaracion->documentos->first() instanceof Documento);
    }
}