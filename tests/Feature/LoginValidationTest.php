<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginValidationTest extends TestCase
{
    /**
     * CP-003
     * Verificar que el login rechace campos vacíos.
     */
    public function test_login_rechaza_campos_vacios(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'login',
            'password',
        ]);
    }
}