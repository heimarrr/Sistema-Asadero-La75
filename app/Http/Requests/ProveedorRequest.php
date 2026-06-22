<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\OnlyLetters;
use App\Rules\AlphaNumericText;
use App\Rules\Phone;

class ProveedorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $proveedorId = $this->route('id');

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                'min:3',
                new OnlyLetters,
                $proveedorId
                    ? 'unique:proveedores,nombre,' . $proveedorId . ',id_proveedor'
                    : 'unique:proveedores,nombre',
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:20',
                new Phone,
            ],
            'direccion' => [
                'nullable',
                'string',
                'max:150',
                new AlphaNumericText,
            ],
            'correo' => [
                'nullable',
                'email',
                'max:100',
            ],
            'status' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'nombre.unique'   => 'Ya existe un proveedor con ese nombre.',
            'nombre.min'      => 'El nombre debe tener al menos 3 caracteres.',
            'correo.email'    => 'El correo no tiene un formato válido.',
        ];
    }
}