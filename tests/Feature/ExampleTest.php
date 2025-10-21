<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\UnidadAcademica;
use App\Models\Cargo;
use App\Models\Formulario;
use App\Models\Declaracion;
use App\Models\Usuario;
use App\Models\Sede;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que la ruta principal responde correctamente con datos.
     */
    public function test_returns_a_successful_response()
    {
        // Crear usuario (modelo Usuario, no User)
        $usuario = Usuario::create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'correo' => 'juan@example.com',
            'contrasena' => bcrypt('password123'), // Agregado campo requerido
            'telefono' => '12345678',
            'cedula' => '123456789',
        ]);

        // Crear sede primero (requerida por unidad_academica)
        $sede = Sede::create([
            'nombre' => 'Sede de Prueba',
            'direccion' => 'Dirección de prueba',
        ]);

        // Crear unidad académica
        $unidad = UnidadAcademica::create([
            'nombre' => 'Unidad de Prueba',
            'descripcion' => 'Descripción de prueba',
            'id_sede' => $sede->id_sede,
        ]);

        // Crear cargo
        $cargo = Cargo::create([
            'nombre' => 'Cargo de Prueba',
            'descripcion' => 'Descripción de cargo',
        ]);

        // Crear formulario
        $formulario = Formulario::create([
            'titulo' => 'Formulario de Prueba',
            'descripcion' => 'Descripción del formulario',
            'fecha_creacion' => now(),
        ]);

        // Crear declaración con todas las relaciones
        Declaracion::create([
            'id_usuario' => $usuario->id_usuario,
            'id_unidad' => $unidad->id_unidad,
            'id_cargo' => $cargo->id_cargo,
            'id_formulario' => $formulario->id_formulario,
            'fecha_desde' => now()->subDays(30),
            'fecha_hasta' => now(),
            'horas_totales' => 40,
        ]);

        // Hacer la petición
        $response = $this->get('/');

        // Verificar que responde correctamente
        $response->assertStatus(200);
    }

    /**
     * Test que la ruta principal responde correctamente sin datos.
     */
    public function test_returns_successful_response_with_empty_data()
    {
        // No crear ningún dato
        $response = $this->get('/');

        // Debe responder 200 incluso sin datos
        $response->assertStatus(200);
    }
}
