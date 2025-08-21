<?php
if (!defined('HOST')) define('HOST', 'localhost');
if (!defined('USER')) define('USER', 'root');
if (!defined('PASS')) define('PASS', '');
if (!defined('DB'))   define('DB', 'fabrica_ba');

$conexion = new mysqli(HOST, USER, PASS, DB);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>
