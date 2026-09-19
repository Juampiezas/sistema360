<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'dompdf/vendor/autoload.php';

use Dompdf\Dompdf;

include("config/conexion.php");

$id = $_GET['id'];

$sql = mysqli_query(
    $conn,
    "SELECT ventas.id,
            ventas.cantidad,
            ventas.total,
            ventas.fecha,

            clientes.nombre AS cliente,

            productos.nombre AS producto,
            productos.precio

    FROM ventas

    INNER JOIN clientes
    ON ventas.cliente_id = clientes.id

    INNER JOIN productos
    ON ventas.producto_id = productos.id

    WHERE ventas.id='$id'"
);

$factura = mysqli_fetch_assoc($sql);

$html = '

<h1>Sistema 360 - Factura</h1>

<hr>

<h3>Cliente: '.$factura['cliente'].'</h3>

<h3>Producto: '.$factura['producto'].'</h3>

<h3>Precio: $'.$factura['precio'].'</h3>

<h3>Cantidad: '.$factura['cantidad'].'</h3>

<h2>Total: $'.$factura['total'].'</h2>

<p>Fecha: '.$factura['fecha'].'</p>

';

$dompdf = new Dompdf();

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream("factura.pdf");

?>