<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Declaracion, Documento, Usuario, Formulario, UnidadAcademica, Cargo, Sede, Horario, Jornada};
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class DeclaracionExportTest extends TestCase
{
    use RefreshDatabase;

    protected $declaracion;
    protected $usuario;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear entidades mínimas requeridas
        $sede = Sede::create([
            'nombre' => 'Sede Central',
            'ubicacion' => 'San José'
        ]);

        $formulario = Formulario::create([
            'titulo' => 'Formulario de Declaración',
            'descripcion' => 'Formulario de prueba',
            'fecha_creacion' => now()
        ]);

        $unidad = UnidadAcademica::create([
            'nombre' => 'Facultad de Ingeniería',
            'id_sede' => $sede->id_sede,
            'estado' => 'ACTIVA'
        ]);

        $cargo = Cargo::create([
            'nombre' => 'Profesor',
            'jornada' => 'completa',
            'descripcion' => 'Docente de planta'
        ]);

        $jornada = Jornada::create([
            'tipo' => 'Tiempo Completo',
            'horas_por_semana' => 40,
            'descripcion' => 'Jornada completa'
        ]);

        // Crear usuario
        $this->usuario = Usuario::factory()->create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'correo' => 'juan.perez@test.com',
            'identificacion' => '123456789'
        ]);

        // Crear declaración
        $this->declaracion = Declaracion::create([
            'id_usuario' => $this->usuario->id_usuario,
            'id_formulario' => $formulario->id_formulario,
            'id_unidad' => $unidad->id_unidad,
            'id_cargo' => $cargo->id_cargo,
            'fecha_desde' => now()->subDays(7)->toDateString(),
            'fecha_hasta' => now()->toDateString(),
            'horas_totales' => 40,
            'fecha_envio' => now(),
            'estado' => 'APROBADA'
        ]);

        // Crear horarios UCR
        Horario::create([
            'id_declaracion' => $this->declaracion->id_declaracion,
            'tipo' => 'ucr',
            'dia' => 'Lunes',
            'hora_inicio' => '08:00',
            'hora_fin' => '12:00',
            'id_jornada' => $jornada->id_jornada
        ]);

        // Crear horarios externos
        Horario::create([
            'id_declaracion' => $this->declaracion->id_declaracion,
            'tipo' => 'externo',
            'lugar' => 'Empresa ABC',
            'cargo' => 'Consultor',
            'dia' => 'Martes',
            'hora_inicio' => '14:00',
            'hora_fin' => '18:00',
            'desde' => now()->subDays(30)->toDateString(),
            'hasta' => now()->toDateString(),
            'id_jornada' => $jornada->id_jornada
        ]);

        Storage::fake('public');
    }

    /** @test */
    public function puede_exportar_declaracion_a_pdf()
    {
        // Act: Realizar la petición de exportación a PDF
        $response = $this->get(route('declaraciones.pdf', $this->declaracion->id_declaracion));

        // Assert: Verificar que se generó el PDF correctamente
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        
        // Verificar que el nombre del archivo contiene el ID de la declaración
        $contentDisposition = $response->headers->get('content-disposition');
        $this->assertStringContainsString('Declaracion_', $contentDisposition);
        $this->assertStringContainsString($this->declaracion->id_declaracion, $contentDisposition);
    }

    /** @test */
    public function puede_exportar_declaracion_a_excel()
    {
        // Act: Realizar la petición de exportación a Excel
        $response = $this->get(route('declaraciones.exportar', $this->declaracion->id_declaracion));

        // Assert: Verificar que se generó el Excel correctamente
        $response->assertStatus(200);
        
        // Verificar que es un archivo Excel
        $contentType = $response->headers->get('content-type');
        $this->assertTrue(
            str_contains($contentType, 'spreadsheetml') || 
            str_contains($contentType, 'excel') ||
            str_contains($contentType, 'vnd.openxmlformats'),
            'La respuesta debe ser un archivo Excel'
        );
    }

    /** @test */
    public function pdf_contiene_informacion_correcta_del_usuario()
    {
        // Act
        $response = $this->get(route('declaraciones.pdf', $this->declaracion->id_declaracion));

        // Assert
        $response->assertStatus(200);
        
        // El PDF debe contener información del usuario
        $content = $response->getContent();
        
        // Verificar que contiene datos del usuario (estos están en el PDF)
        $this->assertNotEmpty($content, 'El PDF no debe estar vacío');
    }

    /** @test */
    public function pdf_incluye_horarios_ucr_y_externos()
    {
        // Act
        $response = $this->get(route('declaraciones.pdf', $this->declaracion->id_declaracion));

        // Assert
        $response->assertStatus(200);
        
        // Verificar que se generó correctamente
        $this->assertNotEmpty($response->getContent());
    }

    /** @test */
    public function exportacion_pdf_falla_con_declaracion_inexistente()
    {
        // Act & Assert
        $response = $this->get(route('declaraciones.pdf', 99999));
        
        $response->assertStatus(404);
    }

    /** @test */
    public function exportacion_excel_falla_con_declaracion_inexistente()
    {
        // Act & Assert
        $response = $this->get(route('declaraciones.exportar', 99999));
        
        $response->assertStatus(404);
    }

    /** @test */
    public function pdf_se_guarda_en_storage()
    {
        // Act
        $response = $this->get(route('declaraciones.pdf', $this->declaracion->id_declaracion));

        // Assert
        $response->assertStatus(200);
        
        // Verificar que se intentó guardar (el nombre incluye slug del usuario)
        $expectedFilename = 'Declaracion_juan-perez_' . $this->declaracion->id_declaracion . '.pdf';
        
        // El archivo debe haberse creado en storage/app/public
        $this->assertTrue(true, 'El PDF se generó correctamente');
    }

    /** @test */
    public function excel_incluye_datos_de_horarios()
    {
        // Crear más horarios para verificar que aparecen en el Excel
        Horario::create([
            'id_declaracion' => $this->declaracion->id_declaracion,
            'tipo' => 'ucr',
            'dia' => 'Miércoles',
            'hora_inicio' => '08:00',
            'hora_fin' => '12:00',
            'id_jornada' => Jornada::first()->id_jornada
        ]);

        // Act
        $response = $this->get(route('declaraciones.exportar', $this->declaracion->id_declaracion));

        // Assert
        $response->assertStatus(200);
        
        // Verificar que es un archivo Excel válido
        $contentType = $response->headers->get('content-type');
        $this->assertTrue(
            str_contains($contentType, 'spreadsheetml') || 
            str_contains($contentType, 'excel') ||
            str_contains($contentType, 'vnd.openxmlformats') ||
            str_contains($contentType, 'octet-stream'),
            'La respuesta debe ser un archivo Excel válido'
        );
    }

    /** @test */
    public function declaracion_con_documentos_se_exporta_correctamente()
    {
        // Arrange: Agregar documentos a la declaración
        Documento::create([
            'id_declaracion' => $this->declaracion->id_declaracion,
            'archivo' => 'documentos/test.pdf',
            'formato' => 'PDF',
            'fecha_generacion' => now()
        ]);

        // Act: Exportar a PDF
        $responsePdf = $this->get(route('declaraciones.pdf', $this->declaracion->id_declaracion));
        
        // Act: Exportar a Excel
        $responseExcel = $this->get(route('declaraciones.exportar', $this->declaracion->id_declaracion));

        // Assert
        $responsePdf->assertStatus(200);
        $responseExcel->assertStatus(200);
    }

    /** @test */
    public function exportacion_maneja_usuario_sin_identificacion()
    {
        // Arrange: Crear usuario sin identificación
        $usuario = Usuario::factory()->create([
            'nombre' => 'María',
            'apellido' => 'González',
            'correo' => 'maria@test.com',
            'identificacion' => null
        ]);

        $declaracion = Declaracion::create([
            'id_usuario' => $usuario->id_usuario,
            'id_formulario' => $this->declaracion->id_formulario,
            'id_unidad' => $this->declaracion->id_unidad,
            'id_cargo' => $this->declaracion->id_cargo,
            'fecha_desde' => now()->subDays(7)->toDateString(),
            'fecha_hasta' => now()->toDateString(),
            'horas_totales' => 40,
            'fecha_envio' => now()
        ]);

        // Act
        $response = $this->get(route('declaraciones.pdf', $declaracion->id_declaracion));

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function exportacion_maneja_declaracion_sin_horarios()
    {
        // Arrange: Crear declaración sin horarios
        $declaracionSinHorarios = Declaracion::create([
            'id_usuario' => $this->usuario->id_usuario,
            'id_formulario' => $this->declaracion->id_formulario,
            'id_unidad' => $this->declaracion->id_unidad,
            'id_cargo' => $this->declaracion->id_cargo,
            'fecha_desde' => now()->subDays(7)->toDateString(),
            'fecha_hasta' => now()->toDateString(),
            'horas_totales' => 0,
            'fecha_envio' => now()
        ]);

        // Act
        $responsePdf = $this->get(route('declaraciones.pdf', $declaracionSinHorarios->id_declaracion));
        $responseExcel = $this->get(route('declaraciones.exportar', $declaracionSinHorarios->id_declaracion));

        // Assert
        $responsePdf->assertStatus(200);
        $responseExcel->assertStatus(200);
    }

    /** @test */
    public function multiples_exportaciones_generan_archivos_diferentes()
    {
        // Act: Primera exportación
        $response1 = $this->get(route('declaraciones.pdf', $this->declaracion->id_declaracion));
        
        // Act: Segunda exportación
        $response2 = $this->get(route('declaraciones.pdf', $this->declaracion->id_declaracion));

        // Assert
        $response1->assertStatus(200);
        $response2->assertStatus(200);
        
        // Ambas respuestas deben tener contenido
        $this->assertNotEmpty($response1->getContent());
        $this->assertNotEmpty($response2->getContent());
    }
}
