<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'nombre',
        'descripcion',
        'stock_actual',
        'tipo',
        'unidad_medida',
        'precio_compra',
        'precio_venta',
        'status',
        'id_categoria',
    ];

    // Relación con Categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    // Relación con DetalleCompras
    public function detalleCompras()
    {
        return $this->hasMany(DetalleCompra::class, 'id_producto', 'id_producto');
    }

    // Relación con DetalleVentas
    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto', 'id_producto');
    }
}
