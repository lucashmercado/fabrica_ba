<?php
// Procesar importación
$no_encontrados = $_SESSION['no_encontrados'] ?? [];
unset($_SESSION['no_encontrados']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    require __DIR__ . '/vendor/autoload.php';

    $archivo = $_FILES['archivo'];
    $tmp      = $archivo['tmp_name'];
    $ext      = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

    $actualizados = 0;
    $omitidos     = [];
    $log_entries  = [];

    if ($ext === 'xlsx') {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tmp);
        foreach ($spreadsheet->getActiveSheet()->toArray() as $fila) {
            if (count($fila) < 2) {
                continue;
            }
            $id    = trim($fila[0]);
            if ($id === '' || preg_match('/^(id_producto|nombre)$/i', $id)) {
                continue; // encabezado
            }
            $costo = trim($fila[1]);
            $stock = isset($fila[2]) ? trim($fila[2]) : null;
            procesar_producto($id, $costo, $stock, $actualizados, $omitidos, $log_entries, $conexion);
        }
    } elseif ($ext === 'pdf') {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($tmp);
        $text   = $pdf->getText();
        $lineas = explode("\n", $text);
        foreach ($lineas as $linea) {
            $partes = preg_split('/\s+/', trim($linea));
            if (count($partes) < 2) {
                continue;
            }
            $id    = $partes[0];
            if (preg_match('/^(id_producto|nombre)$/i', $id)) {
                continue;
            }
            $costo = $partes[1];
            $stock = $partes[2] ?? null;
            procesar_producto($id, $costo, $stock, $actualizados, $omitidos, $log_entries, $conexion);
        }
    } else {
        $_SESSION['mensaje_toastr'] = 'Formato de archivo no permitido.';
        $_SESSION['tipo_toastr']    = 'error';
        header('Location: index2.php?vista=importar_precios');
        exit;
    }

    if (!is_dir(__DIR__ . '/logs')) {
        mkdir(__DIR__ . '/logs', 0777, true);
    }
    $log = '[' . date('Y-m-d H:i:s') . "\n" . implode("\n", $log_entries) . "\n";
    file_put_contents(__DIR__ . '/logs/importar_precios.log', $log, FILE_APPEND);

    $_SESSION['mensaje_toastr'] = "$actualizados productos actualizados, " . count($omitidos) . ' omitidos';
    $_SESSION['tipo_toastr']    = 'success';
    $_SESSION['no_encontrados'] = $omitidos;
    header('Location: index2.php?vista=importar_precios');
    exit;
}

function procesar_producto($id, $costo, $stock, &$actualizados, &$omitidos, &$log_entries, $conexion)
{
    $costo = (float) $costo;

    if (is_numeric($id)) {
        $stmt = $conexion->prepare('SELECT id_producto FROM productos WHERE id_producto=?');
        $stmt->bind_param('i', $id);
    } else {
        $stmt = $conexion->prepare('SELECT id_producto FROM productos WHERE nombre=?');
        $stmt->bind_param('s', $id);
    }
    $stmt->execute();
    $stmt->bind_result($id_producto);
    if ($stmt->fetch()) {
        $stmt->close();
        if ($stock !== null && $stock !== '') {
            $stock = (int) $stock;
            $upd = $conexion->prepare('UPDATE productos SET costo=?, stock=? WHERE id_producto=?');
            $upd->bind_param('dii', $costo, $stock, $id_producto);
        } else {
            $upd = $conexion->prepare('UPDATE productos SET costo=? WHERE id_producto=?');
            $upd->bind_param('di', $costo, $id_producto);
        }
        $upd->execute();
        $upd->close();
        $actualizados++;
        $log_entries[] = "Actualizado: $id_producto | Costo: $costo" . ($stock !== null && $stock !== '' ? " | Stock: $stock" : '');
    } else {
        $omitidos[]   = $id;
        $log_entries[] = "No encontrado: $id";
        $stmt->close();
    }
}
?>

<div class="card">
    <div class="card-body">
        <h3 class="card-title">Importar Precios</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <input type="file" name="archivo" class="form-control" accept=".xlsx,.pdf" required>
            </div>
            <button type="submit" class="btn btn-primary">Importar</button>
        </form>

        <?php if (!empty($no_encontrados)): ?>
            <div class="mt-3">
                <h5>No encontrados:</h5>
                <ul>
                    <?php foreach ($no_encontrados as $ne): ?>
                        <li><?= htmlspecialchars($ne) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>
