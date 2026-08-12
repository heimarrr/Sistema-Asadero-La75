<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VentaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|integer|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1|max:10000',
            'fecha' => 'nullable|date|date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'productos.required' => 'Debe agregar al menos un producto a la venta.',
            'productos.array' => 'El formato de productos no es válido.',
            'productos.min' => 'Debe agregar al menos un producto a la venta.',
            'productos.*.id_producto.required' => 'Falta el producto en uno de los ítems.',
            'productos.*.id_producto.exists' => 'Uno de los productos seleccionados no existe.',
            'productos.*.cantidad.required' => 'Falta la cantidad en uno de los ítems.',
            'productos.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
            'fecha.date_format' => 'La fecha debe tener el formato AAAA-MM-DD.',
        ];
    }

    /**
     * Validación adicional que las reglas simples no cubren:
     * evita que el mismo producto se repita en el array.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $productos = $this->input('productos', []);

            $ids = array_column($productos, 'id_producto');

            if (count($ids) !== count(array_unique($ids))) {
                $validator->errors()->add('productos', 'No puedes incluir el mismo producto más de una vez en la venta.');
            }
        });
    }
}
