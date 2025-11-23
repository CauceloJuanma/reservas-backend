<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioEmpresa extends Model
{
    protected $table = 'usuario_empresa';

    protected $fillable = [
        'usuario_id',
        'empresa_id',
        'cargo_id',
    ];
}
