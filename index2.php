<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once("conexion.php");



// Mensajes toastr
$mensaje_toastr = $_SESSION['mensaje_toastr'] ?? "";
$tipo_toastr = $_SESSION['tipo_toastr'] ?? "";
unset($_SESSION['mensaje_toastr'], $_SESSION['tipo_toastr']);

// Definir vista activa
$vista = $_GET['vista'] ?? 'listar_productos';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Fabrica BA</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Toastr -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">

    <!-- Estilo adicional para evitar que el contenido quede debajo del navbar -->
    <style>
        body {
            padding-top: 70px; /* Ajuste para navbar fijo */
        }
    </style>
</head>
<body>
    <style>
        body {
            background-image: url('fabricaBA.jpg');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>
    <script>
        function Eliminar(id) {
    Swal.fire({
        title: '¿Eliminar producto?',
        text: "Esta acción no se puede deshacer",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'eliminar_producto.php?id=' + id;
        }
    });
}

        function Editar(id) {
            window.location.href = 'index2.php?vista=editar_producto&id=' + id;
        }
    </script>
    <?php include("navbar.php"); ?>

    <div class="container mt-3">
        <?php
        if ($vista === 'agregar_producto') {
            include("agregar_producto.php");
        } elseif ($vista === 'usuarios') {
            include("usuarios.php");
        } elseif ($vista === 'editar_producto' && isset($_GET['id'])) {
            include("editar_producto.php");
        } else {
            include("listar_productos.php");
        }
        ?>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        
    <script>
        // Notificaciones toastr
        <?php if (!empty($mensaje_toastr)): ?>
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };
            toastr[<?= json_encode($tipo_toastr) ?>](<?= json_encode($mensaje_toastr) ?>);
        <?php endif; ?>
    </script>
</body>
</html>