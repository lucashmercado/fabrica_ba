<?php
// importar_precios.php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/vendor/autoload.php';

use Smalot\PdfParser\Parser;

function normalizar_precio($raw){
  $raw = trim($raw);
  $raw = str_replace(['.', ' '], '', $raw);
  $raw = str_replace(',', '.', $raw);
  return (int) round(floatval($raw));
}
function limpiar_nombre($raw){ return preg_replace('/\s+/', ' ', trim($raw)); }

// Procesar upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['precios']) && $_FILES['precios']['error'] === UPLOAD_ERR_OK) {
  $tmp = $_FILES['precios']['tmp_name'];

  $parser = new Parser();
  try { $pdf = $parser->parseFile($tmp); }
  catch (Exception $e) { die("No se pudo leer el PDF: ".htmlspecialchars($e->getMessage())); }

  $texto  = $pdf->getText();
  $lineas = preg_split('/\R+/u', $texto);
  $patron = '/^\s*(.+?)\s+\$\s*([\d\.\,]+)\s*$/u';

  // Recomendado: índice único por nombre para el upsert:
  // ALTER TABLE productos ADD UNIQUE KEY uniq_nombre (nombre);
  $sql  = "INSERT INTO productos (nombre, costo, stock)
           VALUES (?, ?, 0)
           ON DUPLICATE KEY UPDATE costo = VALUES(costo)";
  $stmt = $conexion->prepare($sql);
  if(!$stmt) die("Error SQL: ".$conexion->error);

  $insertados=0; $actualizados=0; $saltados=0;

  foreach($lineas as $lin){
    if(!preg_match($patron,$lin,$m)) continue;
    $nombre = limpiar_nombre($m[1]);
    $precio = normalizar_precio($m[2]);
    if ($precio<=0){ $saltados++; continue; }

    $stmt->bind_param('si',$nombre,$precio);
    if(!$stmt->execute()){ $saltados++; continue; }

    if ($stmt->affected_rows===1) $insertados++;
    elseif ($stmt->affected_rows===2) $actualizados++;
    else $saltados++;
  }

  // ---------- SALIDA (resultado) CON BOOTSTRAP + FA ----------
  ?>
  <!doctype html>
  <html lang="es">
  <head>
    <meta charset="utf-8">
    <title>Importación completada</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap + Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Tus estilos -->
    <link rel="stylesheet" href="styles.css">
    <style>
      body{padding-top:80px;margin:0}
      .glass{max-width:720px;margin:24px auto;padding:20px;background:#ffffffcc;border-radius:16px;backdrop-filter:blur(3px)}
    </style>
  </head>
  <body class="bg-fabrica">
    <?php if (file_exists(__DIR__.'/navbar.php')) include __DIR__.'/navbar.php'; ?>
    <main class="glass">
      <h2 class="mb-3">Importación completada</h2>
      <p>Nuevos: <strong><?= $insertados ?></strong></p>
      <p>Actualizados: <strong><?= $actualizados ?></strong></p>
      <p>Saltados (precio 0 u otras líneas): <strong><?= $saltados ?></strong></p>
      <a href="index2.php?vista=listar_productos" class="btn btn-primary mt-2"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
  </html>
  <?php
  exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Importar precios (PDF)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap + Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- Tus estilos globales -->
  <link rel="stylesheet" href="styles.css">
  <style>
    body{ padding-top:80px; margin:0; }
    .page{ max-width:680px; margin:24px auto; padding:16px; }
    .card{
      background:#ffffffcc; border-radius:16px; padding:20px;
      box-shadow:0 8px 24px rgba(0,0,0,.06); backdrop-filter: blur(3px);
    }
    .row-flex{display:flex;gap:12px;flex-wrap:wrap}
    @media (max-width:520px){ .row-flex > *{width:100%} }
  </style>
</head>
<body class="bg-fabrica">
  <?php if (file_exists(__DIR__.'/navbar.php')) include __DIR__.'/navbar.php'; ?>

  <div class="page">
    <div class="card">
      <h1 class="h3 mb-3"><i class="fa-solid fa-file-import"></i> Importar precios desde PDF</h1>
      <form method="POST" enctype="multipart/form-data" action="importar_precios.php">
        <div class="row-flex">
          <input class="form-control" type="file" name="precios" accept="application/pdf" required>
          <button class="btn btn-primary" type="submit"><i class="fa-solid fa-cloud-arrow-up"></i> Importar</button>
        </div>
        <p class="text-muted mt-2">Se insertan productos nuevos y se actualiza el costo de los existentes. El stock queda en 0.</p>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
