<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\AlphaNumericText;

class ProductoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                'min:3',
                new AlphaNumericText,
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:200',
                new AlphaNumericText,
            ],
            'stock_actual' => 'required|numeric|min:0',
            'unidad_medida' => [
                'required',
                'string',
                'max:50',
                new AlphaNumericText,
            ],
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'tipo' => 'required|in:insumo,venta',
            'status' => 'nullable|boolean',
            'id_categoria' => 'required|exists:categorias,id_categoria',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'stock_actual.required' => 'El stock actual es obligatorio.',
            'stock_actual.numeric' => 'El stock debe ser un número.',
            'stock_actual.min' => 'El stock no puede ser negativo.',
            'unidad_medida.required' => 'La unidad de medida es obligatoria.',
            'precio_compra.numeric' => 'El precio de compra debe ser un número.',
            'precio_venta.numeric' => 'El precio de venta debe ser un número.',
            'tipo.in' => 'El tipo debe ser "insumo" o "venta".',
            'id_categoria.required' => 'La categoría es obligatoria.',
            'id_categoria.exists' => 'La categoría seleccionada no existe.',
        ];
    }
}