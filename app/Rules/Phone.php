<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class Phone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Permite números, espacios, guiones y un + opcional al inicio (para código de país)
        if (!preg_match('/^\+?[0-9\s\-]{7,20}$/', $value)) {
            $fail('El campo :attribute debe ser un número de teléfono válido.');
        }
    }
}
