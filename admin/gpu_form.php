<?php
require_once '../includes/auth.php';
require_once '../includes/conexion.php';

$editando = false;
$gpu = ['nombre'=>'','marca'=>'','memoria_gb'=>'','consumo_w'=>'','precio'=>'','asin'=>'','imagen'=>'','descripcion'=>''];

if (isset($_GET['id'])) {
    $editando = true;
    $stmt = $pdo->prepare("SELECT * FROM gpu WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $gpu = $stmt->fetch();
    if (!$gpu) {
        header('Location: gpu.php');
        exit;
    }
}

// Si venimos de un intento fallido (gpu_guardar.php), recuperamos el mensaje
// de error y los datos que el usuario había escrito, para no hacerle perder lo tecleado.
$error = null;
if (isset($_SESSION['gpu_form_error'])) {
    $error = $_SESSION['gpu_form_error'];
    if (isset($_SESSION['gpu_form_datos'])) {
        $gpu = array_merge($gpu, $_SESSION['gpu_form_datos']);
    }
    unset($_SESSION['gpu_form_error'], $_SESSION['gpu_form_datos']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $editando ? 'Editar' : 'Agregar' ?> GPU - Hardware Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">Hardware Solutions — Admin</span>
            <a href="gpu.php" class="btn btn-outline-light btn-sm">← Volver</a>
        </div>
    </nav>

    <div class="container">
        <h4 class="mb-4"><?= $editando ? 'Editar' : 'Agregar' ?> GPU</h4>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="gpu_guardar.php" class="bg-white p-4 rounded shadow-sm">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= $gpu['id'] ?? '' ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($gpu['nombre'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Marca</label>
                    <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($gpu['marca'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Memoria de video (GB)</label>
                    <input type="number" name="memoria_gb" class="form-control" value="<?= htmlspecialchars($gpu['memoria_gb'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Consumo (W)</label>
                    <input type="number" name="consumo_w" class="form-control" value="<?= htmlspecialchars($gpu['consumo_w'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Precio (COP)</label>
                    <input type="number" step="0.01" min="0" max="99999999.99" name="precio" class="form-control" value="<?= htmlspecialchars($gpu['precio'] ?? '') ?>" required>
                    <div class="form-text">Máximo 99.999.999,99 COP.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ASIN (Amazon)</label>
                    <input type="text" name="asin" class="form-control" value="<?= htmlspecialchars($gpu['asin'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Imagen (URL o nombre de archivo)</label>
                    <input type="text" name="imagen" class="form-control" value="<?= htmlspecialchars($gpu['imagen'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($gpu['descripcion'] ?? '') ?></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Guardar</button>
            <a href="gpu.php" class="btn btn-secondary mt-4">Cancelar</a>
        </form>
    </div>
</body>
</html>
