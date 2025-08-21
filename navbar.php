<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">Fábrica</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Accesos directos -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= ($vista === 'listar_productos') ? 'active' : '' ?>" href="index2.php?vista=listar_productos">
                        <i class="fas fa-list"></i> Listar Productos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($vista === 'agregar_producto') ? 'active' : '' ?>" href="index2.php?vista=agregar_producto">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($vista === 'usuarios') ? 'active' : '' ?>" href="index2.php?vista=usuarios">
                        <i class="fas fa-users"></i> Usuarios
                    </a>
                </li>
            </ul>

            <!-- Usuario y cerrar sesión -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item d-flex align-items-center me-3">
                    <i class="fas fa-circle text-success me-2"></i>
                    <span class="text-white">Bienvenido, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
