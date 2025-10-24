<?php

namespace Database\Factories;

use App\Models\Sede;
use Illuminate\Database\Eloquent\Factories\Factory;

class SedeFactory extends Factory
{
    protected $model = Sede::class;

    public function definition(): array
    {
        return [
            'nombre'    => $this->faker->unique()->city().' (UCR)',
            'ubicacion' => $this->faker->address(),
        ];
    }
}
