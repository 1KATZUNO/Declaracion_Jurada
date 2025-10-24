<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Eliminar o comentar esto:
        // User::factory(10)->create();
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // 🔹 Añadir tus seeders personalizados:
        $this->call(\Database\Seeders\UnidadSedeSeeder::class);
        // (Si luego agregas más, los llamas aquí)
    }
}
