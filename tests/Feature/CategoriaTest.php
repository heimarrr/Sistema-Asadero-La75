<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CategoriaTest extends TestCase
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

    //cp 5.1
    public function test_cp05_1_registrar_categoria_correctamente()
    {
        $data = [
            'nombre' => 'Bebidasss',
            'descripcion' => 'Gaseosas y jugos 100 naturales',
            'status' => 1,
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/categorias', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Categoría creada correctamente',
                     'data' => [
                         'nombre' => 'Bebidasss',
                         'descripcion' => 'Gaseosas y jugos 100 naturales',
                         'status' => 1,
                     ]
                 ]);

        $this->assertDatabaseHas('categorias', [
            'nombre' => 'Bebidasss'
        ]);
    }

    //cp 5.2
    public function test_cp05_2_registrar_categoria_con_campos_obligatorios_vacios()
    {
        $response = $this->actAsAdmin()
                         ->postJson('/api/categorias', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombre']);
    }

    //cp 5.3
    public function test_cp05_3_registrar_categoria_con_datos_invalidos()
    {
        $data = [
            'nombre' => 'Bebidas123', // Inválido por OnlyLetters y números
            'descripcion' => 'Descripción de prueba',
            'status' => 'no-booleano', // Inválido por boolean
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/categorias', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombre', 'status']);
    }

    //cp 5.4
    public function test_cp05_4_registrar_categoria_con_nombre_existente()
    {
        Categoria::create([
            'nombre' => 'Entradas',
            'descripcion' => 'Acompañamientos iniciales',
            'status' => 1,
        ]);

        $data = [
            'nombre' => 'Entradas', // Nombre duplicado
            'descripcion' => 'Otras entradas',
            'status' => 1,
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/categorias', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombre']);
    }

    //cp 5.5
    public function test_cp05_5_consultar_listado_de_categorias()
    {
        $response = $this->actAsAdmin()
                         ->getJson('/api/categorias');

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id_categoria', 'nombre', 'descripcion', 'status']
                     ]
                 ]);
    }

    //cp 5.6
    public function test_cp05_6_consultar_informacion_de_una_categoria()
    {
        $categoria = Categoria::create([
            'nombre' => 'Especiales',
            'descripcion' => 'Platos de la casa',
            'status' => 1,
        ]);

        $response = $this->actAsAdmin()
                         ->getJson('/api/categorias/' . $categoria->id_categoria);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id_categoria' => $categoria->id_categoria,
                         'nombre' => 'Especiales'
                     ]
                 ]);
    }

    //cp 5.7
    public function test_cp05_7_editar_categoria_correctamente()
    {
        $categoria = Categoria::create([
            'nombre' => 'Postres',
            'descripcion' => 'Dulces tradicionales',
            'status' => 1,
        ]);

        $updateData = [
            'nombre' => 'Reposteria',
            'descripcion' => 'Tortas y postres variados',
            'status' => 1,
        ];

        $response = $this->actAsAdmin()
                         ->putJson('/api/categorias/' . $categoria->id_categoria, $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Categoría actualizada correctamente'
                 ]);

        $this->assertDatabaseHas('categorias', [
            'id_categoria' => $categoria->id_categoria,
            'nombre' => 'Reposteria'
        ]);
    }

    //cp 5.8
    public function test_cp05_8_validar_datos_al_editar_categoria()
    {
        $categoria = Categoria::create([
            'nombre' => 'Asados',
            'descripcion' => 'Carnes al carbón',
            'status' => 1,
        ]);

        $invalidData = [
            'nombre' => 'As', // Inválido por min:3
            'status' => 'invalido'
        ];

        $response = $this->actAsAdmin()
                         ->putJson('/api/categorias/' . $categoria->id_categoria, $invalidData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombre', 'status']);
    }

    //cp 5.9
    public function test_cp05_9_cambiar_estado_de_categoria()
    {
        $categoria = Categoria::create([
            'nombre' => 'Licores',
            'descripcion' => 'Bebidas alcohólicas',
            'status' => 1,
        ]);

        $response = $this->actAsAdmin()
                         ->postJson('/api/categorias/' . $categoria->id_categoria . '/toggle-estado');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Estado de la categoría actualizado correctamente'
                 ]);

        $this->assertDatabaseHas('categorias', [
            'id_categoria' => $categoria->id_categoria,
            'status' => 0
        ]);
    }

    //cp 5.10
    public function test_cp05_10_eliminar_categoria_correctamente()
    {
        $categoria = Categoria::create([
            'nombre' => 'Adicionales',
            'descripcion' => 'Salsas y porciones extra',
            'status' => 1,
        ]);

        $response = $this->actAsAdmin()
                         ->deleteJson('/api/categorias/' . $categoria->id_categoria);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Categoría eliminada correctamente'
                 ]);

        $this->assertDatabaseMissing('categorias', [
            'id_categoria' => $categoria->id_categoria
        ]);
    }
}