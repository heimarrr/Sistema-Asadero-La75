<?php

namespace Tests\Feature;

use App\Models\Proveedor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProveedorTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Helper para autenticar las peticiones como Administrador (Rol 1)
     */
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

    public function test_cp06_1_registrar_proveedor_correctamente()
    {
        $data = [
            'nombre' => 'Distribuidora Avicola',
            'telefono' => '3101234567',
            'direccion' => 'Calle 45 No 12 34',
            'correo' => 'contacto_' . uniqid() . '@ejemplo.com',
            'status' => 1,
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/proveedores', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Proveedor creado correctamente',
                     'data' => [
                         'nombre' => 'Distribuidora Avicola',
                         'telefono' => '3101234567',
                     ]
                 ]);

        $this->assertDatabaseHas('proveedores', [
            'nombre' => 'Distribuidora Avicola',
        ]);
    }

    public function test_cp06_2_registrar_proveedor_con_campos_obligatorios_vacios()
    {
        $response = $this->actAsAdmin()
                         ->postJson('/api/proveedores', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombre']);
    }

    public function test_cp06_3_registrar_proveedor_con_datos_invalidos()
    {
        $data = [
            'nombre' => 'Pr',                  // Inválido por min:3
            'telefono' => 'texto_no_numero',   // Inválido por regla Phone
            'correo' => 'no-es-un-correo',     // Inválido por email
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/proveedores', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombre', 'telefono', 'correo']);
    }

    public function test_cp06_4_registrar_proveedor_duplicado()
    {
        $nombreExistente = 'Proveedor Unico';

        Proveedor::create([
            'nombre' => $nombreExistente,
            'telefono' => '3209876543',
            'direccion' => 'Carrera 10 No 5',
            'correo' => 'existente@correo.com',
            'status' => 1,
        ]);

        $data = [
            'nombre' => $nombreExistente, // Intenta repetir nombre
            'telefono' => '3110001122',
            'direccion' => 'Calle 100',
            'correo' => 'nuevo@correo.com',
            'status' => 1,
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/proveedores', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombre']);
    }

    public function test_cp06_5_consultar_listado_de_proveedores()
    {
        $response = $this->actAsAdmin()
                         ->getJson('/api/proveedores');

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id_proveedor', 'nombre', 'telefono', 'direccion', 'correo', 'status']
                     ]
                 ]);
    }

    public function test_cp06_6_consultar_informacion_de_proveedor()
    {
        $proveedor = Proveedor::create([
            'nombre' => 'Avícola San Marino',
            'telefono' => '3157778899',
            'direccion' => 'Av Cali Calle 80',
            'correo' => 'sanmarino_' . uniqid() . '@correo.com',
            'status' => 1,
        ]);

        $response = $this->actAsAdmin()
                         ->getJson('/api/proveedores/' . $proveedor->getKey());

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'nombre' => 'Avícola San Marino',
                     ]
                 ]);
    }

    public function test_cp06_7_editar_proveedor_correctamente()
    {
        $proveedor = Proveedor::create([
            'nombre' => 'Proveedor Original',
            'telefono' => '3001112233',
            'direccion' => 'Diagonal 15 No 4',
            'correo' => 'original_' . uniqid() . '@correo.com',
            'status' => 1,
        ]);

        $updateData = [
            'nombre' => 'Proveedor Editado',
            'telefono' => '3009998877',
            'direccion' => 'Nueva Direccion Calle 50',
            'correo' => $proveedor->correo,
            'status' => 1,
        ];

        $response = $this->actAsAdmin()
                         ->putJson('/api/proveedores/' . $proveedor->getKey(), $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Proveedor actualizado correctamente'
                 ]);

        $this->assertDatabaseHas('proveedores', [
            'id_proveedor' => $proveedor->getKey(),
            'nombre' => 'Proveedor Editado'
        ]);
    }

    public function test_cp06_8_validar_datos_al_editar_proveedor()
    {
        $proveedor = Proveedor::create([
            'nombre' => 'Insumos del Campo',
            'telefono' => '3124445566',
            'direccion' => 'Zona Industrial',
            'correo' => 'campo_' . uniqid() . '@correo.com',
            'status' => 1,
        ]);

        $invalidData = [
            'nombre' => 'Pr',               // Inválido por min:3
            'telefono' => 'invalido',       // Inválido por Phone
            'correo' => 'correo-invalido'   // Inválido por email
        ];

        $response = $this->actAsAdmin()
                         ->putJson('/api/proveedores/' . $proveedor->getKey(), $invalidData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombre', 'telefono', 'correo']);
    }

    public function test_cp06_9_inactivar_proveedor()
    {
        $proveedor = Proveedor::create([
            'nombre' => 'Proveedor Activo',
            'telefono' => '3180000000',
            'direccion' => 'Bodega Principal',
            'correo' => 'activo_' . uniqid() . '@correo.com',
            'status' => 1,
        ]);

        $response = $this->actAsAdmin()
                         ->postJson('/api/proveedores/' . $proveedor->getKey() . '/toggle-estado');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('proveedores', [
            'id_proveedor' => $proveedor->getKey(),
            'status' => 0
        ]);
    }

    public function test_cp06_10_reactivar_proveedor()
    {
        $proveedor = Proveedor::create([
            'nombre' => 'Proveedor Inactivo',
            'telefono' => '3190000000',
            'direccion' => 'Bodega Secundaria',
            'correo' => 'inactivo_' . uniqid() . '@correo.com',
            'status' => 0,
        ]);

        $response = $this->actAsAdmin()
                         ->postJson('/api/proveedores/' . $proveedor->getKey() . '/toggle-estado');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('proveedores', [
            'id_proveedor' => $proveedor->getKey(),
            'status' => 1
        ]);
    }
}