<?php
/**
 * CONTROLADOR FRONTAL — Hardware Solutions
 *
 * Es la única puerta de entrada de la aplicación. Todas las páginas se piden así:
 *     index.php?c=<controlador>&a=<acción>
 * Ejemplo: index.php?c=componentes&a=listar&cat=cpu
 *
 * Su trabajo es: preparar el entorno (sesión, conexión, configuración),
 * decidir qué controlador y qué método atienden la petición, y ejecutarlo.
 */

require_once __DIR__ . '/nucleo/funciones.php';   // funciones de apoyo + inicio de sesión
require_once __DIR__ . '/config/conexion.php';    // crea $pdo
$categorias = require __DIR__ . '/config/categorias.php'; // definición de las 8 categorías

// Carga automática de clases: cuando el código usa una clase que aún no existe,
// PHP llama a esta función y aquí buscamos el archivo con ese nombre.
spl_autoload_register(function (string $clase): void {
    foreach (['controladores', 'modelos', 'nucleo'] as $carpeta) {
        $archivo = __DIR__ . "/$carpeta/$clase.php";
        if (is_file($archivo)) {
            require_once $archivo;
            return;
        }
    }
});

// Tabla de rutas (lista blanca): solo se puede llamar lo que aparece aquí.
// 'c' => [clase del controlador, [acciones permitidas]]
$rutas = [
    // Área pública
    'inicio'          => ['InicioControlador',        ['index', 'glosario']],
    'catalogo'        => ['CatalogoControlador',      ['index', 'detalle', 'comparar']],
    'armador'         => ['ArmadorControlador',       ['index', 'evaluar']],
    'recomendaciones' => ['RecomendacionControlador', ['index', 'generar']],
    // Área administrativa
    'auth'            => ['AuthControlador',          ['login', 'logout']],
    'panel'           => ['PanelControlador',         ['index']],
    'componentes'     => ['ComponenteControlador',    ['listar', 'formulario', 'guardar', 'eliminar']],
    'armados'         => ['ArmadoControlador',        ['listar', 'formulario', 'guardar', 'eliminar']],
    'administradores' => ['AdministradorControlador', ['listar', 'formulario', 'guardar', 'contrasena', 'correo', 'restablecer']],
];

$c = $_GET['c'] ?? 'inicio';                  // página por defecto: la página de inicio
$a = $_GET['a'] ?? ($rutas[$c][1][0] ?? '');  // acción por defecto: la primera de la lista

if (!is_string($c) || !is_string($a) || !isset($rutas[$c]) || !in_array($a, $rutas[$c][1], true)) {
    http_response_code(404);
    vista('plantillas/error', ['titulo' => 'Página no encontrada', 'mensaje' => 'La dirección que abriste no existe.'], 'publico');
    exit;
}

$clase = $rutas[$c][0];
$controlador = new $clase($pdo, $categorias);
$controlador->$a();
