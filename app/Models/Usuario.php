<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // 🔹 importante para login
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 🔹 necesario para Sanctum

class Usuario extends Authenticatable
{
    protected $table = 'usuario';

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'pass',
        'tipo_id',
    ];

    protected $hidden = [
        'pass',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->pass; // Tu columna se llama 'pass', no 'password'
    }

    public function getAuthIdentifierName()
    {
        return 'id'; // Tu primary key
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }
    
    public function tipoUsuario()
    {
        return $this->belongsTo(TipoUsuario::class, 'tipo_id');
    }

}
