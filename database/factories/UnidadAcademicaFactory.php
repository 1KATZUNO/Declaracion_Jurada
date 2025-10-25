<?php

namespace Database\Factories;

use App\Models\UnidadAcademica;
use App\Models\Sede;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnidadAcademicaFactory extends Factory
{
    protected $model = UnidadAcademica::class;

    public function definition(): array
    {
        return [
            'nombre'  => 'Escuela de '.$this->faker->unique()->word(),
            'id_sede' => Sede::factory(),
            'estado'  => 'ACTIVA',
        ];
    }
}
