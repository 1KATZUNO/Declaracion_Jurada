<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sede;
use App\Models\UnidadAcademica;

class UnidadSedeSeeder extends Seeder
{
    public function run(): void
    {
        $sedes = Sede::factory()->count(3)->create();
        foreach ($sedes as $s) {
            UnidadAcademica::factory()->count(4)->create(['id_sede' => $s->id_sede]);
        }
    }
}
