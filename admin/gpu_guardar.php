<?php
require_once '../includes/auth.php';
require_once '../includes/conexion.php';

// La columna precio es decimal(10,2): máximo 8 dígitos enteros + 2 decimales.
const PRECIO_MAX = 99999999.99;

$nombre = trim($_POST['nombre'] ?? '');
$marca = trim($_POST['marca'] ?? '');
$memoria_gb = $_POST['memoria_gb'] !== '' ? (int)$_POST['memoria_gb'] : null;
$consumo = (int)($_POST['consumo_w'] ?? 0);
$precio = (float)($_POST['precio'] ?? 0);
$asin = trim($_POST['asin'] ?? '');
$imagen = trim($_POST['imagen'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

function volver_con_error(string $mensaje): void {
    $_SESSION['gpu_form_error'] = $mensaje;
    $_SESSION['gpu_form_datos'] = $_POST;
    $idQuery = !empty($_POST['id']) ? '?id=' . (int)$_POST['id'] : '';
    header('Location: gpu_form.php' . $idQuery);
    exit;
}

if ($precio < 0 || $precio > PRECIO_MAX) {
    volver_con_error('El precio debe estar entre 0 y ' . number_format(PRECIO_MAX, 0, ',', '.') . ' COP. Revisa que no tenga dígitos de más.');
}

try {
    if (!empty($_POST['id'])) {
        // Editar
        $sql = "UPDATE gpu SET nombre=?, marca=?, memoria_gb=?, consumo_w=?, precio=?, asin=?, imagen=?, descripcion=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $marca, $memoria_gb, $consumo, $precio, $asin, $imagen, $descripcion, $_POST['id']]);
        header('Location: gpu.php?msg=actualizado');
    } else {
        // Crear
        $sql = "INSERT INTO gpu (nombre, marca, memoria_gb, consumo_w, precio, asin, imagen, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $marca, $memoria_gb, $consumo, $precio, $asin, $imagen, $descripcion]);
        header('Location: gpu.php?msg=creado');
    }
} catch (\PDOException $e) {
    volver_con_error('No se pudo guardar: revisa que memoria, consumo y precio estén dentro de un rango razonable.');
}
exit;
