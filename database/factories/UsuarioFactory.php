<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'correo' => $this->faker->unique()->safeEmail(),
            'contrasena' => bcrypt('password'),
            'telefono' => $this->faker->phoneNumber(),
            'rol' => 'funcionario',
            'identificacion' => $this->faker->unique()->numerify('#########'),
        ];
    }
}
