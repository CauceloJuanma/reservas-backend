<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EstadoProducto;

class EstadoProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aRegister = [
            [ 'id' => 1, 'nombre' => 'Activo' ],
            [ 'id' => 2,  'nombre' => 'Inactivo' ],
            [ 'id' => 3,  'nombre' => 'Fuera de Stock' ],
        ];

        foreach ( $aRegister as $register ){
            EstadoProducto::updateOrCreate([ 'id' => $register['id'] ], $register);
        }
    }
}
