<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoUsuario;

class TipoUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aRegister = [
            [ 'id' => 1, 'nombre' => 'Administrador' ],
            [ 'id' => 2,  'nombre' => 'Cliente' ],
        ];

        foreach ( $aRegister as $register ){
            TipoUsuario::updateOrCreate([ 'id' => $register['id'] ], $register);
        }
    }
}
