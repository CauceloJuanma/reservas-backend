<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LineaProducto;
use App\Models\Usuario;

class Reserva extends Model
{
    protected $table = 'reserva';

    protected $fillable = [
        'usuario_id',
        'empresa_id',
        'fecha_reserva',
        'cantidad',
        'estado_id',
    ];

    public function lineas()
    {
        return $this->hasMany(LineaProducto::class, 'reserva_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}

