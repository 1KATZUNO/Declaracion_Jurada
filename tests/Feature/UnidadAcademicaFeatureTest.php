<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Sede;
use App\Models\UnidadAcademica;
use Illuminate\Support\Facades\DB;

class UnidadAcademicaFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_listar_unidades()
    {
        $sede = Sede::factory()->create();
        UnidadAcademica::factory()->create(['id_sede'=>$sede->id_sede,'nombre'=>'Escuela de Prueba']);

        $this->get('/unidades')
             ->assertStatus(200)
             ->assertSee('Escuela de Prueba');
    }

    /** @test */
    public function crea_unidad_valida()
    {
        $sede = Sede::factory()->create();

        $this->post('/unidades', [
            'nombre'  => 'Nueva Unidad',
            'id_sede' => $sede->id_sede,
            'estado'  => 'ACTIVA',
        ])->assertRedirect('/unidades');

        $this->assertDatabaseHas('unidad_academica', [
            'nombre'  => 'Nueva Unidad',
            'id_sede' => $sede->id_sede,
            'estado'  => 'ACTIVA',
        ]);
    }

    /** @test */
    public function valida_campos_requeridos()
    {
        $this->post('/unidades', [])->assertSessionHasErrors(['nombre','id_sede','estado']);
    }

    /** @test */
    public function actualiza_unidad()
    {
        $sedeA = Sede::factory()->create();
        $sedeB = Sede::factory()->create();
        $u = UnidadAcademica::factory()->create(['id_sede'=>$sedeA->id_sede,'nombre'=>'Vieja']);

        $this->put("/unidades/{$u->id_unidad}", [
            'nombre'  => 'Nueva',
            'id_sede' => $sedeB->id_sede,
            'estado'  => 'INACTIVA',
        ])->assertRedirect('/unidades');

        $this->assertDatabaseHas('unidad_academica', [
            'id_unidad' => $u->id_unidad,
            'nombre'    => 'Nueva',
            'id_sede'   => $sedeB->id_sede,
            'estado'    => 'INACTIVA',
        ]);
    }

    /** @test */
    public function elimina_sin_dependencias_y_inactiva_si_hay_declaraciones()
    {
        $sede = Sede::factory()->create();
        $sinDeps = UnidadAcademica::factory()->create(['id_sede'=>$sede->id_sede]);
        $conDeps = UnidadAcademica::factory()->create(['id_sede'=>$sede->id_sede]);

        // Simular una declaración apuntando a $conDeps (sin tocar controladores de declaración)
        $usuarioId = DB::table('usuario')->insertGetId([
            'nombre'=>'Ana','apellido'=>'Soto','correo'=>'ana@ucr.ac.cr',
            'contrasena'=>bcrypt('secret'),'telefono'=>'88888888','rol'=>'admin'
        ]);
        $formId = DB::table('formulario')->insertGetId(['titulo'=>'F','fecha_creacion'=>now()]);
        $cargoId = DB::table('cargo')->insertGetId(['nombre'=>'Docente']);
        DB::table('declaracion')->insert([
            'id_usuario'   => $usuarioId,
            'id_formulario'=> $formId,
            'id_unidad'    => $conDeps->id_unidad,
            'id_cargo'     => $cargoId,
            'fecha_desde'  => now()->toDateString(),
            'fecha_hasta'  => now()->toDateString(),
            'horas_totales'=> 1.0,
        ]);

        // 1) sin dependencias -> soft delete
        $this->delete("/unidades/{$sinDeps->id_unidad}")->assertRedirect();
        $this->assertSoftDeleted('unidad_academica', ['id_unidad'=>$sinDeps->id_unidad]);

        // 2) con dependencias -> inactivar (no borrar)
        $this->delete("/unidades/{$conDeps->id_unidad}")->assertRedirect();
        $this->assertDatabaseHas('unidad_academica', [
            'id_unidad' => $conDeps->id_unidad,
            'estado'    => 'INACTIVA',
        ]);
        $this->assertDatabaseHas('unidad_academica', [
            'id_unidad' => $conDeps->id_unidad,
            'deleted_at'=> null,
        ]);
    }
}
