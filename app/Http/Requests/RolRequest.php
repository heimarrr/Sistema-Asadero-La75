<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\OnlyLetters;
use App\Rules\AlphaNumericText;

class RolRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rolId = $this->route('role') ?? collect($this->route()->parameters())->first();

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                'min:3',
                new OnlyLetters,
                $rolId
                    ? 'unique:roles,nombre,' . $rolId . ',id_rol'
                    : 'unique:roles,nombre',
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:255',
                new AlphaNumericText,
            ],
            'status' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe un rol con ese nombre.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
        ];
    }
}
