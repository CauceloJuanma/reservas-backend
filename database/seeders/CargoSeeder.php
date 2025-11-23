<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CargoUsuarioEmpresa;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aRegister = [
            [ 'id' => 1, 'nombre' => 'Empleado' ],
            [ 'id' => 2, 'nombre' => 'Gerente' ],
        ];

        foreach ($aRegister as $register) {
            CargoUsuarioEmpresa::updateOrCreate([ 'id' => $register['id'] ], $register);
        }
    }
}
