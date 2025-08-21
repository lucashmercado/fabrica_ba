<?php
session_start();
include("conexion.php");

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM usuarios WHERE usuario = '$username' AND clave = '$password'";
$resultado = $conexion->query($sql);

if ($resultado->num_rows === 1) {
    $_SESSION['username'] = $username;
    $_SESSION['mensaje_toastr'] = 'Bienvenido, ' . $username;
    $_SESSION['tipo_toastr'] = 'success';
    header("Location: index2.php");
    exit;
} else {
    $_SESSION['mensaje_toastr'] = 'Usuario o contraseña incorrectos';
    $_SESSION['tipo_toastr'] = 'error';
    header("Location: login.php?error=Usuario o contraseña incorrectos");
    exit;
}

?>
