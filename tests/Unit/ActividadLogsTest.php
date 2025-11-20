<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ActividadLog;
use App\Http\Middleware\VerificarRol;

class ActividadLogsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        
                $this->withoutMiddleware(VerificarRol::class);
    }

    public function test_index_muestra_la_vista_de_registro_de_actividades()
    {
        
        ActividadLog::create([
            'id_usuario'       => null,
            'correo_usuario'   => 'admin@example.com',
            'accion'           => 'CREAR',
            'modulo'           => 'USUARIOS',
            'descripcion'      => 'Usuario de prueba creado desde test',
            'id_registro'      => 1,
            'datos_anteriores' => null,
            'datos_nuevos'     => ['nombre' => 'Test'],
            'ip_address'       => '127.0.0.1',
            'user_agent'       => 'PHPUnit',
        ]);

        ActividadLog::create([
            'id_usuario'       => null,
            'correo_usuario'   => 'admin@example.com',
            'accion'           => 'ACTUALIZAR',
            'modulo'           => 'DECLARACIONES',
            'descripcion'      => 'Declaración actualizada desde test',
            'id_registro'      => 2,
            'datos_anteriores' => ['estado' => 'Pendiente'],
            'datos_nuevos'     => ['estado' => 'Aprobada'],
            'ip_address'       => '127.0.0.1',
            'user_agent'       => 'PHPUnit',
        ]);

       
        $response = $this->get(route('actividad-logs.index'));

        $response->assertStatus(200);
        $response->assertViewIs('actividad-logs.index');

        $response->assertViewHas('logs');
        $response->assertViewHas('acciones');
        $response->assertViewHas('modulos');
    }

    public function test_show_muestra_el_detalle_de_una_actividad()
    {
        
        $log = ActividadLog::create([
            'id_usuario'       => null,
            'correo_usuario'   => 'admin@example.com',
            'accion'           => 'ELIMINAR',
            'modulo'           => 'DOCUMENTOS',
            'descripcion'      => 'Documento eliminado desde test',
            'id_registro'      => 3,
            'datos_anteriores' => ['nombre' => 'doc_prueba.pdf'],
            'datos_nuevos'     => null,
            'ip_address'       => '127.0.0.1',
            'user_agent'       => 'PHPUnit',
        ]);

        $response = $this->get(route('actividad-logs.show', $log->id_actividad));

        $response->assertStatus(200);
        $response->assertViewIs('actividad-logs.show');
        $response->assertViewHas('log');

        
        $response->assertSee('Detalle de Actividad');
        $response->assertSee($log->accion);
        $response->assertSee($log->modulo);
        $response->assertSee($log->descripcion);
    }
}