<?php
require_once '../includes/auth.php';
require_once '../includes/conexion.php';

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM cpu WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        header('Location: cpu.php?msg=eliminado');
    } catch (\PDOException $e) {
        header('Location: cpu.php?msg=error_en_uso');
    }
}
exit;