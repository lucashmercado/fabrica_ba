<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $costo = $_POST['costo'];
    $stock = $_POST['stock'];

    // Validar que los campos no estén vacíos
    if (empty($nombre) || empty($costo) || empty($stock)) {
        $_SESSION['mensaje_toastr'] = 'Todos los campos son obligatorios';
        $_SESSION['tipo_toastr'] = 'error';
    } else {
        // Usar consulta preparada
        $sql = "INSERT INTO productos (nombre, costo, stock) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sdi", $nombre, $costo, $stock);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje_toastr'] = 'Producto "' . $nombre . '" agregado correctamente';
            $_SESSION['tipo_toastr'] = 'success';
            
            // Limpiar el formulario redirigiendo
            header("Location: index2.php?vista=agregar_producto");
            exit;
        } else {
            $_SESSION['mensaje_toastr'] = 'Error al agregar el producto';
            $_SESSION['tipo_toastr'] = 'error';
        }
        $stmt->close();
    }
}
?>

<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-white">
            <h3 class="mb-0">➕ Agregar Producto</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <input type="text" name="nombre" id="nombre" placeholder="Nombre del producto" required class="form-control">
                </div>
                <div class="mb-3">
                    <input type="number" step="0.01" name="costo" id="costo" placeholder="Costo" required class="form-control">
                </div>
                <div class="mb-4">
                    <input type="number" name="stock" id="stock" placeholder="Stock" required class="form-control">
                </div>
                <button type="submit" class="btn btn-success w-100">Agregar Producto</button>
            </form>
        </div>
    </div>
</div>
