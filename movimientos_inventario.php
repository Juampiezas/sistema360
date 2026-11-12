<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

include_once("config/conexion.php");

$producto_filtro = isset($_GET['producto_id'])
    ? (int)$_GET['producto_id']
    : 0;

$fecha_desde = trim($_GET['fecha_desde'] ?? '');
$fecha_hasta = trim($_GET['fecha_hasta'] ?? '');

/* VALIDAR FORMATO DE FECHAS */
function fechaValida($fecha)
{
    if ($fecha === '') {
        return true;
    }

    $d = DateTime::createFromFormat('Y-m-d', $fecha);

    return $d && $d->format('Y-m-d') === $fecha;
}

if (!fechaValida($fecha_desde)) {
    $fecha_desde = '';
}

if (!fechaValida($fecha_hasta)) {
    $fecha_hasta = '';
}

/* LISTAR PRODUCTOS PARA EL FILTRO */
$productos = mysqli_query(
    $conn,
    "SELECT id, nombre
     FROM productos
     ORDER BY nombre ASC"
);

if (!$productos) {
    die("Error al cargar productos.");
}

/* CONSTRUIR CONSULTA DEL HISTORIAL */
$sql = "SELECT
            mi.id,
            mi.producto_id,
            p.nombre AS producto,
            mi.tipo_movimiento,
            mi.cantidad,
            mi.referencia_id,
            mi.origen,
            mi.fecha
        FROM movimientos_inventario mi
        INNER JOIN productos p
            ON mi.producto_id = p.id
        WHERE 1 = 1";

$tipos = '';
$valores = [];

if ($producto_filtro > 0) {
    $sql .= " AND mi.producto_id = ?";
    $tipos .= 'i';
    $valores[] = $producto_filtro;
}

if ($fecha_desde !== '') {
    $sql .= " AND DATE(mi.fecha) >= ?";
    $tipos .= 's';
    $valores[] = $fecha_desde;
}

if ($fecha_hasta !== '') {
    $sql .= " AND DATE(mi.fecha) <= ?";
    $tipos .= 's';
    $valores[] = $fecha_hasta;
}

$sql .= " ORDER BY mi.fecha DESC, mi.id DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    error_log(
        "Error preparando historial de inventario: " .
        mysqli_error($conn)
    );

    die("No fue posible cargar el historial de inventario.");
}

if (!empty($valores)) {
    $referencias = [];
    $referencias[] = &$tipos;

    foreach ($valores as $indice => $valor) {
        $referencias[] = &$valores[$indice];
    }

    call_user_func_array(
        'mysqli_stmt_bind_param',
        array_merge([$stmt], $referencias)
    );
}

if (!mysqli_stmt_execute($stmt)) {
    error_log(
        "Error ejecutando historial de inventario: " .
        mysqli_stmt_error($stmt)
    );

    die("No fue posible cargar el historial de inventario.");
}

$movimientos = mysqli_stmt_get_result($stmt);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimientos de Inventario</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php include_once("includes/sidebar.php"); ?>

<div class="main">

    <h1>Movimientos de Inventario</h1>

    <div class="form-box" style="max-width: 100%; margin-bottom: 20px;">

        <form method="GET">

            <label for="producto_id">Producto</label>

            <select
                name="producto_id"
                id="producto_id"
                class="form-control"
            >
                <option value="0">Todos los productos</option>

                <?php while ($producto = mysqli_fetch_assoc($productos)) { ?>
                    <option
                        value="<?= (int)$producto['id'] ?>"
                        <?= $producto_filtro === (int)$producto['id']
                            ? 'selected'
                            : '' ?>
                    >
                        <?= htmlspecialchars($producto['nombre']) ?>
                    </option>
                <?php } ?>
            </select>

            <label for="fecha_desde">Fecha desde</label>

            <input
                type="date"
                name="fecha_desde"
                id="fecha_desde"
                class="form-control"
                value="<?= htmlspecialchars($fecha_desde) ?>"
            >

            <label for="fecha_hasta">Fecha hasta</label>

            <input
                type="date"
                name="fecha_hasta"
                id="fecha_hasta"
                class="form-control"
                value="<?= htmlspecialchars($fecha_hasta) ?>"
            >

            <button
                type="submit"
                class="btn-login"
                style="margin-bottom: 12px;"
            >
                Filtrar
            </button>

            <a
                href="movimientos_inventario.php"
                class="btn-edit"
                style="display: inline-block;"
            >
                Limpiar filtros
            </a>

        </form>

    </div>

    <table class="tabla">
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Producto</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Origen</th>
            <th>Referencia</th>
        </tr>

        <?php if (mysqli_num_rows($movimientos) > 0) { ?>

            <?php while ($movimiento = mysqli_fetch_assoc($movimientos)) { ?>
                <tr>
                    <td><?= (int)$movimiento['id'] ?></td>

                    <td>
                        <?= htmlspecialchars($movimiento['fecha']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($movimiento['producto']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            ucfirst($movimiento['tipo_movimiento'])
                        ) ?>
                    </td>

                    <td><?= (int)$movimiento['cantidad'] ?></td>

                    <td>
                        <?= htmlspecialchars(
                            ucfirst($movimiento['origen'])
                        ) ?>
                    </td>

                    <td>
                        #<?= (int)$movimiento['referencia_id'] ?>
                    </td>
                </tr>
            <?php } ?>

        <?php } else { ?>
            <tr>
                <td colspan="7">
                    No se encontraron movimientos con los filtros seleccionados.
                </td>
            </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>
