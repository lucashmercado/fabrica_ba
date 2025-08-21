<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];
    $fecha = date('Y-m-d');

    $sql = "INSERT INTO usuarios (usuario, clave, fecha_ingreso) VALUES ('$usuario', '$clave', '$fecha')";
    
    if ($conexion->query($sql) === TRUE) {
        header("Location: login.php");
        exit;
    } else {
        echo "Error al registrar: " . $conexion->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="login-box">
        <h2>Registrarse</h2>
        <form method="POST" action="registrar.php">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="clave" placeholder="Contraseña" required>
            <button type="submit">Registrar</button>
            <p>¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a></p>
        </form>
    </div>
</body>
</html>
