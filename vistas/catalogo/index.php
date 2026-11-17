<?php
/**
 * Catálogo y búsqueda (prototipos de las figuras 11 y 12).
 * Los filtros viajan por la URL (formulario GET), así cualquier resultado se puede
 * guardar o compartir como enlace.
 */
// Parámetros actuales, para conservarlos en los enlaces de paginación.
$parametros = array_filter(['q' => $busqueda, 'cat' => $filtro, 'orden' => $orden !== 'nombre' ? $orden : null]);
?>
<h1 class="h3 mb-1"><?= e($titulo) ?></h1>
<p class="text-muted">
    <?php if ($busqueda !== ''): ?>
        Mostrando <?= (int)$total ?> <?= $total === 1 ? 'resultado' : 'resultados' ?> para: «<?= e($busqueda) ?>».
    <?php else: ?>
        Explora los componentes disponibles para armar tu computador.
    <?php endif; ?>
</p>

<div class="row g-4">
    <!-- Filtros -->
    <aside class="col-lg-3">
        <form method="GET" action="index.php" class="card tarjeta">
            <div class="card-body">
                <input type="hidden" name="c" value="catalogo">
                <?php if ($busqueda !== ''): ?><input type="hidden" name="q" value="<?= e($busqueda) ?>"><?php endif; ?>

                <fieldset class="mb-3">
                    <legend class="h6">Filtrar por categoría</legend>
                    <?php foreach ($nombres as $tabla => $nombre): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cat[]" value="<?= e($tabla) ?>" id="filtro_<?= e($tabla) ?>"
                                   <?= in_array($tabla, $filtro, true) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="filtro_<?= e($tabla) ?>"><?= e($nombre) ?></label>
                        </div>
                    <?php endforeach; ?>
                </fieldset>

                <label for="orden" class="h6 d-block">Ordenar por</label>
                <select name="orden" id="orden" class="form-select mb-3">
                    <?php foreach (Catalogo::ORDENES as $clave => $opcion): ?>
                        <option value="<?= e($clave) ?>" <?= $orden === $clave ? 'selected' : '' ?>><?= e($opcion['texto']) ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-primary w-100 mb-2">Aplicar</button>
                <a href="<?= e(url('catalogo')) ?>" class="btn btn-outline-secondary w-100">Limpiar filtros</a>
            </div>
        </form>
    </aside>

    <!-- Resultados -->
    <section class="col-lg-9">
        <?php if (!$filas): ?>
            <div class="alert alert-info">
                No encontramos componentes con esos criterios. Prueba con otra palabra o
                <a href="<?= e(url('catalogo')) ?>" class="alert-link">mira todo el catálogo</a>.
            </div>
        <?php else: ?>
            <div class="row row-cols-2 row-cols-md-3 g-3">
                <?php foreach ($filas as $fila):
                    $enlace = url('catalogo', 'detalle', ['cat' => $fila['categoria'], 'id' => $fila['id']]);
                    $idCasilla = 'comparar_' . $fila['categoria'] . '_' . $fila['id'];
                ?>
                    <div class="col">
                        <div class="card tarjeta h-100 text-center">
                            <div class="card-body d-flex flex-column">
                                <a href="<?= e($enlace) ?>" class="d-block mb-2" tabindex="-1" aria-hidden="true">
                                    <?= foto_componente($fila['categoria'], $fila['imagen']) ?>
                                </a>
                                <h2 class="h6 mb-1"><?= e($fila['nombre']) ?></h2>
                                <div class="small text-muted"><?= e($nombres[$fila['categoria']]) ?> · <?= e($fila['marca']) ?></div>
                                <div class="fw-bold my-2 mt-auto pt-2"><?= precio($fila['precio']) ?></div>
                                <a href="<?= e($enlace) ?>" class="btn btn-primary btn-sm">Ver detalles</a>
                                <div class="form-check d-inline-flex justify-content-center gap-2 mt-2 small">
                                    <input class="form-check-input casilla-comparar" type="checkbox" id="<?= e($idCasilla) ?>"
                                           data-cat="<?= e($fila['categoria']) ?>" data-id="<?= (int)$fila['id'] ?>"
                                           data-categoria="<?= e($nombres[$fila['categoria']]) ?>">
                                    <label class="form-check-label" for="<?= e($idCasilla) ?>">Comparar</label>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($paginas > 1): ?>
                <nav class="mt-4" aria-label="Páginas del catálogo">
                    <ul class="pagination justify-content-center flex-wrap">
                        <?php for ($p = 1; $p <= $paginas; $p++): ?>
                            <li class="page-item<?= $p === $pagina ? ' active' : '' ?>">
                                <a class="page-link" <?= $p === $pagina ? 'aria-current="page"' : '' ?>
                                   href="<?= e(url('catalogo', 'index', $parametros + ['pagina' => $p])) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</div>

<!-- Barra de comparación: aparece al marcar "Comparar" en algún componente (recursos/js/comparar.js) -->
<div id="barra-comparar" class="barra-comparar" hidden
     data-url="<?= e(url('catalogo', 'comparar')) ?>" data-maximo="<?= (int)ajuste('max_comparar') ?>">
    <div class="container d-flex flex-wrap align-items-center gap-2">
        <span id="texto-comparar" class="me-auto" role="status" aria-live="polite"></span>
        <button type="button" id="btn-limpiar-comparar" class="btn btn-outline-light btn-sm">Quitar selección</button>
        <a id="btn-comparar" href="#" class="btn btn-light btn-sm">Comparar</a>
    </div>
</div>
<script src="recursos/js/comparar.js"></script>
