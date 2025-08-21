<?php
// Verificar que se recibió un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index2.php?vista=listar_productos");
    exit;
}

$id = intval($_GET['id']);

// Obtener datos del producto
$sql = "SELECT * FROM productos WHERE id_producto = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<div class='alert alert-danger'>Producto no encontrado.</div>";
    exit;
}

$producto = $resultado->fetch_assoc();

// Procesar formulario si se envió
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $costo = $_POST['costo'];
    $stock = $_POST['stock'];

    if (empty($nombre) || empty($costo) || empty($stock)) {
        $_SESSION['mensaje_toastr'] = 'Todos los campos son obligatorios';
        $_SESSION['tipo_toastr'] = 'error';
    } else {
        $sql_update = "UPDATE productos SET nombre=?, costo=?, stock=? WHERE id_producto=?";
        $stmt_update = $conexion->prepare($sql_update);
        $stmt_update->bind_param("sdii", $nombre, $costo, $stock, $id);

        if ($stmt_update->execute()) {
            $_SESSION['mensaje_toastr'] = 'Producto "' . $nombre . '" actualizado correctamente';
            $_SESSION['tipo_toastr'] = 'success';
            
            // Redirigir para mostrar la notificación
            header("Location: index2.php?vista=listar_productos");
            exit;
        } else {
            $_SESSION['mensaje_toastr'] = 'Error al actualizar el producto';
            $_SESSION['tipo_toastr'] = 'error';
        }
        $stmt_update->close();
    }
}

$stmt->close();
?>

<div class="container my-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-white">
            <h3 class="mb-0">✏️ Editar Producto</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <input type="text" name="nombre" id="nombre" placeholder="Nombre del producto" 
                           value="<?= htmlspecialchars($producto['nombre']) ?>" required class="form-control">
                </div>
                <div class="mb-3">
                    <input type="number" step="0.01" name="costo" id="costo" placeholder="Costo" 
                           value="<?= $producto['costo'] ?>" required class="form-control">
                </div>
                <div class="mb-4">
                    <input type="number" name="stock" id="stock" placeholder="Stock" 
                           value="<?= $producto['stock'] ?>" required class="form-control">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">Guardar Cambios</button>
                    <a href="index2.php?vista=listar_productos" class="btn btn-secondary flex-fill">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>