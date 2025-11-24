<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'usuario',
        'correo',
        'contrasena',
        'id_rol',
        'estado',
    ];

    protected $hidden = [
        'contrasena',
    ];

    
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function adminlte_image()
    {
        // Ejemplo: Retorna una imagen por defecto o una dinámica desde un campo de la base de datos
        return 'https://cdn.pixabay.com/photo/2023/02/18/11/00/icon-7797704_1280.png';
    }
}
