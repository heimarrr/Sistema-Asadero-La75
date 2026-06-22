<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class AlphaNumericText implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ0-9\s.,;:()\-]+$/u', $value)) {
            $fail('El campo :attribute contiene caracteres no permitidos.');
        }
    }
}