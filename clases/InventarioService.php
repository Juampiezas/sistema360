<?php

class InventarioService
{
    public function validarCantidad(int $cantidad): bool
    {
        return $cantidad > 0;
    }

    public function hayStockSuficiente(
        int $stockDisponible,
        int $cantidadSolicitada
    ): bool {
        return $cantidadSolicitada > 0
            && $stockDisponible >= $cantidadSolicitada;
    }

    public function validarTipoMovimiento(string $tipo): bool
    {
        return in_array($tipo, ['entrada', 'salida'], true);
    }

    public function validarOrigen(string $origen): bool
    {
        return in_array($origen, ['compra', 'venta'], true);
    }

    public function crearMovimiento(
        int $productoId,
        string $tipo,
        int $cantidad,
        int $referenciaId,
        string $origen
    ): array {
        if ($productoId <= 0) {
            throw new InvalidArgumentException('Producto no válido.');
        }

        if (!$this->validarCantidad($cantidad)) {
            throw new InvalidArgumentException(
                'La cantidad debe ser mayor que cero.'
            );
        }

        if (!$this->validarTipoMovimiento($tipo)) {
            throw new InvalidArgumentException(
                'Tipo de movimiento no válido.'
            );
        }

        if (!$this->validarOrigen($origen)) {
            throw new InvalidArgumentException(
                'Origen de movimiento no válido.'
            );
        }

        if ($referenciaId <= 0) {
            throw new InvalidArgumentException(
                'Referencia no válida.'
            );
        }

        return [
            'producto_id' => $productoId,
            'tipo_movimiento' => $tipo,
            'cantidad' => $cantidad,
            'referencia_id' => $referenciaId,
            'origen' => $origen
        ];
    }
}
