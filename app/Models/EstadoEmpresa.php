<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoEmpresa extends Model
{
    protected $table = 'estadoempresa';

    protected $fillable = [
        'nombre',
    ];
}
