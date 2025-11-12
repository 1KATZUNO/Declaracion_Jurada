<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sede;
use App\Models\UnidadAcademica;

class SedeUnidadSeeder extends Seeder
{
    public function run()
    {
        // Crear sedes
        $sedeRodrigoFacio = Sede::firstOrCreate([
            'nombre' => 'Sede Rodrigo Facio'
        ], [
            'ubicacion' => 'San José'
        ]);

        $sedeGuanacaste = Sede::firstOrCreate([
            'nombre' => 'Sede Guanacaste'
        ], [
            'ubicacion' => 'Liberia, Guanacaste'
        ]);

        $sedeAtlantico = Sede::firstOrCreate([
            'nombre' => 'Sede Atlántico'
        ], [
            'ubicacion' => 'Turrialba'
        ]);

        // Crear unidades académicas para Sede Rodrigo Facio
        UnidadAcademica::firstOrCreate([
            'nombre' => 'Escuela de Ingeniería Eléctrica',
            'id_sede' => $sedeRodrigoFacio->id_sede
        ], [
            'estado' => 'ACTIVA'
        ]);

        UnidadAcademica::firstOrCreate([
            'nombre' => 'Escuela de Ciencias de la Computación',
            'id_sede' => $sedeRodrigoFacio->id_sede
        ], [
            'estado' => 'ACTIVA'
        ]);

        UnidadAcademica::firstOrCreate([
            'nombre' => 'Facultad de Medicina',
            'id_sede' => $sedeRodrigoFacio->id_sede
        ], [
            'estado' => 'ACTIVA'
        ]);

        // Crear unidades académicas para Sede Guanacaste
        UnidadAcademica::firstOrCreate([
            'nombre' => 'Ingeniería en Agronomía',
            'id_sede' => $sedeGuanacaste->id_sede
        ], [
            'estado' => 'ACTIVA'
        ]);

        UnidadAcademica::firstOrCreate([
            'nombre' => 'Escuela de Ciencias Biológicas',
            'id_sede' => $sedeGuanacaste->id_sede
        ], [
            'estado' => 'ACTIVA'
        ]);

        // Crear unidades académicas para Sede Atlántico
        UnidadAcademica::firstOrCreate([
            'nombre' => 'Centro de Investigación Agrícola',
            'id_sede' => $sedeAtlantico->id_sede
        ], [
            'estado' => 'ACTIVA'
        ]);
    }
}