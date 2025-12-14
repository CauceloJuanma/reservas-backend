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
        'hora_ini',
        'hora_fin',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoProducto::class, 'tipo_id');
    }

    // Método helper para obtener hora_ini formateada
    public function getHoraIniFormato()
    {
        return $this->hora_ini ? substr($this->hora_ini, 0, 5) : null;
    }

    // Método helper para obtener hora_fin formateada
    public function getHoraFinFormato()
    {
        return $this->hora_fin ? substr($this->hora_fin, 0, 5) : null;
    }

    public function tieneRestriccionHoraria()
    {
        return !empty($this->hora_ini) && !empty($this->hora_fin); 
    }

    public function esHoraValida($hora)
    {
        if (empty($this->hora_ini) || empty($this->hora_fin)) {
            return true;
        }
        
        $horaCarbon = \Carbon\Carbon::createFromFormat('H:i', $hora);
        $inicio = \Carbon\Carbon::createFromFormat('H:i', $this->getHoraIniFormato()); 
        $fin = \Carbon\Carbon::createFromFormat('H:i', $this->getHoraFinFormato());
        
        return $horaCarbon->between($inicio, $fin);
    }

    public function generarSlots($intervaloMinutos = 30)
    {
        if (empty($this->hora_ini) || empty($this->hora_fin)) {
            return [];
        }
        
        $slots = [];
        $inicio = \Carbon\Carbon::parse("today " . $this->getHoraIniFormato()); 
        $fin = \Carbon\Carbon::parse("today " . $this->getHoraFinFormato());
        
        while ($inicio->lte($fin)) {
            $slots[] = $inicio->format('H:i');
            $inicio->addMinutes($intervaloMinutos);
        }
        
        return $slots;
    }
}