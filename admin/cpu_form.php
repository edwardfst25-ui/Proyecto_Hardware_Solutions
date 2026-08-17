<?php
require_once '../includes/auth.php';
require_once '../includes/conexion.php';

$editando = false;
$cpu = ['nombre'=>'','marca'=>'','socket'=>'','nucleos'=>'','frecuencia_ghz'=>'','consumo_w'=>'','precio'=>'','asin'=>'','descripcion'=>''];

if (isset($_GET['id'])) {
    $editando = true;
    $stmt = $pdo->prepare("SELECT * FROM cpu WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $cpu = $stmt->fetch();
    if (!$cpu) {
        header('Location: cpu.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $editando ? 'Editar' : 'Agregar' ?> CPU - Hardware Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">Hardware Solutions — Admin</span>
            <a href="cpu.php" class="btn btn-outline-light btn-sm">← Volver</a>
        </div>
    </nav>

    <div class="container">
        <h4 class="mb-4"><?= $editando ? 'Editar' : 'Agregar' ?> CPU</h4>

        <form method="POST" action="cpu_guardar.php" class="bg-white p-4 rounded shadow-sm">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= $cpu['id'] ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($cpu['nombre'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Marca</label>
                    <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($cpu['marca'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Socket</label>
                    <input type="text" name="socket" class="form-control" value="<?= htmlspecialchars($cpu['socket'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Núcleos</label>
                    <input type="number" name="nucleos" class="form-control" value="<?= htmlspecialchars($cpu['nucleos'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Frecuencia base (GHz)</label>
                    <input type="number" step="0.1" name="frecuencia_ghz" class="form-control" value="<?= htmlspecialchars($cpu['frecuencia_ghz'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Consumo (W)</label>
                    <input type="number" name="consumo_w" class="form-control" value="<?= htmlspecialchars($cpu['consumo_w'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Precio (COP)</label>
                    <input type="number" name="precio" class="form-control" value="<?= htmlspecialchars($cpu['precio'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ASIN (Amazon)</label>
                    <input type="text" name="asin" class="form-control" value="<?= htmlspecialchars($cpu['asin'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($cpu['descripcion'] ?? '') ?></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Guardar</button>
            <a href="cpu.php" class="btn btn-secondary mt-4">Cancelar</a>
        </form>
    </div>
</body>
</html>