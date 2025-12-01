<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;

class ProductoController extends Controller
{
    public function productos_empresa($id)
    {
        $empresa = Empresa::find($id);

        if (!$empresa) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        return response()->json([
            'company' => $empresa,
            'products' => $empresa->productos()->where('estado_id', 1)->get()
        ]);
    }


}
