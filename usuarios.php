<?php
// Conexión (asegurate de tener $conexion configurado antes)
include('conexion.php');

$resultado = $conexion->query("SELECT id, usuario, fecha_ingreso FROM usuarios");
?>

<div class="container my-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-white">
            <h3 class="mb-0">👥 Listado de Usuarios Registrados</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Fecha de Ingreso</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= $fila['id'] ?></td>
                        <td><?= htmlspecialchars($fila['usuario']) ?></td>
                        <td><?= $fila['fecha_ingreso'] ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
