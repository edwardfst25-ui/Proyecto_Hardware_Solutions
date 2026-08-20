<?php
require_once '../includes/auth.php';
require_once '../includes/conexion.php';

$stmt = $pdo->query("SELECT * FROM gpu ORDER BY nombre");
$gpus = $stmt->fetchAll();

$mensaje = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar GPU - Hardware Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">Hardware Solutions — Admin</span>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">← Volver al panel</a>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Gestionar GPU</h4>
            <a href="gpu_form.php" class="btn btn-primary">+ Agregar GPU</a>
        </div>

        <?php if ($mensaje === 'creado'): ?>
            <div class="alert alert-success">GPU creada con éxito.</div>
        <?php elseif ($mensaje === 'actualizado'): ?>
            <div class="alert alert-success">GPU actualizada con éxito.</div>
        <?php elseif ($mensaje === 'eliminado'): ?>
            <div class="alert alert-success">GPU eliminada con éxito.</div>
        <?php elseif ($mensaje === 'error_en_uso'): ?>
            <div class="alert alert-danger">No se pudo eliminar: esta GPU está usada en un armado recomendado. Elimina primero ese armado.</div>
        <?php endif; ?>

        <table class="table table-bordered bg-white">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th>Memoria (VRAM)</th>
                    <th>Consumo</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gpus as $gpu): ?>
                <tr>
                    <td><?= htmlspecialchars($gpu['nombre'] ?? '') ?></td>
                    <td><?= htmlspecialchars($gpu['marca'] ?? '') ?></td>
                    <td><?= htmlspecialchars($gpu['memoria_gb'] ?? '') ?> GB</td>
                    <td><?= htmlspecialchars($gpu['consumo_w'] ?? '') ?> W</td>
                    <td>$<?= number_format($gpu['precio'], 0, ',', '.') ?></td>
                    <td>
                        <a href="gpu_form.php?id=<?= $gpu['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <a href="gpu_eliminar.php?id=<?= $gpu['id'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Seguro que quieres eliminar esta GPU?');">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
