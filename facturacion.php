<?php

include("config/conexion.php");

$sql = "SELECT * FROM ventas";

$resultado = mysqli_query($conn, $sql);

?>

<h1>Facturas</h1>

<table>

<tr>
<th>ID</th>
<th>Fecha</th>
<th>Total</th>
</tr>

<?php while($row = mysqli_fetch_assoc($resultado)){ ?>

<tr>

<td><?= $row['id'] ?></td>
<td><?= $row['fecha'] ?></td>
<td><?= $row['total'] ?></td>

</tr>

<?php } ?>

</table>