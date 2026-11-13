<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../clases/InventarioService.php';

final class InventarioTest extends TestCase
{
    private InventarioService $service;

    protected function setUp(): void
    {
        $this->service = new InventarioService();
    }

    public function testCantidadValida(): void
    {
        $this->assertTrue(
            $this->service->validarCantidad(6)
        );

        $this->assertFalse(
            $this->service->validarCantidad(0)
        );
    }

    public function testStockSuficiente(): void
    {
        $this->assertTrue(
            $this->service->hayStockSuficiente(14, 2)
        );
    }

    public function testStockInsuficiente(): void
    {
        $this->assertFalse(
            $this->service->hayStockSuficiente(14, 20)
        );
    }

    public function testTiposDeMovimientoPermitidos(): void
    {
        $this->assertTrue(
            $this->service->validarTipoMovimiento('entrada')
        );

        $this->assertTrue(
            $this->service->validarTipoMovimiento('salida')
        );

        $this->assertFalse(
            $this->service->validarTipoMovimiento('ajuste')
        );
    }

    public function testOrigenesPermitidos(): void
    {
        $this->assertTrue(
            $this->service->validarOrigen('compra')
        );

        $this->assertTrue(
            $this->service->validarOrigen('venta')
        );

        $this->assertFalse(
            $this->service->validarOrigen('otro')
        );
    }

    public function testCrearMovimientoEntrada(): void
    {
        $movimiento = $this->service->crearMovimiento(
            2,
            'entrada',
            6,
            3,
            'compra'
        );

        $this->assertSame(2, $movimiento['producto_id']);
        $this->assertSame('entrada', $movimiento['tipo_movimiento']);
        $this->assertSame(6, $movimiento['cantidad']);
        $this->assertSame(3, $movimiento['referencia_id']);
        $this->assertSame('compra', $movimiento['origen']);
    }

    public function testCrearMovimientoSalida(): void
    {
        $movimiento = $this->service->crearMovimiento(
            1,
            'salida',
            2,
            3,
            'venta'
        );

        $this->assertSame('salida', $movimiento['tipo_movimiento']);
        $this->assertSame('venta', $movimiento['origen']);
    }

    public function testCrearMovimientoConProductoInvalido(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->crearMovimiento(
            0,
            'entrada',
            1,
            1,
            'compra'
        );
    }

    public function testCrearMovimientoConCantidadInvalida(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->crearMovimiento(
            1,
            'entrada',
            0,
            1,
            'compra'
        );
    }

    public function testCrearMovimientoConTipoInvalido(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->crearMovimiento(
            1,
            'ajuste',
            1,
            1,
            'compra'
        );
    }

    public function testCrearMovimientoConOrigenInvalido(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->crearMovimiento(
            1,
            'entrada',
            1,
            1,
            'otro'
        );
    }

    public function testCrearMovimientoConReferenciaInvalida(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->crearMovimiento(
            1,
            'entrada',
            1,
            0,
            'compra'
        );
    }
}
