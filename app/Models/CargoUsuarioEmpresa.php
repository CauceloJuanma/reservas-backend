<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CargoUsuarioEmpresa extends Model
{
    protected $table = 'cargousuarioempresa';

    protected $fillable = [
        'nombre',
    ];
}
