<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Reserva;
use App\Models\Producto;

class LineaProducto extends Model
{
    protected $table = 'lineaproducto';

    protected $fillable = [
        'reserva_id',        
        'producto_id',
        'cantidad',
        'precio_unitario',   
        'subtotal',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
