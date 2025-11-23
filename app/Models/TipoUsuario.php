<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipo_Usuario extends Model
{
    protected $table = 'tipousuario';

    protected $fillable = [
        'nombre',
    ];
}
