<?php
require_once '../includes/auth.php';
require_once '../includes/conexion.php';

$nombre = trim($_POST['nombre'] ?? '');
$marca = trim($_POST['marca'] ?? '');
$socket = trim($_POST['socket'] ?? '');
$nucleos = $_POST['nucleos'] !== '' ? (int)$_POST['nucleos'] : null;
$frecuencia = $_POST['frecuencia_ghz'] !== '' ? (float)$_POST['frecuencia_ghz'] : null;
$consumo = (int)($_POST['consumo_w'] ?? 0);
$precio = (float)($_POST['precio'] ?? 0);
$asin = trim($_POST['asin'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if (!empty($_POST['id'])) {
    // Editar
    $sql = "UPDATE cpu SET nombre=?, marca=?, socket=?, nucleos=?, frecuencia_ghz=?, consumo_w=?, precio=?, asin=?, descripcion=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $marca, $socket, $nucleos, $frecuencia, $consumo, $precio, $asin, $descripcion, $_POST['id']]);
    header('Location: cpu.php?msg=actualizado');
} else {
    // Crear
    $sql = "INSERT INTO cpu (nombre, marca, socket, nucleos, frecuencia_ghz, consumo_w, precio, asin, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $marca, $socket, $nucleos, $frecuencia, $consumo, $precio, $asin, $descripcion]);
    header('Location: cpu.php?msg=creado');
}
exit;