<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;
use App\Models\TipoProducto;

class Producto extends Model
{
    protected $table = 'producto';

    protected $fillable = [
        'empresa_id',
        'tipo_id',
        'nombre',
        'descripcion',
        'precio',
        'stock',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoProducto::class, 'tipo_id');
    }
}
