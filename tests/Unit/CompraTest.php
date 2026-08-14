<?php

namespace Tests\Unit;

use App\Http\Requests\CompraRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CompraTest extends TestCase
{
    /**
     * UT-003 / CP07.4
     * Verificar que la cantidad de productos
     * en una compra sea mayor a 0.
     */
    public function test_rechaza_cantidad_cero_o_negativa(): void
    {
        $request = new CompraRequest();

        $rules = $request->rules();

        $validator = Validator::make(
            [
                'productos' => [
                    [
                        'cantidad' => 0,
                    ],
                ],
            ],
            [
                'productos.*.cantidad' => $rules['productos.*.cantidad'],
            ]
        );

        $this->assertTrue($validator->fails());

        $this->assertTrue(
            $validator->errors()->has('productos.0.cantidad')
        );

        $validator = Validator::make(
            [
                'productos' => [
                    [
                        'cantidad' => -1,
                    ],
                ],
            ],
            [
                'productos.*.cantidad' => $rules['productos.*.cantidad'],
            ]
        );

        $this->assertTrue($validator->fails());
    }

    /**
     * Verificar que se acepten cantidades
     * mayores a 0.
     */
    public function test_acepta_cantidad_mayor_a_cero(): void
    {
        $request = new CompraRequest();

        $rules = $request->rules();

        $validator = Validator::make(
            [
                'productos' => [
                    [
                        'cantidad' => 0.01,
                    ],
                ],
            ],
            [
                'productos.*.cantidad' => $rules['productos.*.cantidad'],
            ]
        );

        $this->assertFalse($validator->fails());

        $validator = Validator::make(
            [
                'productos' => [
                    [
                        'cantidad' => 10,
                    ],
                ],
            ],
            [
                'productos.*.cantidad' => $rules['productos.*.cantidad'],
            ]
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * UT-004 / CP07.5
     * Verificar que el precio unitario de una compra
     * no pueda ser cero, negativo o superar el máximo permitido.
     */
    public function test_rechaza_precio_unitario_invalido(): void
    {
        $request = new CompraRequest();

        $rules = $request->rules();

        $validator = Validator::make(
            [
                'productos' => [
                    [
                        'precio_unitario' => 0,
                    ],
                ],
            ],
            [
                'productos.*.precio_unitario' =>
                $rules['productos.*.precio_unitario'],
            ]
        );

        $this->assertTrue($validator->fails());

        $validator = Validator::make(
            [
                'productos' => [
                    [
                        'precio_unitario' => -100,
                    ],
                ],
            ],
            [
                'productos.*.precio_unitario' =>
                $rules['productos.*.precio_unitario'],
            ]
        );

        $this->assertTrue($validator->fails());

        $validator = Validator::make(
            [
                'productos' => [
                    [
                        'precio_unitario' => 1000000,
                    ],
                ],
            ],
            [
                'productos.*.precio_unitario' =>
                $rules['productos.*.precio_unitario'],
            ]
        );

        $this->assertTrue($validator->fails());
    }

    /**
     * Verificar que se acepten precios unitarios
     * dentro del rango permitido.
     */
    public function test_acepta_precio_unitario_valido(): void
    {
        $request = new CompraRequest();

        $rules = $request->rules();

        $validator = Validator::make(
            [
                'productos' => [
                    [
                        'precio_unitario' => 0.01,
                    ],
                ],
            ],
            [
                'productos.*.precio_unitario' =>
                $rules['productos.*.precio_unitario'],
            ]
        );

        $this->assertFalse($validator->fails());

        $validator = Validator::make(
            [
                'productos' => [
                    [
                        'precio_unitario' => 50000,
                    ],
                ],
            ],
            [
                'productos.*.precio_unitario' =>
                $rules['productos.*.precio_unitario'],
            ]
        );

        $this->assertFalse($validator->fails());

        $validator = Validator::make(
            [
                'productos' => [
                    [
                        'precio_unitario' => 999999.99,
                    ],
                ],
            ],
            [
                'productos.*.precio_unitario' =>
                $rules['productos.*.precio_unitario'],
            ]
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * UT-005 / CP07.6
     * Verificar el cálculo correcto del subtotal
     * de un producto en una compra.
     */
    public function test_calcula_correctamente_el_subtotal(): void
    {
        $cantidad = 3;
        $precioUnitario = 2500;

        $subtotal = round(
            $cantidad * $precioUnitario,
            2
        );

        $this->assertEquals(7500, $subtotal);
    }

    /**
     * Verificar el redondeo del subtotal a dos decimales.
     */
    public function test_redondea_el_subtotal_a_dos_decimales(): void
    {
        $cantidad = 3;
        $precioUnitario = 10.555;

        $subtotal = round(
            $cantidad * $precioUnitario,
            2
        );

        $this->assertEquals(31.67, $subtotal);
    }

    /**
 * UT-006 / CP07.7
 * Verificar el cálculo correcto del total
 * de una compra con varios productos.
 */
public function test_calcula_correctamente_el_total(): void
{
    $productos = [
        [
            'cantidad' => 2,
            'precio_unitario' => 5000,
        ],
        [
            'cantidad' => 3,
            'precio_unitario' => 2500,
        ],
    ];

    $totalCalculado = 0;

    foreach ($productos as $item) {
        $totalCalculado += round(
            $item['cantidad'] * $item['precio_unitario'],
            2
        );
    }

    $this->assertEquals(17500, $totalCalculado);
}
}
