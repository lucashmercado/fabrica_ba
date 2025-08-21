<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="login-box">
        <h2>Iniciar sesión</h2>
        <form action="autentificar.php" method="POST">
            <input type="text" name="username" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Ingresar</button>
        </form>
        <p>¿No tenés cuenta? <a href="registrar.php">Registrate aquí</a></p>
        <?php if (isset($_GET['error'])): ?>
            <p class="error"><?= htmlspecialchars($_GET['error']) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
