<?php
// Conexión a la base de datos
require_once('conexion.php');

// Consulta de productos
$resultado = $conexion->query("SELECT * FROM productos");
?>

<div class="container my-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-white">
            <h3 class="mb-0">📦 Listado de Productos</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Costo</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($fila = $resultado->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['nombre']) ?></td>
                        <td>$<?= $fila['costo'] ?></td>
                        <td><?= $fila['stock'] ?></td>
                        <td class="acciones">
                        <button type='button' class='btn btn-success' onclick="Editar('<?= $fila['id_producto'] ?>')">Editar</button>
                        <button type='button' class='btn btn-danger' onclick="Eliminar('<?= $fila['id_producto'] ?>')">Eliminar</button>
                        </td>                        
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
