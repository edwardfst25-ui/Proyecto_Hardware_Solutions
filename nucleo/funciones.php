<?php
/**
 * Funciones de apoyo que usa toda la aplicación.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Carpeta raíz del proyecto en el disco. */
define('RAIZ', dirname(__DIR__));

/** Escapa un texto antes de imprimirlo en HTML (evita ataques XSS). */
function e($valor): string
{
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

/** Arma una dirección interna: url('componentes', 'listar', ['cat' => 'cpu']). */
function url(string $c, string $a = '', array $parametros = []): string
{
    $query = ['c' => $c];
    if ($a !== '') {
        $query['a'] = $a;
    }
    return 'index.php?' . http_build_query($query + $parametros);
}

/** Envía al navegador a otra página y detiene el script. */
function redirigir(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/** Formatea un precio en pesos: 1250000 -> $1.250.000 */
function precio($valor): string
{
    return '$' . number_format((float)$valor, 0, ',', '.');
}

/** Lee un ajuste de config/app.php: ajuste('por_pagina'). */
function ajuste(string $clave)
{
    static $ajustes = null;
    if ($ajustes === null) {
        $ajustes = require RAIZ . '/config/app.php';
    }
    return $ajustes[$clave] ?? null;
}

/** Glosario en lenguaje sencillo (config/glosario.php). */
function glosario(): array
{
    static $glosario = null;
    if ($glosario === null) {
        $glosario = require RAIZ . '/config/glosario.php';
    }
    return $glosario;
}

/** Ícono (Bootstrap Icons) de cada categoría. Se usa cuando un componente no tiene foto. */
function icono_categoria(string $categoria): string
{
    $iconos = [
        'cpu' => 'bi-cpu', 'placa_madre' => 'bi-motherboard', 'ram' => 'bi-memory', 'gpu' => 'bi-gpu-card',
        'fuente_poder' => 'bi-plug', 'almacenamiento' => 'bi-device-ssd', 'gabinete' => 'bi-pc', 'cooler' => 'bi-fan',
    ];
    return $iconos[$categoria] ?? 'bi-box';
}

/**
 * HTML de la foto de un componente; si no tiene, muestra el ícono de su categoría.
 * $clase define el tamaño (ver recursos/css/estilos.css).
 */
function foto_componente(string $categoria, ?string $imagen, string $clase = 'foto-tarjeta'): string
{
    if ($imagen) {
        return '<img src="galeria/componentes/' . e($imagen) . '" alt="" class="' . e($clase) . '">';
    }
    return '<span class="' . e($clase) . ' foto-vacia" aria-hidden="true"><i class="bi ' . icono_categoria($categoria) . '"></i></span>';
}

/** Enlace del producto en Amazon a partir de su ASIN (con el código de afiliado si está configurado). */
function enlace_amazon(string $asin): string
{
    $url = 'https://www.amazon.com/dp/' . rawurlencode($asin);
    $tag = (string)ajuste('amazon_tag');
    return $tag !== '' ? $url . '?tag=' . rawurlencode($tag) : $url;
}

/**
 * Muestra una vista dentro de la plantilla común.
 * $datos se convierte en variables de la vista: ['titulo' => 'X'] crea $titulo.
 * $zona es 'admin' o 'publico' y cambia la barra de navegación.
 */
function vista(string $__vista, array $__datos = [], string $zona = 'admin'): void
{
    // EXTR_SKIP: un dato nunca puede pisar las variables propias de esta función.
    extract($__datos, EXTR_SKIP);
    require RAIZ . '/vistas/plantillas/encabezado.php';
    require RAIZ . "/vistas/$__vista.php";
    require RAIZ . '/vistas/plantillas/pie.php';
}

/* ---------- Sesión del administrador ---------- */

/** Bloquea la página si no hay un administrador con sesión iniciada. */
function requerir_admin(): void
{
    if (!isset($_SESSION['admin_id'])) {
        redirigir(url('auth', 'login'));
    }
}

/* ---------- Mensajes de una sola vez ("flash") ---------- */

/** Guarda un mensaje para mostrarlo en la siguiente página. $tipo: success, danger, warning. */
function flash(string $tipo, string $texto): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}

/** Devuelve el mensaje pendiente (si hay) y lo borra para que no se repita. */
function flash_tomar(): ?array
{
    $mensaje = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $mensaje;
}

/* ---------- Protección CSRF ----------
 * Cada formulario lleva un código secreto guardado en la sesión. Si llega un POST
 * sin ese código, viene de otra página (posible ataque) y se rechaza.
 */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/** Campo oculto para poner dentro de cada <form method="POST">. */
function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

/** Exige que la petición sea POST y traiga el código correcto. */
function csrf_verificar(): void
{
    $enviado = $_POST['csrf'] ?? '';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_string($enviado) || !hash_equals(csrf_token(), $enviado)) {
        http_response_code(400);
        vista('plantillas/error', [
            'titulo'  => 'Solicitud no válida',
            'mensaje' => 'El formulario expiró o no se envió correctamente. Vuelve atrás, recarga la página e inténtalo de nuevo.',
        ], 'publico');
        exit;
    }
}
