<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'nombre_usuario',
        'apellido_usuario',
        'usuario_usuario',
        'password_usuario',
        'estado_usuario',
        'rol_usuario'
    ];

    protected $hidden = [
        'password_usuario',
    ];
}
