<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Documento, Declaracion, Usuario, Formulario, UnidadAcademica, Cargo, Sede};

class DocumentoSimpleTest extends TestCase
{
    use RefreshDatabase;

    public function test_documento_belongs_to_declaracion()
    {
        // Crear datos mínimos requeridos por las migraciones
        $sede = Sede::create([
            'nombre' => 'Sede Test',
            'ubicacion' => 'Ubicación X'
        ]);

        $form = Formulario::create([
            'titulo' => 'Form Test',
            'descripcion' => null,
            'fecha_creacion' => now()
        ]);

        $unidad = UnidadAcademica::create([
            'nombre' => 'Unidad Test',
            'id_sede' => $sede->id_sede,
            'estado' => 'ACTIVA'
        ]);

        $cargo = Cargo::create([
            'nombre' => 'Cargo Test',
            'jornada' => 'full',
            'descripcion' => ''
        ]);

        // Usuario (usa la factory que tienes en el repo)
        $usuario = Usuario::factory()->create();

        // Declaración vinculada al usuario
        $declaracion = Declaracion::create([
            'id_usuario' => $usuario->id_usuario,
            'id_formulario' => $form->id_formulario,
            'id_unidad' => $unidad->id_unidad,
            'id_cargo' => $cargo->id_cargo,
            'fecha_desde' => now()->subDays(10)->toDateString(),
            'fecha_hasta' => now()->toDateString(),
            'horas_totales' => 8,
            'fecha_envio' => now()
        ]);

        // Documento asociado
        $doc = Documento::create([
            'id_declaracion' => $declaracion->id_declaracion,
            'archivo' => 'docs/test.pdf',
            'formato' => 'PDF',
            'fecha_generacion' => now()
        ]);

        // Aserciones: relación y claves
        $this->assertInstanceOf(Declaracion::class, $doc->declaracion);
        $this->assertEquals($declaracion->id_declaracion, $doc->declaracion->id_declaracion);
        $this->assertEquals($usuario->id_usuario, $doc->declaracion->id_usuario);
    }
}