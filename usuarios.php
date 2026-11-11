<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

/* SOLO ADMIN */

if ($_SESSION['rol'] != 'admin') {
    die("Acceso denegado");
}

include_once("config/conexion.php");

$error = "";
$mensaje = "";

/* AGREGAR USUARIO */

if (isset($_POST['guardar'])) {

    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    $rolesPermitidos = ['admin', 'empleado'];

    if (!in_array($rol, $rolesPermitidos, true)) {
        $error = "Rol no válido";
    } elseif ($usuario === "" || $password === "") {
        $error = "Debe completar todos los campos";
    } else {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (usuario, clave, rol)
                VALUES (?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $usuario,
                $passwordHash,
                $rol
            );

            if (mysqli_stmt_execute($stmt)) {
                $mensaje = "Usuario creado correctamente";
            } else {
                error_log(
                    "Error al crear usuario: " .
                    mysqli_stmt_error($stmt)
                );

                $error = "No fue posible crear el usuario";
            }

            mysqli_stmt_close($stmt);

        } else {

            error_log(
                "Error al preparar INSERT de usuario: " .
                mysqli_error($conn)
            );

            $error = "No fue posible crear el usuario";
        }
    }
}

/* LISTAR USUARIOS */

$usuarios = mysqli_query(
    $conn,
    "SELECT id, usuario, rol FROM usuarios"
);

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Usuarios</title>

<link rel="stylesheet" href="css/styles.css">

</head>
<body>

<?php include_once("includes/sidebar.php"); ?>

<div class="main">

    <h1>Usuarios</h1>

    <?php if ($mensaje != "") { ?>

        <div class="success">
            <?= htmlspecialchars($mensaje) ?>
        </div>

    <?php } ?>

    <?php if ($error != "") { ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php } ?>

    <div class="form-box">

        <form method="POST">

            <label for="usuario">Usuario</label>

            <input
                type="text"
                id="usuario"
                name="usuario"
                class="form-control"
                placeholder="Usuario"
                required
            >

            <label for="password">Contraseña</label>

            <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                placeholder="Contraseña"
                required
            >

            <label for="rol">Rol</label>

            <select
                id="rol"
                name="rol"
                class="form-control"
                required
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

        <?php while ($row = mysqli_fetch_assoc($usuarios)) { ?>

        <tr>

            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['usuario']) ?></td>
            <td><?= htmlspecialchars($row['rol']) ?></td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>