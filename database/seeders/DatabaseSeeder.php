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
        // Crea un usuario de prueba
        Usuario::factory()->create([
            'nombre' => 'Bryan',
            'apellido' => 'Calderon',
            'correo' => 'calderonespinozajosue@gmail.com',
            'contrasena' => bcrypt('1234'),
            'telefono' => '85563477',
            'rol' => 'admin',
            'identificacion' => '504510044',
        ]);
    }
}
