<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function reservar_producto($id, Request $request)
    {
        // Lógica para reservar el producto con ID $id
        // Puedes acceder a los datos del usuario autenticado mediante $request->user()
        

        return response()->json([
            'message' => "Producto con ID $id reservado exitosamente."
        ]);
    }
}
