<?php
// Conexión a la base de datos
require_once('conexion.php');
include('navbar.php');

// Parámetros de búsqueda y paginación
$search = $_GET['search'] ?? '';
$page   = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$limit  = isset($_GET['limit']) ? max(1, (int) $_GET['limit']) : 10;
$order  = $_GET['order'] ?? 'nombre';
$dir    = $_GET['dir'] ?? 'asc';

$validColumns = ['nombre', 'costo', 'stock'];
if (!in_array($order, $validColumns)) {
    $order = 'nombre';
}
$dir = $dir === 'desc' ? 'desc' : 'asc';

$offset = ($page - 1) * $limit;

// Construir cláusula WHERE
$where = '';
if ($search !== '') {
    $searchEsc = $conexion->real_escape_string($search);
    $where = "WHERE nombre LIKE '%$searchEsc%' OR costo LIKE '%$searchEsc%' OR stock LIKE '%$searchEsc%'";
}

// Obtener total de registros
$totalQuery = $conexion->query("SELECT COUNT(*) AS total FROM productos $where");
$total      = $totalQuery->fetch_assoc()['total'] ?? 0;
$totalPages = $limit > 0 ? (int) ceil($total / $limit) : 1;

// Consulta de productos
$query     = "SELECT * FROM productos $where ORDER BY $order $dir LIMIT $limit OFFSET $offset";
$resultado = $conexion->query($query);

// Helper para construir URLs conservando parámetros existentes
function build_query(array $params): string {
    $base        = $_GET;
    $base['vista'] = 'listar_productos';
    $query       = array_merge($base, $params);
    return 'index2.php?' . http_build_query($query);
}
?>

<div class="container my-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-white">
            <h3 class="mb-0">📦 Listado de Productos</h3>
        </div>
        <div class="card-body">

            <form class="mb-3" method="GET" action="index2.php">
                <input type="hidden" name="vista" value="listar_productos">
                <input type="hidden" name="order" value="<?= htmlspecialchars($order) ?>">
                <input type="hidden" name="dir" value="<?= htmlspecialchars($dir) ?>">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Buscar por nombre, costo o stock" value="<?= htmlspecialchars($search) ?>">
                    <select name="limit" class="form-select" style="max-width:120px;">
                        <?php foreach ([5, 10, 20, 50] as $opt): ?>
                            <option value="<?= $opt ?>" <?= $limit == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </form>

            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>
                            <a class="text-white text-decoration-none" href="<?= build_query(['order' => 'nombre', 'dir' => $order === 'nombre' && $dir === 'asc' ? 'desc' : 'asc', 'page' => 1]) ?>">
                                Nombre <?= $order === 'nombre' ? ($dir === 'asc' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
                        <th>
                            <a class="text-white text-decoration-none" href="<?= build_query(['order' => 'costo', 'dir' => $order === 'costo' && $dir === 'asc' ? 'desc' : 'asc', 'page' => 1]) ?>">
                                Costo <?= $order === 'costo' ? ($dir === 'asc' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
                        <th>
                            <a class="text-white text-decoration-none" href="<?= build_query(['order' => 'stock', 'dir' => $order === 'stock' && $dir === 'asc' ? 'desc' : 'asc', 'page' => 1]) ?>">
                                Stock <?= $order === 'stock' ? ($dir === 'asc' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
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
                            <button type="button" class="btn btn-success" onclick="Editar('<?= $fila['id_producto'] ?>')">Editar</button>
                            <button type="button" class="btn btn-danger" onclick="Eliminar('<?= $fila['id_producto'] ?>')">Eliminar</button>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <nav>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= build_query(['page' => $i]) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>

        </div>
    </div>
</div>
