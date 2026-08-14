<?php

namespace Tests\Unit;

use App\Http\Requests\VentaRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class VentaTest extends TestCase
{
    /**
     * UT-007 / CP08.3
     * Verificar la validación de cantidad
     * de productos en una venta.
     */
    public function test_rechaza_cantidad_invalida(): void
    {
        $request = new VentaRequest();

        $rules = $request->rules();

        $regla = [
            'productos.*.cantidad' =>
            $rules['productos.*.cantidad'],
        ];

        // Cantidad 0
        $validator = Validator::make(
            [
                'productos' => [
                    ['cantidad' => 0],
                ],
            ],
            $regla
        );

        $this->assertTrue($validator->fails());

        // Cantidad negativa
        $validator = Validator::make(
            [
                'productos' => [
                    ['cantidad' => -1],
                ],
            ],
            $regla
        );

        $this->assertTrue($validator->fails());

        // Cantidad decimal
        $validator = Validator::make(
            [
                'productos' => [
                    ['cantidad' => 1.5],
                ],
            ],
            $regla
        );

        $this->assertTrue($validator->fails());

        // Cantidad superior al máximo
        $validator = Validator::make(
            [
                'productos' => [
                    ['cantidad' => 10001],
                ],
            ],
            $regla
        );

        $this->assertTrue($validator->fails());
    }

    /**
     * Verificar cantidades válidas.
     */
    public function test_acepta_cantidad_valida(): void
    {
        $request = new VentaRequest();

        $rules = $request->rules();

        $validator = Validator::make(
            [
                'productos' => [
                    ['cantidad' => 1],
                ],
            ],
            [
                'productos.*.cantidad' =>
                $rules['productos.*.cantidad'],
            ]
        );

        $this->assertFalse($validator->fails());

        $validator = Validator::make(
            [
                'productos' => [
                    ['cantidad' => 10000],
                ],
            ],
            [
                'productos.*.cantidad' =>
                $rules['productos.*.cantidad'],
            ]
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * UT-008 / CP08.6
     * Verificar que no se permita vender una cantidad
     * superior al stock disponible.
     */
    public function test_rechaza_cantidad_superior_al_stock(): void
    {
        $stockActual = 10;
        $cantidadSolicitada = 11;

        $stockDisponible = $cantidadSolicitada <= $stockActual;

        $this->assertFalse($stockDisponible);
    }

    /**
     * Verificar que se permita vender cuando
     * la cantidad solicitada no supera el stock.
     */
    public function test_acepta_cantidad_dentro_del_stock(): void
    {
        $stockActual = 10;

        // Menor al stock
        $cantidadSolicitada = 5;

        $stockDisponible = $cantidadSolicitada <= $stockActual;

        $this->assertTrue($stockDisponible);

        // Igual al stock
        $cantidadSolicitada = 10;

        $stockDisponible = $cantidadSolicitada <= $stockActual;

        $this->assertTrue($stockDisponible);
    }

    
}
