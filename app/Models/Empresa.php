<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresa';

    protected $fillable = [
        'nombre',
        'descripcion',
        'direccion',
        'telefono',
        'email',
        'estado_id',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'empresa_id');
    }
}
