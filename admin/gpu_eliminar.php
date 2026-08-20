<?php
require_once '../includes/auth.php';
require_once '../includes/conexion.php';

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM gpu WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        header('Location: gpu.php?msg=eliminado');
    } catch (\PDOException $e) {
        header('Location: gpu.php?msg=error_en_uso');
    }
}
exit;
