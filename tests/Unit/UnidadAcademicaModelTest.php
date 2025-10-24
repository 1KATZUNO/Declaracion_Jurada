<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Sede;
use App\Models\UnidadAcademica;

class UnidadAcademicaModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function pertenece_a_una_sede()
    {
        $sede = Sede::factory()->create();
        $u = UnidadAcademica::factory()->create(['id_sede'=>$sede->id_sede]);

        $this->assertEquals($sede->id_sede, $u->sede->id_sede);
    }
}
