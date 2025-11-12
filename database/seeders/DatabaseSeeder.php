<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crea un usuario de prueba - Bryan
        Usuario::firstOrCreate(
            ['correo' => 'calderonespinozajosue@gmail.com'],
            [
                'nombre' => 'Bryan',
                'apellido' => 'Calderon',
                'contrasena' => bcrypt('1234'),
                'telefono' => '85563477',
                'rol' => 'admin',
                'identificacion' => '504510044',
            ]
        );

        // Crea usuario Carlos Jiménez
        Usuario::firstOrCreate(
            ['correo' => 'zammaducr@gmail.com'],
            [
                'nombre' => 'Carlos',
                'apellido' => 'Jiménez',
                'contrasena' => bcrypt('holamundo'),
                'telefono' => '63824488',
                'rol' => 'admin',
                'identificacion' => '123456789',
            ]
        );
    }
}
