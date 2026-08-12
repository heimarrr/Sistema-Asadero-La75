<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class Username implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Letras, números, punto y guion bajo. Sin espacios ni símbolos raros.
        if (!preg_match('/^[a-zA-Z0-9._]+$/', $value)) {
            $fail('El campo :attribute solo puede contener letras, números, puntos y guiones bajos, sin espacios.');
        }
    }
}
