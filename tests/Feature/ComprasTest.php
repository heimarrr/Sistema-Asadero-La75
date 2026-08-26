<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ComprasTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Autenticar como Administrador (Rol 1)
     */
    private function actAsAdmin()
    {
        $admin = Usuario::create([
            'nombre' => 'Admin Compras',
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
     * Helper para crear un proveedor
     */
    private function createProveedor()
    {
        return Proveedor::create([
            'nombre' => 'Proveedor Test ' . uniqid(),
            'telefono' => '3001234567',
            'direccion' => 'Calle Falsa 123',
            'correo' => 'prov_' . uniqid() . '@test.com',
            'status' => 1,
        ]);
    }

    /**
     * Helper para crear un producto en inventario
     */
    private function createProducto($stock = 10, $nombre = null)
    {
        $categoria = Categoria::create([
            'nombre' => 'Categoria ' . uniqid(),
            'descripcion' => 'Descripción',
            'status' => 1,
        ]);

        return Producto::create([
            'nombre' => $nombre ?? ('Insumo Pollo ' . uniqid()),
            'descripcion' => 'Insumo de prueba',
            'stock_actual' => $stock,
            'unidad_medida' => 'Kilos',
            'precio_compra' => 12000,
            'precio_venta' => 0,
            'tipo' => 'insumo',
            'status' => 1,
            'id_categoria' => $categoria->id_categoria,
        ]);
    }

    public function test_cp07_1_registrar_compra_correctamente()
    {
        $proveedor = $this->createProveedor();
        $producto = $this->createProducto(5);

        $payload = [
            'id_proveedor' => $proveedor->getKey(),
            'fecha' => now()->toDateString(),
            'total_compra' => 120000,
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => 10,
                    'precio_unitario' => 12000,
                ]
            ],
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/compras', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Compra registrada correctamente.',
                 ]);
    }

    public function test_cp07_2_registrar_compra_sin_proveedor()
    {
        $producto = $this->createProducto(5);

        $payload = [
            'fecha' => now()->toDateString(),
            'total_compra' => 50000,
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => 5,
                    'precio_unitario' => 10000,
                ]
            ],
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/compras', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['id_proveedor']);
    }

    public function test_cp07_3_registrar_compra_sin_productos()
    {
        $proveedor = $this->createProveedor();

        $payload = [
            'id_proveedor' => $proveedor->getKey(),
            'fecha' => now()->toDateString(),
            'total_compra' => 0,
            'productos' => [],
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/compras', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['productos']);
    }

    public function test_cp07_4_registrar_compra_con_cantidad_invalida()
    {
        $proveedor = $this->createProveedor();
        $producto = $this->createProducto(5);

        $payload = [
            'id_proveedor' => $proveedor->getKey(),
            'fecha' => now()->toDateString(),
            'total_compra' => 0,
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => 0,
                    'precio_unitario' => 10000,
                ]
            ],
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/compras', $payload);

        $response->assertStatus(422);
    }

    public function test_cp07_5_registrar_compra_con_precio_invalido()
    {
        $proveedor = $this->createProveedor();
        $producto = $this->createProducto(5);

        $payload = [
            'id_proveedor' => $proveedor->getKey(),
            'fecha' => now()->toDateString(),
            'total_compra' => -2500,
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => 5,
                    'precio_unitario' => -500,
                ]
            ],
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/compras', $payload);

        $response->assertStatus(422);
    }

    public function test_cp07_6_calcular_subtotal_de_compra()
    {
        $proveedor = $this->createProveedor();
        $producto = $this->createProducto(0);

        $cantidad = 4;
        $precioUnitario = 15000;
        $subtotalEsperado = 60000;

        $payload = [
            'id_proveedor' => $proveedor->getKey(),
            'fecha' => now()->toDateString(),
            'total_compra' => $subtotalEsperado,
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                ]
            ],
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/compras', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('detalle_compras', [
            'id_producto' => $producto->getKey(),
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal' => $subtotalEsperado,
        ]);
    }

    public function test_cp07_7_calcular_total_de_compra()
    {
        $proveedor = $this->createProveedor();
        $producto1 = $this->createProducto(0);
        $producto2 = $this->createProducto(0);

        $payload = [
            'id_proveedor' => $proveedor->getKey(),
            'fecha' => now()->toDateString(),
            'total_compra' => 35000,
            'productos' => [
                [
                    'id_producto' => $producto1->getKey(),
                    'cantidad' => 2,
                    'precio_unitario' => 10000,
                ],
                [
                    'id_producto' => $producto2->getKey(),
                    'cantidad' => 3,
                    'precio_unitario' => 5000,
                ]
            ],
        ];

        $response = $this->actAsAdmin()
                         ->postJson('/api/compras', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('compras', [
            'id_proveedor' => $proveedor->getKey(),
            'total' => 35000,
        ]);
    }

    public function test_cp07_8_actualizar_stock_despues_de_registrar_compra()
    {
        $proveedor = $this->createProveedor();
        $stockInicial = 10;
        $cantidadComprada = 15;
        $producto = $this->createProducto($stockInicial);

        $payload = [
            'id_proveedor' => $proveedor->getKey(),
            'fecha' => now()->toDateString(),
            'total_compra' => 120000,
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => $cantidadComprada,
                    'precio_unitario' => 8000,
                ]
            ],
        ];

        $this->actAsAdmin()->postJson('/api/compras', $payload);

        $this->assertDatabaseHas('productos', [
            'id_producto' => $producto->getKey(),
            'stock_actual' => $stockInicial + $cantidadComprada,
        ]);
    }

    public function test_cp07_9_consultar_listado_de_compras()
    {
        $response = $this->actAsAdmin()
                         ->getJson('/api/compras');

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id_compra', 'total', 'id_proveedor', 'id_usuario']
                     ]
                 ]);
    }

    public function test_cp07_10_consultar_detalle_de_compra()
    {
        $proveedor = $this->createProveedor();
        $producto = $this->createProducto(5);

        $payload = [
            'id_proveedor' => $proveedor->getKey(),
            'fecha' => now()->toDateString(),
            'total_compra' => 20000,
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => 2,
                    'precio_unitario' => 10000,
                ]
            ],
        ];

        $createResponse = $this->actAsAdmin()->postJson('/api/compras', $payload);
        $idCompra = $createResponse->json('data.id_compra');

        $response = $this->actAsAdmin()->getJson('/api/compras/' . $idCompra);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id_compra' => $idCompra,
                     ]
                 ]);
    }

    public function test_cp07_11_anular_compra()
    {
        $proveedor = $this->createProveedor();
        $producto = $this->createProducto(5);

        $payload = [
            'id_proveedor' => $proveedor->getKey(),
            'fecha' => now()->toDateString(),
            'total_compra' => 50000,
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => 5,
                    'precio_unitario' => 10000,
                ]
            ],
        ];

        $createResponse = $this->actAsAdmin()->postJson('/api/compras', $payload);
        $idCompra = $createResponse->json('data.id_compra');

        $response = $this->actAsAdmin()->deleteJson('/api/compras/' . $idCompra);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Compra anulada correctamente.'
                 ]);

        $this->assertDatabaseHas('compras', [
            'id_compra' => $idCompra,
            'status' => 0,
        ]);
    }

    public function test_cp07_12_actualizar_stock_al_anular_compra()
    {
        $proveedor = $this->createProveedor();
        $stockInicial = 10;
        $cantidadComprada = 5;
        $producto = $this->createProducto($stockInicial);

        $payload = [
            'id_proveedor' => $proveedor->getKey(),
            'fecha' => now()->toDateString(),
            'total_compra' => 50000,
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => $cantidadComprada,
                    'precio_unitario' => 10000,
                ]
            ],
        ];

        $createResponse = $this->actAsAdmin()->postJson('/api/compras', $payload);
        $idCompra = $createResponse->json('data.id_compra');

        // Tras la compra el stock es 15
        $this->actAsAdmin()->deleteJson('/api/compras/' . $idCompra);

        // Tras la anulación el stock vuelve a ser 10
        $this->assertDatabaseHas('productos', [
            'id_producto' => $producto->getKey(),
            'stock_actual' => $stockInicial,
        ]);
    }

    public function test_cp07_13_evitar_anulacion_de_compra_ya_anulada()
    {
        $proveedor = $this->createProveedor();
        $producto = $this->createProducto(5);

        $payload = [
            'id_proveedor' => $proveedor->getKey(),
            'fecha' => now()->toDateString(),
            'total_compra' => 20000,
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => 2,
                    'precio_unitario' => 10000,
                ]
            ],
        ];

        $createResponse = $this->actAsAdmin()->postJson('/api/compras', $payload);
        $idCompra = $createResponse->json('data.id_compra');

        // Primera anulación
        $this->actAsAdmin()->deleteJson('/api/compras/' . $idCompra);

        // Segunda anulación rebotada por status != 1
        $response = $this->actAsAdmin()->deleteJson('/api/compras/' . $idCompra);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'message' => 'La compra ya está anulada.'
                 ]);
    }
}