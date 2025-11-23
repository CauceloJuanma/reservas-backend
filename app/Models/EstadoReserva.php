<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoReserva extends Model
{
    protected $table = 'estadoreserva';

    protected $fillable = [
        'nombre',
    ];
}
