<?php
require_once '../includes/auth.php';
require_once '../includes/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Hardware Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">Hardware Solutions — Admin</span>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">Hola, <?= htmlspecialchars($_SESSION['admin_nombre'] ?? $_SESSION['admin_usuario']) ?></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h4 class="mb-4">Categorías de componentes</h4>
        <div class="row g-3">
            <?php
            $categorias = [
                ['nombre' => 'CPU', 'archivo' => 'cpu.php'],
                ['nombre' => 'Placa Madre', 'archivo' => null],
                ['nombre' => 'RAM', 'archivo' => null],
                ['nombre' => 'GPU', 'archivo' => 'gpu.php'],
                ['nombre' => 'Fuente de Poder', 'archivo' => null],
                ['nombre' => 'Almacenamiento', 'archivo' => null],
                ['nombre' => 'Gabinete', 'archivo' => null],
                ['nombre' => 'Cooler', 'archivo' => null],
            ];
            foreach ($categorias as $cat):
            ?>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?= htmlspecialchars($cat['nombre']) ?></h6>
                            <?php if ($cat['archivo']): ?>
                                <p class="text-muted small flex-grow-1">Gestionar componentes de esta categoría.</p>
                                <a href="<?= $cat['archivo'] ?>" class="btn btn-sm btn-primary mt-2">Gestionar</a>
                            <?php else: ?>
                                <p class="text-muted small flex-grow-1">Próximamente.</p>
                                <button class="btn btn-sm btn-outline-secondary mt-2" disabled>Gestionar</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>