<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    //cp 1.1
    public function test_cp01_1_iniciar_sesion_con_credenciales_validas()
    {
        Usuario::create([
            'nombre' => 'Test User',
            'correo' => 'test_login@ejemplo.com',
            'usuario' => 'testuser_unique_1',
            'contrasena' => Hash::make('password123'),
            'id_rol' => 1,
            'estado' => 1,
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'test_login@ejemplo.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'token_type', 'user'])
                 ->assertJsonFragment(['correo' => 'test_login@ejemplo.com']);
    }
    //cp 1.2
    public function test_cp01_2_iniciar_sesion_con_contrasena_incorrecta()
    {
        Usuario::create([
            'nombre' => 'Test User',
            'correo' => 'test_wrong@ejemplo.com',
            'usuario' => 'testuser_unique_2',
            'contrasena' => Hash::make('password123'),
            'id_rol' => 1,
            'estado' => 1,
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'test_wrong@ejemplo.com',
            'password' => 'clave_incorrecta',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Credenciales incorrectas o usuario inactivo.']);
    }
    //cp 1.3
    public function test_cp01_3_iniciar_sesion_con_usuario_inexistente()
    {
        $response = $this->postJson('/api/login', [
            'login' => 'noexiste_999@ejemplo.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Credenciales incorrectas o usuario inactivo.']);
    }

    //cp 1.4
    public function test_cp01_4_validar_campos_obligatorios_al_iniciar_sesion()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['login', 'password']);
    }

    //cp 1.5
    public function test_cp01_5_validar_acceso_segun_rol()
    {
        $cajero = Usuario::create([
            'nombre' => 'Cajero Test',
            'correo' => 'cajero_test@ejemplo.com',
            'usuario' => 'cajero_unique_3',
            'contrasena' => Hash::make('password123'),
            'id_rol' => 2,
            'estado' => 1,
        ]);

        $token = $cajero->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
             ->getJson('/api/usuarios')
             ->assertStatus(403);
    }

    //cp 2.1
    public function test_cp02_1_cerrar_sesion_correctamente()
    {
        $usuario = Usuario::create([
            'nombre' => 'Test Logout',
            'correo' => 'logout_test@ejemplo.com',
            'usuario' => 'logoutuser_unique_4',
            'contrasena' => Hash::make('password123'),
            'id_rol' => 1,
            'estado' => 1,
        ]);

        $token = $usuario->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
             ->postJson('/api/logout')
             ->assertStatus(200)
             ->assertJson(['message' => 'Sesión cerrada correctamente']);
    }

    //cp 2.2
    public function test_cp02_2_intentar_acceder_a_ruta_protegida_despues_de_cerrar_sesion()
    {
        $usuario = Usuario::create([
            'nombre' => 'Test Token',
            'correo' => 'token_test@ejemplo.com',
            'usuario' => 'tokenuser_unique_5',
            'contrasena' => Hash::make('password123'),
            'id_rol' => 1,
            'estado' => 1,
        ]);

        $token = $usuario->createToken('auth_token')->plainTextToken;

        // Cerrar sesión revocando el token
        $this->withHeader('Authorization', 'Bearer ' . $token)
             ->postJson('/api/logout')
             ->assertStatus(200);

        // Limpiar guard en memoria de Laravel para forzar re-autenticación
        $this->app['auth']->forgetGuards();

        // Intentar ingresar con el token eliminado
        $this->withHeader('Authorization', 'Bearer ' . $token)
             ->getJson('/api/usuarios')
             ->assertStatus(401);
    }

    //cp 3.11
    public function test_cp03_11_validar_que_usuario_inactivo_no_pueda_ingresar()
    {
        Usuario::create([
            'nombre' => 'Inactivo User',
            'correo' => 'inactivo_test@ejemplo.com',
            'usuario' => 'inactivo_unique_6',
            'contrasena' => Hash::make('password123'),
            'id_rol' => 1,
            'estado' => 0,
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'inactivo_test@ejemplo.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Credenciales incorrectas o usuario inactivo.']);
    }
}