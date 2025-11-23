<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineaProducto extends Model
{
    protected $table = 'lineaproducto';

    protected $fillable = [
        'producto_id',
        'cantidad',
        'precio',
    ];
}
