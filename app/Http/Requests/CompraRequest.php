<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompraRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
            'fecha' => 'required|date|date_format:Y-m-d',
            'total_compra' => 'required|numeric|min:0.01|max:9999999.99',

            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|numeric|min:0.01|max:999999',
            'productos.*.precio_unitario' => 'required|numeric|min:0.01|max:999999.99',
        ];
    }

    public function messages()
    {
        return [
            'id_proveedor.required' => 'Debe seleccionar un proveedor.',
            'id_proveedor.exists' => 'El proveedor seleccionado no existe.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date_format' => 'La fecha debe tener el formato AAAA-MM-DD.',
            'total_compra.required' => 'El total de la compra es obligatorio.',
            'total_compra.numeric' => 'El total debe ser un número.',
            'total_compra.min' => 'El total debe ser mayor a 0.',
            'productos.required' => 'Debe agregar al menos un producto a la compra.',
            'productos.min' => 'Debe agregar al menos un producto a la compra.',
            'productos.*.id_producto.required' => 'Falta el producto en uno de los ítems.',
            'productos.*.id_producto.exists' => 'Uno de los productos seleccionados no existe.',
            'productos.*.cantidad.required' => 'Falta la cantidad en uno de los ítems.',
            'productos.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'productos.*.precio_unitario.required' => 'Falta el precio unitario en uno de los ítems.',
            'productos.*.precio_unitario.min' => 'El precio unitario debe ser mayor a 0.',
        ];
    }

    /**
     * Validaciones adicionales que las reglas simples no cubren.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $productos = $this->input('productos', []);

            // Evitar el mismo producto repetido en el array
            $ids = array_column($productos, 'id_producto');
            if (count($ids) !== count(array_unique($ids))) {
                $validator->errors()->add('productos', 'No puedes incluir el mismo producto más de una vez en la compra.');
            }

            // Verificar que el total enviado coincida con la suma de los subtotales
            $totalCalculado = 0;
            foreach ($productos as $item) {
                if (isset($item['cantidad'], $item['precio_unitario'])) {
                    $totalCalculado += round($item['cantidad'] * $item['precio_unitario'], 2);
                }
            }

            $totalEnviado = (float) $this->input('total_compra', 0);

            if (round($totalCalculado, 2) !== round($totalEnviado, 2)) {
                $validator->errors()->add('total_compra', 'El total enviado no coincide con la suma de los productos.');
            }
        });
    }
}