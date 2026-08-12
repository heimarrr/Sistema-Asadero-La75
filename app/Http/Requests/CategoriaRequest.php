<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\OnlyLetters;
use App\Rules\AlphaNumericText;

class CategoriaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $categoriaId = $this->route('categoria') ?? collect($this->route()->parameters())->first();

        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                'min:3',
                new OnlyLetters,
                $categoriaId
                    ? 'unique:categorias,nombre,' . $categoriaId . ',id_categoria'
                    : 'unique:categorias,nombre',
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
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique'   => 'Ya existe una categoría con ese nombre.',
            'nombre.min'      => 'El nombre debe tener al menos 3 caracteres.',
        ];
    }
}
