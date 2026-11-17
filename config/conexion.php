<?php
/**
 * Conexión a la base de datos con PDO.
 * Deja lista la variable $pdo, que el controlador frontal reparte a los controladores.
 * PENDIENTE antes de publicar: mover usuario y contraseña a variables de entorno.
 */
$host = '127.0.0.1';
$db   = 'proyecto';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opciones = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // los errores de SQL lanzan excepciones
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // las filas llegan como arreglos asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // consultas preparadas reales
];

try {
    $pdo = new PDO($dsn, $user, $pass, $opciones);
} catch (PDOException $e) {
    http_response_code(500);
    die('No se pudo conectar a la base de datos. Revisa que MySQL esté encendido en Laragon.');
}
