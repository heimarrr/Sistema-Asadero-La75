<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VentasTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Autenticar como Cajero/Administrador
     */
    private function actAsUser()
    {
        $user = Usuario::create([
            'nombre' => 'Vendedor Test',
            'correo' => 'ventas_' . uniqid() . '@ejemplo.com',
            'usuario' => 'vendedor_' . uniqid(),
            'contrasena' => Hash::make('password123'),
            'id_rol' => 1,
            'estado' => 1,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->withHeader('Authorization', 'Bearer ' . $token);
    }

    /**
     * Helper para crear un producto en inventario
     */
    private function createProducto($stock = 20, $precioVenta = 15000, $nombre = null)
    {
        $categoria = Categoria::create([
            'nombre' => 'Categoria ' . uniqid(),
            'descripcion' => 'Descripción',
            'status' => 1,
        ]);

        return Producto::create([
            'nombre' => $nombre ?? ('Producto ' . uniqid()),
            'descripcion' => 'Producto de prueba',
            'stock_actual' => $stock,
            'unidad_medida' => 'Unidad',
            'precio_compra' => 8000,
            'precio_venta' => $precioVenta,
            'tipo' => 'venta',
            'status' => 1,
            'id_categoria' => $categoria->id_categoria,
        ]);
    }

    public function test_cp08_1_registrar_venta_correctamente()
    {
        $producto = $this->createProducto(20, 25000);

        $payload = [
            'fecha' => now()->format('Y-m-d'),
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => 2,
                ]
            ],
        ];

        $response = $this->actAsUser()
                         ->postJson('/api/ventas', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Venta registrada correctamente',
                 ]);
    }

    public function test_cp08_2_registrar_venta_sin_productos()
    {
        $payload = [
            'fecha' => now()->format('Y-m-d'),
            'productos' => [],
        ];

        $response = $this->actAsUser()
                         ->postJson('/api/ventas', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['productos']);
    }

    public function test_cp08_3_registrar_venta_con_producto_inexistente()
    {
        $payload = [
            'fecha' => now()->format('Y-m-d'),
            'productos' => [
                [
                    'id_producto' => 999999,
                    'cantidad' => 1,
                ]
            ],
        ];

        $response = $this->actAsUser()
                         ->postJson('/api/ventas', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['productos.0.id_producto']);
    }

    public function test_cp08_4_registrar_venta_con_cantidad_invalida()
    {
        $producto = $this->createProducto(10);

        $payload = [
            'fecha' => now()->format('Y-m-d'),
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => 0,
                ]
            ],
        ];

        $response = $this->actAsUser()
                         ->postJson('/api/ventas', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['productos.0.cantidad']);
    }

    public function test_cp08_5_registrar_venta_con_stock_insuficiente()
    {
        $producto = $this->createProducto(3); // Solo 3 en stock

        $payload = [
            'fecha' => now()->format('Y-m-d'),
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => 5, // Intenta vender 5
                ]
            ],
        ];

        $response = $this->actAsUser()
                         ->postJson('/api/ventas', $payload);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    public function test_cp08_6_descontar_stock_al_registrar_venta()
    {
        $stockInicial = 15;
        $cantidadVenta = 4;
        $producto = $this->createProducto($stockInicial);

        $payload = [
            'fecha' => now()->format('Y-m-d'),
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => $cantidadVenta,
                ]
            ],
        ];

        $this->actAsUser()->postJson('/api/ventas', $payload);

        $this->assertDatabaseHas('productos', [
            'id_producto' => $producto->getKey(),
            'stock_actual' => $stockInicial - $cantidadVenta,
        ]);
    }


    public function test_cp08_8_evitar_productos_duplicados_en_la_misma_venta()
    {
        $producto = $this->createProducto(20);

        $payload = [
            'fecha' => now()->format('Y-m-d'),
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => 2,
                ],
                [
                    'id_producto' => $producto->getKey(), // Producto repetido
                    'cantidad' => 1,
                ]
            ],
        ];

        $response = $this->actAsUser()
                         ->postJson('/api/ventas', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['productos']);
    }

    public function test_cp08_9_consultar_listado_de_ventas()
    {
        $response = $this->actAsUser()
                         ->getJson('/api/ventas');

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id_venta', 'total', 'id_usuario', 'status']
                     ]
                 ]);
    }

    public function test_cp08_10_consultar_detalle_de_venta()
    {
        $producto = $this->createProducto(10);

        $payload = [
            'fecha' => now()->format('Y-m-d'),
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => 2,
                ]
            ],
        ];

        $createResponse = $this->actAsUser()->postJson('/api/ventas', $payload);
        $idVenta = $createResponse->json('data.id_venta');

        $response = $this->actAsUser()->getJson('/api/ventas/' . $idVenta);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id_venta' => $idVenta,
                     ]
                 ]);
    }

    public function test_cp08_11_anular_venta_y_restaurar_stock()
    {
        $stockInicial = 20;
        $cantidadVendido = 5;
        $producto = $this->createProducto($stockInicial);

        $payload = [
            'fecha' => now()->format('Y-m-d'),
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => $cantidadVendido,
                ]
            ],
        ];

        $createResponse = $this->actAsUser()->postJson('/api/ventas', $payload);
        $idVenta = $createResponse->json('data.id_venta');

        // Anular la venta
        $response = $this->actAsUser()->deleteJson('/api/ventas/' . $idVenta);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Venta anulada correctamente',
                 ]);

        // Verificar restauración de stock
        $this->assertDatabaseHas('productos', [
            'id_producto' => $producto->getKey(),
            'stock_actual' => $stockInicial,
        ]);

        $this->assertDatabaseHas('ventas', [
            'id_venta' => $idVenta,
            'status' => 0,
        ]);
    }

    public function test_cp08_12_anular_venta_especial_pollo_asado_restaurar_stock_crudo()
    {
        $stockPolloAsado = 10;
        $stockPolloCrudo = 15;
        $cantidadVendido = 3;

        $polloAsado = $this->createProducto($stockPolloAsado, 35000, 'Pollo asado');
        $polloCrudo = $this->createProducto($stockPolloCrudo, 20000, 'Pollo crudo');

        $payload = [
            'fecha' => now()->format('Y-m-d'),
            'productos' => [
                [
                    'id_producto' => $polloAsado->getKey(),
                    'cantidad' => $cantidadVendido,
                ]
            ],
        ];

        $createResponse = $this->actAsUser()->postJson('/api/ventas', $payload);
        $idVenta = $createResponse->json('data.id_venta');

        // Anular la venta
        $this->actAsUser()->deleteJson('/api/ventas/' . $idVenta);

        // Verificar que el stock de ambos productos vuelva a sus niveles iniciales
        $this->assertDatabaseHas('productos', [
            'id_producto' => $polloAsado->getKey(),
            'stock_actual' => $stockPolloAsado,
        ]);

        $this->assertDatabaseHas('productos', [
            'id_producto' => $polloCrudo->getKey(),
            'stock_actual' => $stockPolloCrudo,
        ]);
    }

    public function test_cp08_13_evitar_anulacion_de_venta_ya_anulada()
    {
        $producto = $this->createProducto(10);

        $payload = [
            'fecha' => now()->format('Y-m-d'),
            'productos' => [
                [
                    'id_producto' => $producto->getKey(),
                    'cantidad' => 2,
                ]
            ],
        ];

        $createResponse = $this->actAsUser()->postJson('/api/ventas', $payload);
        $idVenta = $createResponse->json('data.id_venta');

        // Primera anulación
        $this->actAsUser()->deleteJson('/api/ventas/' . $idVenta);

        // Segunda anulación (debe fallar)
        $response = $this->actAsUser()->deleteJson('/api/ventas/' . $idVenta);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'message' => 'La venta ya está anulada',
                 ]);
    }
}