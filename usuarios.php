<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
}

/* SOLO ADMIN */

if($_SESSION['rol'] != 'admin'){
    die("Acceso denegado");
}

include("config/conexion.php");

/* AGREGAR USUARIO */

if(isset($_POST['guardar'])){

    $usuario = $_POST['usuario'];
    $password = md5($_POST['password']);
    $rol = $_POST['rol'];

    mysqli_query(
        $conn,
        "INSERT INTO usuarios(
            usuario,
            clave,
            rol
        )
        VALUES(
            '$usuario',
            '$password',
            '$rol'
        )"
    );

}

/* LISTAR USUARIOS */

$usuarios = mysqli_query(
    $conn,
    "SELECT * FROM usuarios"
);

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Usuarios</title>

<link rel="stylesheet" href="css/styles.css">

</head>
<body>

<?php include("includes/sidebar.php"); ?>

<div class="main">

    <h1>Usuarios</h1>

    <div class="form-box">

        <form method="POST">

            <input
                type="text"
                name="usuario"
                class="form-control"
                placeholder="Usuario"
                required
            >

            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Contraseña"
                required
            >

            <select
                name="rol"
                class="form-control"
            >

                <option value="admin">
                    Administrador
                </option>

                <option value="empleado">
                    Empleado
                </option>

            </select>

            <button
                type="submit"
                name="guardar"
                class="btn-login"
            >
                Crear Usuario
            </button>

        </form>

    </div>

    <br>

    <table class="tabla">

        <tr>

            <th>ID</th>
            <th>Usuario</th>
            <th>Rol</th>

        </tr>

        <?php while($row = mysqli_fetch_assoc($usuarios)){ ?>

        <tr>

            <td><?= $row['id'] ?></td>
            <td><?= $row['usuario'] ?></td>
            <td><?= $row['rol'] ?></td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>