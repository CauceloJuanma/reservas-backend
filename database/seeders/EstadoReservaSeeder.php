<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EstadoReserva;

class EstadoReservaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aRegister = [
            [ 'id' => 1, 'nombre' => 'Pendiente' ],
            [ 'id' => 2,  'nombre' => 'Confirmada' ],
            [ 'id' => 3,  'nombre' => 'Cancelada' ],
            [ 'id' => 4,  'nombre' => 'Completada' ],
        ];

        foreach ($aRegister as $register) {
            EstadoReserva::updateOrCreate([ 'id' => $register['id'] ], $register);
        }
    }
}
