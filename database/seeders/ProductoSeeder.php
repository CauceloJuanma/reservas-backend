<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aRegister = [
            [
                'id' => 1,
                'empresa_id' => 1,
                'tipo_id' => 1,
                'nombre' => 'Producto A',
                'descripcion' => 'Descripción del Producto A',
                'precio' => 10.50,
                'stock' => 100,
            ],
            [
                'id' => 2,
                'empresa_id' => 1,
                'tipo_id' => 2,
                'nombre' => 'Producto B',
                'descripcion' => 'Descripción del Producto B',
                'precio' => 20.00,
                'stock' => 50,
            ],
            [
                'id' => 3,
                'empresa_id' => 2,
                'tipo_id' => 1,
                'nombre' => 'Producto C',
                'descripcion' => 'Descripción del Producto C',
                'precio' => 15.75,
                'stock' => 75,
            ],
            [
                'id' => 4,
                'empresa_id' => 3,
                'tipo_id' => 2,
                'nombre' => 'Producto D',
                'descripcion' => 'Descripción del Producto D',
                'precio' => 30.00,
                'stock' => 20,
            ],
            [
                'id' => 5,
                'empresa_id' => 4,
                'tipo_id' => 1,
                'nombre' => 'Producto E',
                'descripcion' => 'Descripción del Producto E',
                'precio' => 25.00,
                'stock' => 60,
            ]
        ];

        foreach ($aRegister as $register) {
            Producto::updateOrCreate([ 'id' => $register['id'] ], $register);
        }
    }
}
