<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Empresa;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aRegister = [
            [ 'id' => 1, 'estado_id' => 1, 'nombre' => 'Churrerias Paco', 'descripcion' => 'La mejor churreria de la ciudad', 'direccion' => 'Avenida ESI, 2', 'telefono' => '123456789', 'email' => 'paquitoelchurrero@gmail.com'],
            [ 'id' => 2, 'estado_id' => 1, 'nombre' => 'Pizzeria Donatello', 'descripcion' => 'Pizzas artesanas al estilo italiano', 'direccion' => 'Calle Leonardo da Vinci, 10', 'telefono' => '987654321', 'email' => 'pizzavinci@gmail.com'],
            [ 'id' => 3, 'estado_id' => 1, 'nombre' => 'Hamburguesas El Toro', 'descripcion' => 'Hamburguesas gourmet con ingredientes frescos', 'direccion' => 'Plaza del Sol, 5', 'telefono' => '555123456', 'email' => 'hamburtoro@gmail.com'],
            [ 'id' => 4, 'estado_id' => 1, 'nombre' => 'Sushi Zen', 'descripcion' => 'Auténtico sushi japonés preparado al momento', 'direccion' => 'Calle Sakura, 8', 'telefono' => '444987654', 'email' => 'sushizen@gmail.com'],
            [ 'id' => 5, 'estado_id' => 1, 'nombre' => 'Cafeteria La Esquina', 'descripcion' => 'Cafés especiales y repostería casera', 'direccion' => 'Avenida Central, 12', 'telefono' => '333654321', 'email' => 'laesquina@gmail.com'],
            [ 'id' => 6, 'estado_id' => 1, 'nombre' => 'Restaurante El Rincón', 'descripcion' => 'Cocina tradicional con un toque moderno', 'direccion' => 'Calle Mayor, 20', 'telefono' => '222123456', 'email' => 'elrincon@gmail.com'],
            [ 'id' => 7, 'estado_id' => 1, 'nombre' => 'Tacos y Más', 'descripcion' => 'Tacos mexicanos auténticos y sabrosos', 'direccion' => 'Boulevard de la Fiesta, 15', 'telefono' => '111987654', 'email' => 'tacosboulevard@gmail.com'],
        ];

        foreach ($aRegister as $register) {
            Empresa::updateOrCreate([ 'id' => $register['id'] ], $register);
        }
    }
}
