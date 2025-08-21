<?php
session_start();
include 'conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Primero obtener el nombre del producto para el mensaje
    $check_sql = "SELECT nombre FROM productos WHERE id_producto = ?";
    $check_stmt = $conexion->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        $_SESSION['mensaje_toastr'] = 'El producto no existe o ya fue eliminado';
        $_SESSION['tipo_toastr'] = 'error';
        header("Location: index2.php?vista=listar_productos");
        exit;
    }
    
    $producto = $check_result->fetch_assoc();
    $nombre_producto = $producto['nombre'];
    $check_stmt->close();
    
    // Proceder con la eliminación
    $sql = "DELETE FROM productos WHERE id_producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje_toastr'] = 'Producto "' . $nombre_producto . '" eliminado correctamente';
        $_SESSION['tipo_toastr'] = 'success';
    } else {
        $_SESSION['mensaje_toastr'] = 'Error al eliminar el producto';
        $_SESSION['tipo_toastr'] = 'error';
    }
    
    $stmt->close();
} else {
    $_SESSION['mensaje_toastr'] = 'ID de producto no especificado';
    $_SESSION['tipo_toastr'] = 'error';
}

$conexion->close();
header("Location: index2.php?vista=listar_productos");
exit;
?>