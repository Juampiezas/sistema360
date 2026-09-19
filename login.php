<?php

session_start();

include("config/conexion.php");

$error = "";

if(isset($_POST['login'])){

    $usuario = $_POST['usuario'];
    $clave = md5($_POST['clave']);

    $sql = "SELECT * FROM usuarios
            WHERE usuario='$usuario'
            AND clave='$clave'";

    $resultado = mysqli_query($conn, $sql);

    if(mysqli_num_rows($resultado) > 0){

    $row = mysqli_fetch_assoc($resultado);

    $_SESSION['usuario'] = $usuario;
    $_SESSION['rol'] = $row['rol'];

    header("Location: dashboard.php");

}
    else{

        $error = "Usuario o contraseña incorrectos";

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

        <?php if($error != ""){ ?>

            <div class="error">
                <?= $error ?>
            </div>

        <?php } ?>

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