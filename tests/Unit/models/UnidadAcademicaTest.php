<?php

namespace Tests\Unit\Models;

use App\Models\Declaracion;
use App\Models\Sede;
use App\Models\UnidadAcademica;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class UnidadAcademicaTest extends TestCase
{
    public function test_el_nombre_de_la_tabla_es_unidad_academica(): void
    {
        $unidad = new UnidadAcademica();

        $this->assertSame('unidad_academica', $unidad->getTable());
    }

    public function test_la_clave_primaria_es_id_unidad(): void
    {
        $unidad = new UnidadAcademica();

        $this->assertSame('id_unidad', $unidad->getKeyName());
    }

    public function test_los_atributos_asignables(): void
    {
        $unidad = new UnidadAcademica();

        $this->assertSame(['nombre', 'id_sede'], $unidad->getFillable());
    }

    public function test_relacion_con_sede(): void
    {
        $unidad = new UnidadAcademica();

        $relacion = $unidad->sede();

        $this->assertInstanceOf(BelongsTo::class, $relacion);
        $this->assertInstanceOf(Sede::class, $relacion->getRelated());
        $this->assertSame('id_sede', $relacion->getForeignKeyName());
    }

    public function test_relacion_con_declaraciones(): void
    {
        $unidad = new UnidadAcademica();

        $relacion = $unidad->declaraciones();

        $this->assertInstanceOf(HasMany::class, $relacion);
        $this->assertInstanceOf(Declaracion::class, $relacion->getRelated());
        $this->assertSame('id_unidad', $relacion->getForeignKeyName());
    }
}
