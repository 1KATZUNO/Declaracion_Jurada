<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Models\UnidadAcademica;
use App\Models\Sede;
use App\Http\Controllers\UnidadAcademicaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;

class UnidadAcademicaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_index_devuelve_vista_correcta_con_unidades_y_sedes()
    {
        $unidadMock = Mockery::mock('alias:App\Models\UnidadAcademica');
        $unidadMock->shouldReceive('all')->andReturn([]);

        $sedeMock = Mockery::mock('alias:App\Models\Sede');
        $sedeMock->shouldReceive('all')->andReturn([]);

        View::shouldReceive('make')
            ->with('unidades.index', Mockery::type('array'))
            ->andReturnSelf();

        $controller = new UnidadAcademicaController();
        $view = $controller->index(new Request());

        $this->assertInstanceOf('Illuminate\View\View', $view);
    }

    public function test_store_crea_una_nueva_unidad_y_redirige()
    {
        $data = [
            'nombre' => 'Unidad Test',
            'id_sede' => 1,
            'estado' => 'ACTIVA'
        ];

        $requestMock = Mockery::mock('overload:App\Http\Requests\StoreUnidadAcademicaRequest');
        $requestMock->shouldReceive('validated')->andReturn($data);

        $unidadMock = Mockery::mock('alias:App\Models\UnidadAcademica');
        $unidadMock->shouldReceive('create')->once()->with($data);

        $controller = new UnidadAcademicaController();
        $response = $controller->store($requestMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('unidades.index'), $response->getTargetUrl());
    }

    public function test_update_actualiza_una_unidad_existente()
    {
        $data = [
            'nombre' => 'Unidad Actualizada',
            'id_sede' => 1,
            'estado' => 'ACTIVA'
        ];

        $requestMock = Mockery::mock('overload:App\Http\Requests\StoreUnidadAcademicaRequest');
        $requestMock->shouldReceive('validated')->andReturn($data);

        $unidadMock = Mockery::mock(UnidadAcademica::class);
        $unidadMock->shouldReceive('update')->once()->with($data);

        $controller = new UnidadAcademicaController();
        $response = $controller->update($requestMock, 1);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('unidades.index'), $response->getTargetUrl());
    }

    public function test_destroy_elimina_una_unidad_o_la_inactiva_si_tiene_declaraciones()
    {
        $unidadMock = Mockery::mock(UnidadAcademica::class);
        $unidadMock->shouldReceive('withCount')->once()->with('declaraciones')->andReturnSelf();
        $unidadMock->shouldReceive('findOrFail')->andReturnSelf();
        $unidadMock->declaraciones_count = 1;

        $controller = new UnidadAcademicaController();
        $response = $controller->destroy(1);

        $this->assertStringContainsString('Unidad inactivada', $response->getSession()->get('ok'));
    }

    public function test_catalogo_devuelve_lista_de_unidades_activas()
    {
        $unidadMock = Mockery::mock('alias:App\Models\UnidadAcademica');
        $unidadMock->shouldReceive('query')->once()->andReturnSelf();
        $unidadMock->shouldReceive('select')->once()->andReturnSelf();
        $unidadMock->shouldReceive('where')->once()->with('estado', 'ACTIVA')->andReturnSelf();
        $unidadMock->shouldReceive('orderBy')->once()->with('nombre')->andReturnSelf();
        $unidadMock->shouldReceive('get')->once()->andReturn([]);

        $controller = new UnidadAcademicaController();
        $response = $controller->catalogo(new Request());

        $this->assertJson($response->getContent());
    }
}