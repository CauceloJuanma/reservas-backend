<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LineaProducto;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Models\EstadoReserva;

class Reserva extends Model
{
    protected $table = 'reserva';

    protected $fillable = [
        'usuario_id',
        'empresa_id',
        'fecha_hora',
        'cantidad',
        'estado_id',
        'importe',
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
        return $this->belongsTo(EstadoReserva::class, 'estado_id');
    }

    public function getPrimerProducto()
    {
        return $this->lineas()
            ->with('producto')
            ->first()
            ?->producto
            ?->nombre ?? 'N/A';
    }
}

