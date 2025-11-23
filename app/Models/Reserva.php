<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reserva';

    protected $fillable = [
        'usuario_id',
        'producto_id',
        'fecha_reserva',
        'cantidad',
        'estado_id',
    ];
}
