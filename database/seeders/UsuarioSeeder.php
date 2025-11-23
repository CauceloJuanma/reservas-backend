<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aRegister = [
            [ 'id' => 1, 'tipo_id' => 2, 'nombre' => 'Simón', 'apellido' => 'Pérez', 'correo' => 'simon@gmail.com', 'contraseña' => '1234'],
            [ 'id' => 2, 'tipo_id' => 2, 'nombre' => 'Laura', 'apellido' => 'García', 'correo' => 'laura@gmail.com', 'contraseña' => '5678'],
            [ 'id' => 3, 'tipo_id' => 2, 'nombre' => 'Miguel', 'apellido' => 'López', 'correo' => 'miguel@gmail.com', 'contraseña' => 'abcd'],
        ];

        foreach ($aRegister as $register) {
            Usuario::updateOrCreate([ 'id' => $register['id'] ], $register);
        }
    }
}
