<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

include("config/conexion.php");

/**
 * Verificar sesión
 */
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);

    echo json_encode([
        'success' => false,
        'message' => 'La sesión ha expirado.'
    ]);

    exit;
}

/**
 * Leer JSON enviado por compras.php
 */
$entrada = file_get_contents("php://input");
$datos = json_decode($entrada, true);

if (!is_array($datos)) {
    echo json_encode([
        'success' => false,
        'message' => 'No se recibieron datos válidos.'
    ]);

    exit;
}

/**
 * Obtener proveedor y productos
 */
$proveedor_id = intval($datos['proveedor_id'] ?? 0);
$productos = $datos['productos'] ?? [];

/**
 * Validaciones
 */
if ($proveedor_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Proveedor no válido.'
    ]);

    exit;
}

if (!is_array($productos) || count($productos) === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'El carrito está vacío.'
    ]);

    exit;
}

/**
 * Iniciar transacción
 */
mysqli_begin_transaction($conn);

try {

    foreach ($productos as $item) {

        $producto_id = intval($item['producto_id'] ?? 0);
        $cantidad = intval($item['cantidad'] ?? 0);
        $costo_unitario = floatval(
            $item['costo_unitario'] ?? 0
        );

        /**
         * Validar producto
         */
        if (
            $producto_id <= 0 ||
            $cantidad <= 0 ||
            $costo_unitario <= 0
        ) {
            throw new Exception(
                "Uno de los productos contiene datos inválidos."
            );
        }

        /**
         * Verificar que el producto realmente existe
         */
        $stmtProducto = mysqli_prepare(
            $conn,
            "SELECT id
             FROM productos
             WHERE id = ?"
        );

        if (!$stmtProducto) {
            throw new Exception(
                "Error preparando validación del producto: " .
                mysqli_error($conn)
            );
        }

        mysqli_stmt_bind_param(
            $stmtProducto,
            "i",
            $producto_id
        );

        mysqli_stmt_execute($stmtProducto);

        $resultadoProducto =
            mysqli_stmt_get_result($stmtProducto);

        if (mysqli_num_rows($resultadoProducto) === 0) {
            mysqli_stmt_close($stmtProducto);

            throw new Exception(
                "Uno de los productos no existe."
            );
        }

        mysqli_stmt_close($stmtProducto);

        /**
         * Calcular total de este producto
         */
        $total = $cantidad * $costo_unitario;

        /**
         * Registrar compra
         */
        $stmtCompra = mysqli_prepare(
            $conn,
            "INSERT INTO compras
            (
                proveedor_id,
                producto_id,
                cantidad,
                costo,
                costo_unitario
            )
            VALUES (?, ?, ?, ?, ?)"
        );

        if (!$stmtCompra) {
            throw new Exception(
                "Error preparando la compra: " .
                mysqli_error($conn)
            );
        }

        mysqli_stmt_bind_param(
            $stmtCompra,
            "iiidd",
            $proveedor_id,
            $producto_id,
            $cantidad,
            $total,
            $costo_unitario
        );

        if (!mysqli_stmt_execute($stmtCompra)) {
            throw new Exception(
                "Error registrando la compra: " .
                mysqli_stmt_error($stmtCompra)
            );
        }

        /**
         * Obtener ID de la compra recién registrada
         */
        $id_compra = mysqli_insert_id($conn);

        mysqli_stmt_close($stmtCompra);

        /**
         * Aumentar stock
         */
        $stmtStock = mysqli_prepare(
            $conn,
            "UPDATE productos
             SET stock = stock + ?
             WHERE id = ?"
        );

        if (!$stmtStock) {
            throw new Exception(
                "Error preparando actualización de stock: " .
                mysqli_error($conn)
            );
        }

        mysqli_stmt_bind_param(
            $stmtStock,
            "ii",
            $cantidad,
            $producto_id
        );

        if (!mysqli_stmt_execute($stmtStock)) {
            throw new Exception(
                "Error actualizando el stock: " .
                mysqli_stmt_error($stmtStock)
            );
        }

        mysqli_stmt_close($stmtStock);

        /**
         * Registrar movimiento de inventario
         */
        $stmtMovimiento = mysqli_prepare(
            $conn,
            "INSERT INTO movimientos_inventario
            (
                producto_id,
                tipo_movimiento,
                cantidad,
                referencia_id,
                origen
            )
            VALUES (?, 'entrada', ?, ?, 'compra')"
        );

        if (!$stmtMovimiento) {
            throw new Exception(
                "Error preparando el movimiento de inventario: " .
                mysqli_error($conn)
            );
        }

        mysqli_stmt_bind_param(
            $stmtMovimiento,
            "iii",
            $producto_id,
            $cantidad,
            $id_compra
        );

        if (!mysqli_stmt_execute($stmtMovimiento)) {
            throw new Exception(
                "Error registrando el movimiento de inventario: " .
                mysqli_stmt_error($stmtMovimiento)
            );
        }

        mysqli_stmt_close($stmtMovimiento);
    }

    /**
     * Todo salió correctamente
     */
    mysqli_commit($conn);

    echo json_encode([
        'success' => true,
        'message' => 'Compra registrada correctamente.'
    ]);

} catch (Throwable $e) {

    /**
     * Si algo falla, deshacer TODO
     */
    mysqli_rollback($conn);

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}