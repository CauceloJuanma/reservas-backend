<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoProducto;

class TipoProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aRegister = [
            [ 'id' => 1, 'nombre' => 'Mesa' ],
            [ 'id' => 2,  'nombre' => 'Producto' ],
        ];

        foreach ( $aRegister as $register ){
            TipoProducto::updateOrCreate([ 'id' => $register['id'] ], $register);
        }
    }
}
