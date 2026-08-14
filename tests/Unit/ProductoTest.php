<?php

namespace Tests\Unit;

use App\Http\Requests\ProductoRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ProductoTest extends TestCase
{
    /**
     * UT-001 / CP04.4
     * Verificar que los precios de producto
     * no puedan ser negativos.
     */
    public function test_rechaza_precio_negativo(): void
    {
        $request = new ProductoRequest();

        $validator = Validator::make(
            [
                'precio_compra' => -5000,
                'precio_venta' => -10000,
            ],
            $request->rules()
        );

        $this->assertTrue($validator->fails());

        $this->assertTrue(
            $validator->errors()->has('precio_compra')
        );

        $this->assertTrue(
            $validator->errors()->has('precio_venta')
        );
    }

    /**
     * Verificar que se acepten precios
     * iguales o mayores a cero.
     */
    /**
     * Verificar que se acepten precios
     * iguales o mayores a cero.
     */
    public function test_acepta_precio_cero_y_positivo(): void
    {
        $request = new ProductoRequest();

        $rules = $request->rules();

        $validator = Validator::make(
            [
                'precio_compra' => 0,
                'precio_venta' => 15000,
            ],
            [
                'precio_compra' => $rules['precio_compra'],
                'precio_venta' => $rules['precio_venta'],
            ]
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * UT-002 / CP04.5
     * Verificar que no se permita stock negativo.
     */
    public function test_rechaza_stock_negativo(): void
    {
        $request = new ProductoRequest();

        $rules = $request->rules();

        $validator = Validator::make(
            [
                'stock_actual' => -1,
            ],
            [
                'stock_actual' => $rules['stock_actual'],
            ]
        );

        $this->assertTrue($validator->fails());

        $this->assertTrue(
            $validator->errors()->has('stock_actual')
        );
    }

    /**
     * Verificar que se permita stock igual o mayor a cero.
     */
    public function test_acepta_stock_cero_y_positivo(): void
    {
        $request = new ProductoRequest();

        $rules = $request->rules();

        $validator = Validator::make(
            [
                'stock_actual' => 0,
            ],
            [
                'stock_actual' => $rules['stock_actual'],
            ]
        );

        $this->assertFalse($validator->fails());

        $validator = Validator::make(
            [
                'stock_actual' => 10,
            ],
            [
                'stock_actual' => $rules['stock_actual'],
            ]
        );

        $this->assertFalse($validator->fails());
    }
}
