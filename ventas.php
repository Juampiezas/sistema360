<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

include_once("config/conexion.php");

/* REGISTRAR VENTA */

if (isset($_POST['guardar'])) {

    $cliente_id = (int)($_POST['cliente_id'] ?? 0);
    $producto_id = (int)($_POST['producto_id'] ?? 0);
    $cantidad = (int)($_POST['cantidad'] ?? 0);

    /* VALIDACIONES BÁSICAS */

    if ($cliente_id <= 0 || $producto_id <= 0) {

        $mensaje = "Debe seleccionar un cliente y un producto.";
        $tipo_mensaje = "error";

    } elseif ($cantidad <= 0) {

        $mensaje = "Introduce una cantidad válida.";
        $tipo_mensaje = "error";

    } else {

        /* OBTENER PRODUCTO */

        $stmtProducto = mysqli_prepare(
            $conn,
            "SELECT precio, stock
             FROM productos
             WHERE id = ?"
        );

        if (!$stmtProducto) {

            $mensaje = "No fue posible consultar el producto.";
            $tipo_mensaje = "error";

            error_log(
                "Error preparando consulta de producto en ventas: " .
                mysqli_error($conn)
            );

        } else {

            mysqli_stmt_bind_param(
                $stmtProducto,
                "i",
                $producto_id
            );

            if (!mysqli_stmt_execute($stmtProducto)) {

                $mensaje = "No fue posible consultar el producto.";
                $tipo_mensaje = "error";

                error_log(
                    "Error ejecutando consulta de producto en ventas: " .
                    mysqli_stmt_error($stmtProducto)
                );

                mysqli_stmt_close($stmtProducto);

            } else {

                $resultadoProducto =
                    mysqli_stmt_get_result($stmtProducto);

                $rowProducto =
                    mysqli_fetch_assoc($resultadoProducto);

                mysqli_stmt_close($stmtProducto);

                if (!$rowProducto) {

                    $mensaje =
                        "El producto seleccionado no existe.";

                    $tipo_mensaje = "error";

                } else {

                    $precio =
                        (float)$rowProducto['precio'];

                    $stock_actual =
                        (int)$rowProducto['stock'];

                    /* VALIDAR STOCK */

                    if ($cantidad > $stock_actual) {

                        $mensaje =
                            "Stock insuficiente. Solo hay " .
                            $stock_actual .
                            " unidades disponibles.";

                        $tipo_mensaje = "error";

                    } else {

                        $total =
                            $precio * $cantidad;

                        mysqli_begin_transaction($conn);

                        try {

                            /* GUARDAR VENTA */

                            $stmtVenta = mysqli_prepare(
                                $conn,
                                "INSERT INTO ventas
                                (
                                    cliente_id,
                                    total
                                )
                                VALUES (?, ?)"
                            );

                            if (!$stmtVenta) {
                                throw new Exception(
                                    "Error preparando la venta."
                                );
                            }

                            mysqli_stmt_bind_param(
                                $stmtVenta,
                                "id",
                                $cliente_id,
                                $total
                            );

                            if (!mysqli_stmt_execute($stmtVenta)) {
                                throw new Exception(
                                    "Error registrando la venta."
                                );
                            }

                            /* OBTENER ID DE LA VENTA */

                            $venta_id =
                                mysqli_insert_id($conn);

                            mysqli_stmt_close($stmtVenta);

                            /* GUARDAR DETALLE */

                            $stmtDetalle = mysqli_prepare(
                                $conn,
                                "INSERT INTO detalle_ventas
                                (
                                    venta_id,
                                    producto_id,
                                    cantidad,
                                    subtotal
                                )
                                VALUES (?, ?, ?, ?)"
                            );

                            if (!$stmtDetalle) {
                                throw new Exception(
                                    "Error preparando el detalle de la venta."
                                );
                            }

                            mysqli_stmt_bind_param(
                                $stmtDetalle,
                                "iiid",
                                $venta_id,
                                $producto_id,
                                $cantidad,
                                $total
                            );

                            if (!mysqli_stmt_execute($stmtDetalle)) {
                                throw new Exception(
                                    "Error registrando el detalle de la venta."
                                );
                            }

                            mysqli_stmt_close($stmtDetalle);

                            /* ACTUALIZAR STOCK */

                            $stmtStock = mysqli_prepare(
                                $conn,
                                "UPDATE productos
                                 SET stock = stock - ?
                                 WHERE id = ?
                                 AND stock >= ?"
                            );

                            if (!$stmtStock) {
                                throw new Exception(
                                    "Error preparando la actualización del stock."
                                );
                            }

                            mysqli_stmt_bind_param(
                                $stmtStock,
                                "iii",
                                $cantidad,
                                $producto_id,
                                $cantidad
                            );

                            if (!mysqli_stmt_execute($stmtStock)) {
                                throw new Exception(
                                    "Error actualizando el stock."
                                );
                            }

                            if (
                                mysqli_stmt_affected_rows($stmtStock)
                                === 0
                            ) {
                                throw new Exception(
                                    "Stock insuficiente."
                                );
                            }

                            mysqli_stmt_close($stmtStock);

                            /* REGISTRAR SALIDA DE INVENTARIO */

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
                                VALUES (?, 'salida', ?, ?, 'venta')"
                            );

                            if (!$stmtMovimiento) {
                                throw new Exception(
                                    "Error preparando el movimiento de inventario."
                                );
                            }

                            mysqli_stmt_bind_param(
                                $stmtMovimiento,
                                "iii",
                                $producto_id,
                                $cantidad,
                                $venta_id
                            );

                            if (!mysqli_stmt_execute($stmtMovimiento)) {
                                throw new Exception(
                                    "Error registrando la salida de inventario."
                                );
                            }

                            mysqli_stmt_close($stmtMovimiento);

                            /* CONFIRMAR TRANSACCIÓN */

                            mysqli_commit($conn);

                            $mensaje =
                                "Venta registrada correctamente.";

                            $tipo_mensaje = "exito";

                        } catch (Throwable $e) {

                            mysqli_rollback($conn);

                            error_log(
                                "Error al registrar venta: " .
                                $e->getMessage()
                            );

                            $mensaje =
                                "Error al registrar la venta: " .
                                $e->getMessage();

                            $tipo_mensaje = "error";
                        }
                    }
                }
            }
        }
    }
}


