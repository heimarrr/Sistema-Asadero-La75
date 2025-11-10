<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false; // tu tabla no los usa

    protected $fillable = [
        'nombre',
        'usuario',
        'contrasena',
        'id_rol',
    ];

    protected $hidden = [
        'contrasena',
    ];

    // ⚠️ Laravel por defecto busca "password", así que lo indicamos:
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // Relación opcional con roles
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }
}
