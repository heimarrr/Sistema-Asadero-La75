<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    use DatabaseTransactions;

    private function actAsAdmin()
    {
        $admin = Usuario::create([
            'nombre' => 'Admin Test',
            'correo' => 'admin_' . uniqid() . '@ejemplo.com',
            'usuario' => 'admin_' . uniqid(),
            'contrasena' => Hash::make('password123'),
            'id_rol' => 1,
            'estado' => 1,
        ]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        return $this->withHeader('Authorization', 'Bearer ' . $token);
    }

    //cp 3.1
    public function test_cp03_1_registrar_usuario_correctamente()
    {
        $data = [
            'nombre' => 'Carlos Mendoza',
            'usuario' => 'carlosm_' . uniqid(),
            'correo' => 'carlos_' . uniqid() . '@ejemplo.com',
            'contrasena' => 'Password123',
            'id_rol' => 1,
            'estado' => 1,
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/usuarios', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Usuario creado correctamente'
                 ])
                 ->assertJsonPath('data.nombre', 'Carlos Mendoza');

        $this->assertDatabaseHas('usuarios', [
            'correo' => $data['correo']
        ]);
    }

    //cp 3.2
    public function test_cp03_2_registrar_usuario_con_campos_obligatorios_vacios()
    {
        $response = $this->actAsAdmin()
                         ->postJson('/api/usuarios', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'nombre',
                     'usuario',
                     'correo',
                     'contrasena',
                     'id_rol',
                     'estado'
                 ]);
    }

    //cp 3.3
    public function test_cp03_3_registrar_usuario_con_datos_invalidos()
    {
        $data = [
            'nombre' => 'Carlos123', // Inválido por OnlyLetters
            'usuario' => 'usr',      // Inválido por min:4
            'correo' => 'no-es-correo', // Inválido por formato email
            'contrasena' => '123',   // Inválido por min:6
            'id_rol' => 99999,       // Inválido por exists:roles
            'estado' => 'no-booleano'
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/usuarios', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'nombre',
                     'usuario',
                     'correo',
                     'contrasena',
                     'id_rol',
                     'estado'
                 ]);
    }

    //cp 3.4
    public function test_cp03_4_registrar_usuario_con_correo_existente()
    {
        $correoExistente = 'existente_' . uniqid() . '@ejemplo.com';

        Usuario::create([
            'nombre' => 'Usuario Existente',
            'correo' => $correoExistente,
            'usuario' => 'user_' . uniqid(),
            'contrasena' => Hash::make('password123'),
            'id_rol' => 1,
            'estado' => 1,
        ]);

        $data = [
            'nombre' => 'Nuevo Usuario',
            'usuario' => 'user_' . uniqid(),
            'correo' => $correoExistente, // Correo duplicado
            'contrasena' => 'Password123',
            'id_rol' => 1,
            'estado' => 1,
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/usuarios', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['correo']);
    }

    //cp 3.5
    public function test_cp03_5_consultar_listado_de_usuarios()
    {
        $response = $this->actAsAdmin()
                         ->getJson('/api/usuarios');

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id_usuario', 'nombre', 'usuario', 'correo', 'id_rol', 'estado', 'rol']
                     ]
                 ]);
    }

    //cp 3.6
    public function test_cp03_6_consultar_informacion_de_un_usuario()
    {
        $targetUser = Usuario::create([
            'nombre' => 'Ana Gomez',
            'correo' => 'ana_' . uniqid() . '@ejemplo.com',
            'usuario' => 'anag_' . uniqid(),
            'contrasena' => Hash::make('password123'),
            'id_rol' => 1,
            'estado' => 1,
        ]);

        $response = $this->actAsAdmin()
                         ->getJson('/api/usuarios/' . $targetUser->id_usuario);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id_usuario' => $targetUser->id_usuario,
                         'nombre' => 'Ana Gomez'
                     ]
                 ]);
    }

    //cp 3.7
    public function test_cp03_7_editar_usuario_correctamente()
    {
        $user = Usuario::create([
            'nombre' => 'Nombre Original',
            'correo' => 'original_' . uniqid() . '@ejemplo.com',
            'usuario' => 'user_' . uniqid(),
            'contrasena' => Hash::make('password123'),
            'id_rol' => 1,
            'estado' => 1,
        ]);

        $updateData = [
            'nombre' => 'Nombre Editado',
            'usuario' => $user->usuario,
            'correo' => $user->correo,
            'id_rol' => 1,
            'estado' => 1,
        ];

        $response = $this->actAsAdmin()
                         ->putJson('/api/usuarios/' . $user->id_usuario, $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Usuario actualizado correctamente'
                 ]);

        $this->assertDatabaseHas('usuarios', [
            'id_usuario' => $user->id_usuario,
            'nombre' => 'Nombre Editado'
        ]);
    }

    //cp 3.8
    public function test_cp03_8_validar_datos_al_editar_usuario()
    {
        $user = Usuario::create([
            'nombre' => 'Laura Perez',
            'correo' => 'laura_' . uniqid() . '@ejemplo.com',
            'usuario' => 'laurap_' . uniqid(),
            'contrasena' => Hash::make('password123'),
            'id_rol' => 1,
            'estado' => 1,
        ]);

        $invalidData = [
            'nombre' => 'Laura123', // Inválido por números
            'usuario' => 'lau',      // Inválido por min:4
            'correo' => 'correo-invalido',
            'id_rol' => 1,
            'estado' => 1,
        ];

        $response = $this->actAsAdmin()
                         ->putJson('/api/usuarios/' . $user->id_usuario, $invalidData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombre', 'usuario', 'correo']);
    }

    //cp 3.9
    public function test_cp03_9_inactivar_usuario()
    {
        $activeUser = Usuario::create([
            'nombre' => 'Usuario Activo',
            'correo' => 'activo_' . uniqid() . '@ejemplo.com',
            'usuario' => 'activo_' . uniqid(),
            'contrasena' => Hash::make('password123'),
            'id_rol' => 1,
            'estado' => 1,
        ]);

        $response = $this->actAsAdmin()
                         ->postJson('/api/usuarios/' . $activeUser->id_usuario . '/toggle-estado');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('usuarios', [
            'id_usuario' => $activeUser->id_usuario,
            'estado' => 0
        ]);
    }

    //cp 3.10
    public function test_cp03_10_reactivar_usuario()
    {
        $inactiveUser = Usuario::create([
            'nombre' => 'Usuario Inactivo',
            'correo' => 'inactivo_' . uniqid() . '@ejemplo.com',
            'usuario' => 'inactivo_' . uniqid(),
            'contrasena' => Hash::make('password123'),
            'id_rol' => 1,
            'estado' => 0,
        ]);

        $response = $this->actAsAdmin()
                         ->postJson('/api/usuarios/' . $inactiveUser->id_usuario . '/toggle-estado');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('usuarios', [
            'id_usuario' => $inactiveUser->id_usuario,
            'estado' => 1
        ]);
    }
}