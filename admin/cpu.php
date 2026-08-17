<?php
require_once '../includes/auth.php';
require_once '../includes/conexion.php';

$stmt = $pdo->query("SELECT * FROM cpu ORDER BY nombre");
$cpus = $stmt->fetchAll();

$mensaje = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar CPU - Hardware Solutions</title>
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
            <h4>Gestionar CPU</h4>
            <a href="cpu_form.php" class="btn btn-primary">+ Agregar CPU</a>
        </div>

        <?php if ($mensaje === 'creado'): ?>
            <div class="alert alert-success">CPU creado con éxito.</div>
        <?php elseif ($mensaje === 'actualizado'): ?>
            <div class="alert alert-success">CPU actualizado con éxito.</div>
        <?php elseif ($mensaje === 'eliminado'): ?>
            <div class="alert alert-success">CPU eliminado con éxito.</div>
        <?php elseif ($mensaje === 'error_en_uso'): ?>
            <div class="alert alert-danger">No se pudo eliminar: este CPU está usado en un armado recomendado. Elimina primero ese armado.</div>
        <?php endif; ?>

        <table class="table table-bordered bg-white">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th>Socket</th>
                    <th>Consumo</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cpus as $cpu): ?>
                <tr>
                    <td><?= htmlspecialchars($cpu['nombre']) ?></td>
                    <td><?= htmlspecialchars($cpu['marca']) ?></td>
                    <td><?= htmlspecialchars($cpu['socket']) ?></td>
                    <td><?= $cpu['consumo_w'] ?> W</td>
                    <td>$<?= number_format($cpu['precio'], 0, ',', '.') ?></td>
                    <td>
                        <a href="cpu_form.php?id=<?= $cpu['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <a href="cpu_eliminar.php?id=<?= $cpu['id'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Seguro que quieres eliminar este CPU?');">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>