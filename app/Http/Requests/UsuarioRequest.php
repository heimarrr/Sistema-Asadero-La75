<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\OnlyLetters;
use App\Rules\Username;

class UsuarioRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $usuarioId = $this->route('usuario') ?? collect($this->route()->parameters())->first();
        $esUpdate = (bool) $usuarioId;

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                'min:3',
                new OnlyLetters,
            ],
            'usuario' => [
                'required',
                'string',
                'max:255',
                'min:4',
                new Username,
                $esUpdate
                    ? 'unique:usuarios,usuario,' . $usuarioId . ',id_usuario'
                    : 'unique:usuarios,usuario',
            ],
            'correo' => [
                'required',
                'email',
                'max:255',
                $esUpdate
                    ? 'unique:usuarios,correo,' . $usuarioId . ',id_usuario'
                    : 'unique:usuarios,correo',
            ],
            // En update la contraseña es opcional (solo se cambia si se envía)
            'contrasena' => [
                $esUpdate ? 'nullable' : 'required',
                'string',
                'min:6',
                'max:255',
            ],
            'id_rol' => 'required|exists:roles,id_rol',
            'estado' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required'    => 'El nombre es obligatorio.',
            'usuario.required'   => 'El usuario es obligatorio.',
            'usuario.unique'     => 'Ese nombre de usuario ya está en uso.',
            'usuario.min'        => 'El usuario debe tener al menos 4 caracteres.',
            'correo.required'    => 'El correo es obligatorio.',
            'correo.email'       => 'El correo no tiene un formato válido.',
            'correo.unique'      => 'Ese correo ya está registrado.',
            'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.min'     => 'La contraseña debe tener al menos 6 caracteres.',
            'id_rol.required'    => 'El rol es obligatorio.',
            'id_rol.exists'      => 'El rol seleccionado no existe.',
        ];
    }
}
