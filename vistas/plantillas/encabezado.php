<?php
/**
 * Parte superior común a todas las páginas.
 * Variables: $titulo (texto de la pestaña) y $zona ('admin' o 'publico').
 * El área pública sigue los prototipos del documento (figuras 11 a 14):
 * cabecera azul con el menú, franja de búsqueda y pie de página.
 */
$mensajeFlash = flash_tomar();
$seccion = is_string($_GET['c'] ?? null) ? $_GET['c'] : 'inicio'; // para resaltar el menú actual
// La franja de búsqueda solo tiene sentido donde se exploran componentes.
$mostrarBusqueda = in_array($seccion, ['inicio', 'catalogo'], true);
// En el inicio de sesión se quita el menú: la pantalla es solo para administradores.
$mostrarMenu = $seccion !== 'auth';
$menu = ['inicio' => 'Inicio', 'catalogo' => 'Catálogo', 'armador' => 'Armado', 'recomendaciones' => 'Recomendaciones'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'Hardware Solutions') ?> - Hardware Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="recursos/css/estilos.css" rel="stylesheet">
</head>
<?php if ($zona === 'admin'): ?>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= e(url('panel')) ?>">Hardware Solutions — Admin</a>
            <div class="d-flex align-items-center gap-2">
                <span class="text-white me-2 d-none d-md-inline">Hola, <?= e($_SESSION['admin_nombre'] ?? $_SESSION['admin_usuario'] ?? '') ?></span>
                <a href="<?= e(url('inicio')) ?>" class="btn btn-outline-light btn-sm">Ver sitio</a>
                <form method="POST" action="<?= e(url('auth', 'logout')) ?>" class="m-0">
                    <?= csrf_campo() ?>
                    <button type="submit" class="btn btn-outline-light btn-sm">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container pb-5">
<?php else: ?>
<body class="zona-publica d-flex flex-column min-vh-100">
    <header class="cabecera">
        <nav class="navbar navbar-expand-md navbar-dark">
            <div class="container">
                <a class="navbar-brand fw-bold" href="<?= e(url('inicio')) ?>">
                    <i class="bi bi-pc-display me-1" aria-hidden="true"></i> Hardware Solutions
                </a>
                <?php if ($mostrarMenu): ?>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu-principal"
                        aria-controls="menu-principal" aria-expanded="false" aria-label="Abrir menú">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="menu-principal">
                    <ul class="navbar-nav">
                        <?php foreach ($menu as $clave => $texto): ?>
                            <li class="nav-item">
                                <a class="nav-link<?= $seccion === $clave ? ' active' : '' ?>" <?= $seccion === $clave ? 'aria-current="page"' : '' ?>
                                   href="<?= e(url($clave)) ?>"><?= e($texto) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php else: ?>
                    <a href="<?= e(url('inicio')) ?>" class="btn btn-outline-light btn-sm">← Volver al sitio</a>
                <?php endif; ?>
            </div>
        </nav>
        <div class="container">
            <p class="lema">Plataforma para la selección y comparación de componentes de PC</p>
        </div>
    </header>

    <!-- Franja de búsqueda: envía el texto al catálogo. En un formulario GET los
         parámetros van en campos ocultos (lo que se escriba en "action" se pierde). -->
    <?php if ($mostrarBusqueda): ?>
    <div class="franja-busqueda">
        <form class="container d-flex gap-2" method="GET" action="index.php" role="search">
            <input type="hidden" name="c" value="catalogo">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search" aria-hidden="true"></i></span>
                <input type="search" name="q" class="form-control border-start-0" placeholder="Buscar componentes"
                       aria-label="Buscar componentes" value="<?= e($busqueda ?? '') ?>" maxlength="80">
            </div>
            <button type="submit" class="btn btn-primary px-4">Buscar</button>
        </form>
    </div>
    <?php endif; ?>

    <main class="container py-4 flex-grow-1">
<?php endif; ?>
        <?php if ($mensajeFlash): ?>
            <div class="alert alert-<?= e($mensajeFlash['tipo']) ?>" role="alert"><?= e($mensajeFlash['texto']) ?></div>
        <?php endif; ?>
