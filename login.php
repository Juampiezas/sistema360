<?php

session_start();

include_once("config/conexion.php");

$error = "";

if (isset($_POST['login'])) {

    $usuario = trim($_POST['usuario']);
    $clave = $_POST['clave'];

    $sql = "SELECT id, usuario, clave, rol
            FROM usuarios
            WHERE usuario = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {

        mysqli_stmt_bind_param($stmt, "s", $usuario);
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($resultado)) {

            if (password_verify($clave, $row['clave'])) {

                session_regenerate_id(true);

                $_SESSION['usuario'] = $row['usuario'];
                $_SESSION['rol'] = $row['rol'];

                header("Location: dashboard.php");
                exit;

            } else {
                $error = "Usuario o contraseña incorrectos";
            }

        } else {
            $error = "Usuario o contraseña incorrectos";
        }

        mysqli_stmt_close($stmt);

    } else {

        error_log("Error al preparar consulta de login: " . mysqli_error($conn));
        $error = "No fue posible iniciar sesión. Intente nuevamente.";
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login - Sistema 360</title>

<link rel="stylesheet" href="css/styles.css">

</head>
<body>

<div class="login-container">

    <div class="login-box">

        <h1>Sistema 360</h1>

        <?php if ($error != "") { ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php } ?>

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

            <label for="clave">Contraseña</label>

            <input
                type="password"
                id="clave"
                name="clave"
                class="form-control"
                placeholder="Contraseña"
                required
            >

            <button
                type="submit"
                name="login"
                class="btn-login"
            >
                Iniciar Sesión
            </button>

        </form>

    </div>

</div>

</body>
</html>