<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EstadoEmpresa;

class EstadoEmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aRegister = [
            [ 'id' => 1, 'nombre' => 'Activa' ],
            [ 'id' => 2,  'nombre' => 'Inactiva' ],
        ];

        foreach ( $aRegister as $register ){
            EstadoEmpresa::updateOrCreate([ 'id' => $register['id'] ], $register);
        }
    }
}
