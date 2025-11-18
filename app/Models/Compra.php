<?php

// app/Models/Compra.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'compras'; 

    // Clave primaria
    protected $primaryKey = 'id_compra'; 

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'id_proveedor',
        'id_usuario',
        'fecha',
        'total',
        'status',
    ];
    
    // Relación: Una compra tiene muchos detalles de compra (One-to-Many)
    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'id_compra', 'id_compra');
    }

    // Relación con Proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }
    
    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario'); // Asumiendo que tu tabla de usuarios usa 'id_usuario'
    }
}
