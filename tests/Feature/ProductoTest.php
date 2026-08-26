<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProductoTest extends TestCase
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

    /**
     * Helper para crear una categoría en la base de datos
     */
    private function createCategoria()
    {
        return Categoria::create([
            'nombre' => 'Categoria ' . uniqid(),
            'descripcion' => 'Descripción de prueba',
            'status' => 1,
        ]);
    }

    //cp 4.1
    public function test_cp04_1_registrar_producto_correctamente()
    {
        $categoria = $this->createCategoria();

        $data = [
            'nombre' => 'Pollo Asado Entero',
            'descripcion' => 'Pollo marinado servido con papas y arepa',
            'stock_actual' => 15,
            'unidad_medida' => 'Unidades',
            'precio_compra' => 20000,
            'precio_venta' => 35000,
            'tipo' => 'venta',
            'status' => 1,
            'id_categoria' => $categoria->id_categoria,
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/productos', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Producto creado correctamente',
                     'data' => [
                         'nombre' => 'Pollo Asado Entero',
                         'stock_actual' => 15,
                         'unidad_medida' => 'Unidades',
                         'tipo' => 'venta',
                         'id_categoria' => $categoria->id_categoria,
                     ]
                 ]);

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Pollo Asado Entero',
            'precio_venta' => 35000,
        ]);
    }

    //cp 4.2
    public function test_cp04_2_registrar_producto_con_campos_obligatorios_vacios()
    {
        $response = $this->actAsAdmin()
                         ->postJson('/api/productos', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'nombre',
                     'stock_actual',
                     'unidad_medida',
                     'tipo',
                     'id_categoria'
                 ]);
    }

    //cp 4.3
    public function test_cp04_3_registrar_producto_con_datos_invalidos()
    {
        $data = [
            'nombre' => 'Po',               // Inválido por min:3
            'stock_actual' => -5,          // Inválido por min:0
            'unidad_medida' => 'Unidades',
            'precio_compra' => -100,       // Inválido por min:0
            'precio_venta' => 'no-numero', // Inválido por numeric
            'tipo' => 'otro_tipo',         // Inválido por in:insumo,venta
            'id_categoria' => 99999,       // Inválido por exists:categorias
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/productos', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'nombre',
                     'stock_actual',
                     'precio_compra',
                     'precio_venta',
                     'tipo',
                     'id_categoria'
                 ]);
    }

    //cp 4.4
    public function test_cp04_4_consultar_listado_de_productos()
    {
        $response = $this->actAsAdmin()
                         ->getJson('/api/productos');

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'nombre',
                             'stock_actual',
                             'unidad_medida',
                             'precio_compra',
                             'precio_venta',
                             'tipo',
                             'status',
                             'id_categoria',
                             'categoria'
                         ]
                     ]
                 ]);
    }

    //cp 4.5
    public function test_cp04_5_consultar_informacion_de_un_producto()
    {
        $categoria = $this->createCategoria();

        $producto = Producto::create([
            'nombre' => 'Gaseosa 1.5L',
            'descripcion' => 'Sabor a elección',
            'stock_actual' => 30,
            'unidad_medida' => 'Botellas',
            'precio_compra' => 4000,
            'precio_venta' => 7000,
            'tipo' => 'venta',
            'status' => 1,
            'id_categoria' => $categoria->id_categoria,
        ]);

        $response = $this->actAsAdmin()
                         ->getJson('/api/productos/' . $producto->getKey());

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'nombre' => 'Gaseosa 1.5L',
                         'tipo' => 'venta',
                     ]
                 ]);
    }

    //cp 4.6
    public function test_cp04_6_editar_producto_correctamente()
    {
        $categoria = $this->createCategoria();

        $producto = Producto::create([
            'nombre' => 'Papas Fritas',
            'descripcion' => 'Porción mediana',
            'stock_actual' => 50,
            'unidad_medida' => 'Porciones',
            'precio_compra' => 2000,
            'precio_venta' => 5000,
            'tipo' => 'venta',
            'status' => 1,
            'id_categoria' => $categoria->id_categoria,
        ]);

        $updateData = [
            'nombre' => 'Papas Fritas Grandes',
            'descripcion' => 'Porción grande',
            'stock_actual' => 40,
            'unidad_medida' => 'Porciones',
            'precio_compra' => 3000,
            'precio_venta' => 8000,
            'tipo' => 'venta',
            'status' => 1,
            'id_categoria' => $categoria->id_categoria,
        ];

        $response = $this->actAsAdmin()
                         ->putJson('/api/productos/' . $producto->getKey(), $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Producto actualizado correctamente'
                 ]);

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Papas Fritas Grandes',
            'precio_venta' => 8000
        ]);
    }

    //cp 4.7
    public function test_cp04_7_validar_datos_al_editar_producto()
    {
        $categoria = $this->createCategoria();

        $producto = Producto::create([
            'nombre' => 'Carbón vegetal',
            'descripcion' => 'Bolsa de 5kg',
            'stock_actual' => 20,
            'unidad_medida' => 'Bolsas',
            'precio_compra' => 15000,
            'precio_venta' => 0,
            'tipo' => 'insumo',
            'status' => 1,
            'id_categoria' => $categoria->id_categoria,
        ]);

        $invalidData = [
            'nombre' => 'Ca',       // Inválido por min:3
            'stock_actual' => -10, // Inválido por min:0
            'unidad_medida' => 'Bolsas',
            'tipo' => 'invalido',  // Inválido por in
            'id_categoria' => $categoria->id_categoria,
        ];

        $response = $this->actAsAdmin()
                         ->putJson('/api/productos/' . $producto->getKey(), $invalidData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombre', 'stock_actual', 'tipo']);
    }

    //cp 4.8
    public function test_cp04_8_cambiar_estado_de_producto()
    {
        $categoria = $this->createCategoria();

        $producto = Producto::create([
            'nombre' => 'Cerveza Club Colombia',
            'descripcion' => 'Lata 330ml',
            'stock_actual' => 24,
            'unidad_medida' => 'Latas',
            'precio_compra' => 2500,
            'precio_venta' => 5000,
            'tipo' => 'venta',
            'status' => 1,
            'id_categoria' => $categoria->id_categoria,
        ]);

        $response = $this->actAsAdmin()
                         ->postJson('/api/productos/' . $producto->getKey() . '/toggle-estado');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Estado del producto actualizado correctamente'
                 ]);

        $this->assertDatabaseHas('productos', [
            'status' => 0
        ]);
    }

    //cp 4.9
    public function test_cp04_9_eliminar_producto_correctamente()
    {
        $categoria = $this->createCategoria();

        $producto = Producto::create([
            'nombre' => 'Servilletas Paquete',
            'descripcion' => 'Insumo de mesa',
            'stock_actual' => 10,
            'unidad_medida' => 'Paquetes',
            'precio_compra' => 3000,
            'precio_venta' => 0,
            'tipo' => 'insumo',
            'status' => 1,
            'id_categoria' => $categoria->id_categoria,
        ]);

        $response = $this->actAsAdmin()
                         ->deleteJson('/api/productos/' . $producto->getKey());

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Producto eliminado correctamente'
                 ]);
    }
}