/* LISTAR CLIENTES */

$clientes = mysqli_query(
    $conn,
    "SELECT id, nombre
     FROM clientes
     ORDER BY nombre ASC"
);


/* LISTAR PRODUCTOS */

$productos = mysqli_query(
    $conn,
    "SELECT id, nombre, precio, stock
     FROM productos
     ORDER BY nombre ASC"
);


/* HISTORIAL DE VENTAS */

$ventas = mysqli_query(
    $conn,
    "SELECT
        ventas.id,
        clientes.nombre AS cliente,
        productos.nombre AS producto,
        detalle_ventas.cantidad,
        ventas.total,
        ventas.fecha

    FROM ventas

    INNER JOIN clientes
        ON ventas.cliente_id = clientes.id

    INNER JOIN detalle_ventas
        ON detalle_ventas.venta_id = ventas.id

    INNER JOIN productos
        ON detalle_ventas.producto_id = productos.id

    ORDER BY ventas.id DESC"
);

if (!$ventas) {
    die(
        "Error en historial de ventas: "
        . mysqli_error($conn)
    );
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Ventas</title>

<link
    rel="stylesheet"
    href="css/styles.css"
>

</head>

<body>

<?php include_once("includes/sidebar.php"); ?>

<div class="main">

    <h1>Ventas</h1>

    <?php if (isset($mensaje)) { ?>

        <div style="
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            background:
                <?= $tipo_mensaje === 'exito'
                    ? '#d4edda'
                    : '#f8d7da' ?>;
            color:
                <?= $tipo_mensaje === 'exito'
                    ? '#155724'
                    : '#721c24' ?>;
        ">

            <?= htmlspecialchars($mensaje) ?>

        </div>

    <?php } ?>


    <div class="form-box">

        <form method="POST">

            <label for="cliente_id">
                Cliente
            </label>

            <select
                name="cliente_id"
                id="cliente_id"
                class="form-control"
                required
            >

                <option value="">
                    Seleccionar Cliente
                </option>

                <?php while (
                    $cliente =
                    mysqli_fetch_assoc($clientes)
                ) { ?>

                    <option
                        value="<?= $cliente['id'] ?>"
                    >
                        <?= htmlspecialchars(
                            $cliente['nombre']
                        ) ?>
                    </option>

                <?php } ?>

            </select>


            <label for="producto">
                Producto
            </label>

            <select
                name="producto_id"
                id="producto"
                class="form-control"
                onchange="mostrarStock()"
                required
            >

                <option value="">
                    Seleccionar Producto
                </option>

                <?php while (
                    $producto =
                    mysqli_fetch_assoc($productos)
                ) { ?>

                    <option
                        value="<?= $producto['id'] ?>"
                        data-stock="<?= $producto['stock'] ?>"
                        data-precio="<?= $producto['precio'] ?>"
                    >
                        <?= htmlspecialchars(
                            $producto['nombre']
                        ) ?>
                    </option>

                <?php } ?>

            </select>


            <label for="stock_disponible">
                Stock disponible
            </label>

            <input
                type="text"
                id="stock_disponible"
                class="form-control"
                placeholder="Stock disponible"
                readonly
            >


            <label for="precio_unitario">
                Precio unitario
            </label>

            <input
                type="text"
                id="precio_unitario"
                class="form-control"
                placeholder="Precio unitario"
                readonly
            >


            <label for="cantidad">
                Cantidad
            </label>

            <input
                type="number"
                name="cantidad"
                id="cantidad"
                class="form-control"
                placeholder="Cantidad"
                min="1"
                oninput="calcularTotal()"
                required
            >


            <label for="precio_total">
                Precio total
            </label>

            <input
                type="text"
                id="precio_total"
                class="form-control"
                placeholder="Precio total"
                readonly
            >


            <button
                type="submit"
                name="guardar"
                class="btn-login"
            >
                Registrar Venta
            </button>

        </form>

    </div>

    <br>


    <input
        type="text"
        id="buscador"
        class="form-control"
        placeholder="Buscar venta por ID, cliente o producto..."
    >


    <table
        class="tabla"
        id="tablaVentas"
    >

        <tr>

            <th>ID</th>
            <th>Cliente</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Total</th>
            <th>Fecha</th>
            <th>Factura</th>

        </tr>

        <?php while (
            $venta =
            mysqli_fetch_assoc($ventas)
        ) { ?>

            <tr>

                <td>
                    <?= htmlspecialchars(
                        $venta['id']
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $venta['cliente']
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $venta['producto']
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $venta['cantidad']
                    ) ?>
                </td>

                <td>
                    $<?= number_format(
                        $venta['total'],
                        2
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $venta['fecha']
                    ) ?>
                </td>

                <td>

                    <a
                        href="factura.php?id=<?= $venta['id'] ?>"
                        class="btn-edit"
                    >
                        Ver Factura
                    </a>

                </td>

            </tr>

        <?php } ?>

    </table>

</div>


<script>

function mostrarStock() {

    const select =
        document.getElementById("producto");

    const campoStock =
        document.getElementById(
            "stock_disponible"
        );

    const campoPrecio =
        document.getElementById(
            "precio_unitario"
        );

    const opcion =
        select.options[
            select.selectedIndex
        ];

    if (select.value === "") {

        campoStock.value = "";
        campoPrecio.value = "";

        calcularTotal();

        return;
    }

    const stock =
        opcion.getAttribute(
            "data-stock"
        );

    const precio =
        parseFloat(
            opcion.getAttribute(
                "data-precio"
            )
        );

    campoStock.value =
        "Stock disponible: " + stock;

    campoPrecio.value =
        "Precio unitario: $" +
        precio.toLocaleString(
            "en-US",
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );

    calcularTotal();
}


function calcularTotal() {

    const select =
        document.getElementById(
            "producto"
        );

    const cantidad =
        parseFloat(
            document.getElementById(
                "cantidad"
            ).value
        );

    const campoTotal =
        document.getElementById(
            "precio_total"
        );

    if (
        select.value === "" ||
        isNaN(cantidad) ||
        cantidad <= 0
    ) {

        campoTotal.value = "";
        return;
    }

    const opcion =
        select.options[
            select.selectedIndex
        ];

    const precio =
        parseFloat(
            opcion.getAttribute(
                "data-precio"
            )
        );

    const total =
        precio * cantidad;

    campoTotal.value =
        "Precio total: $" +
        total.toLocaleString(
            "en-US",
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );
}


/* BUSCADOR */

const buscador =
    document.getElementById("buscador");

buscador.addEventListener(
    "input",
    function () {

        const texto =
            this.value
                .toLowerCase()
                .trim();

        const filas =
            document.querySelectorAll(
                "#tablaVentas tr:not(:first-child)"
            );

        filas.forEach(
            function (fila) {

                const id =
                    fila.cells[0]
                        .textContent
                        .toLowerCase();

                const cliente =
                    fila.cells[1]
                        .textContent
                        .toLowerCase();

                const producto =
                    fila.cells[2]
                        .textContent
                        .toLowerCase();

                if (
                    id.includes(texto) ||
                    cliente.includes(texto) ||
                    producto.includes(texto)
                ) {

                    fila.style.display = "";

                } else {

                    fila.style.display = "none";
                }
            }
        );
    }
);

</script>

</body>
</html>