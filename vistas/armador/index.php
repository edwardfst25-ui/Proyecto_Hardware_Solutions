<?php
/**
 * Página pública "Armado personalizado".
 *
 * A la izquierda, una tarjeta por cada pieza del equipo. El botón "Elegir" abre una
 * ventana con las tarjetas de esa categoría (como en el catálogo), donde se puede
 * buscar, ordenar y ver cuáles son compatibles con lo ya elegido.
 * A la derecha, el resultado de la compatibilidad y el costo.
 *
 * El HTML solo trae la estructura; todo lo dibuja recursos/js/armador.js con la
 * respuesta del servidor (index.php?c=armador&a=evaluar).
 */
$ayudas = glosario()['categorias'];
?>
<div class="mb-4">
    <h1 class="h3 mb-1">Armado personalizado</h1>
    <p class="text-muted mb-0">Elige cada pieza de tu computador. Revisamos al instante si son compatibles entre sí y cuánto cuesta el equipo.</p>
</div>

<noscript><div class="alert alert-warning">Esta página necesita JavaScript activado para armar el equipo.</div></noscript>

<div class="row g-4" id="armador" data-url-evaluar="<?= e(url('armador', 'evaluar')) ?>">
    <!-- Columna izquierda: las 8 piezas -->
    <div class="col-lg-7">
        <?php foreach ($piezas as $cat => $nombre): ?>
            <section class="card tarjeta ranura mb-3" data-cat="<?= e($cat) ?>" data-nombre="<?= e($nombre) ?>" data-icono="<?= icono_categoria($cat) ?>">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ranura-foto" aria-hidden="true"></div>
                        <div class="flex-grow-1">
                            <h2 class="h6 mb-0">
                                <?= e(ucfirst($nombre)) ?>
                                <?php if ($cat === 'gpu'): ?><span class="text-muted fw-normal small">(opcional si el procesador tiene gráficos integrados)</span><?php endif; ?>
                            </h2>
                            <!-- Aquí el JavaScript escribe el componente elegido o "Sin elegir" -->
                            <div class="ranura-contenido text-muted small">Sin elegir</div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="ranura-precio fw-bold mb-1"></div>
                            <button type="button" class="btn btn-primary btn-sm btn-elegir">Elegir</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-quitar" hidden>Quitar</button>
                        </div>
                    </div>
                    <details class="ayuda small mt-2">
                        <summary>¿Qué es y en qué fijarme?</summary>
                        <p class="mb-0 mt-1 text-muted"><?= e($ayudas[$cat] ?? '') ?></p>
                    </details>
                </div>
            </section>
        <?php endforeach; ?>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary" id="btn-limpiar">Empezar de nuevo</button>
            <button type="button" class="btn btn-outline-primary" id="btn-compartir">
                <i class="bi bi-link-45deg" aria-hidden="true"></i> Copiar enlace para compartir
            </button>
            <span id="aviso-compartir" class="small text-success" role="status" aria-live="polite"></span>
            <a href="<?= e(url('inicio', 'glosario')) ?>" class="ms-auto small">Ver el glosario</a>
        </div>
    </div>

    <!-- Columna derecha: resumen que se actualiza solo -->
    <div class="col-lg-5">
        <div class="resumen-fijo">
            <div class="card tarjeta mb-3">
                <div class="card-body">
                    <h2 class="h5">Compatibilidad</h2>
                    <!-- aria-live hace que un lector de pantalla anuncie el cambio de estado -->
                    <div id="estado" class="alert alert-secondary mb-3" role="status" aria-live="polite">Elige un componente para comenzar.</div>
                    <ul id="verificaciones" class="list-unstyled small mb-0"></ul>
                    <ul id="avisos" class="small text-warning-emphasis mt-2 mb-0 ps-3"></ul>
                </div>
            </div>

            <div class="card tarjeta">
                <div class="card-body">
                    <h2 class="h5">Costo estimado</h2>
                    <label for="presupuesto" class="form-label small mb-1">Tu presupuesto (opcional)</label>
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">$</span>
                        <input type="text" inputmode="numeric" class="form-control" id="presupuesto" placeholder="Ej. 5000000">
                        <span class="input-group-text">COP</span>
                    </div>
                    <div id="caja-presupuesto" hidden>
                        <div class="progress mb-1" role="progressbar" aria-label="Presupuesto usado" aria-valuemin="0" aria-valuemax="100" id="barra-presupuesto">
                            <div class="progress-bar" id="relleno-presupuesto"></div>
                        </div>
                        <p id="texto-presupuesto" class="small mb-2" role="status" aria-live="polite"></p>
                    </div>
                    <div class="d-flex justify-content-between align-items-baseline">
                        <span class="fw-semibold">Total</span>
                        <span id="total" class="fs-4 fw-bold">$0</span>
                    </div>
                    <p id="consumo" class="text-muted small mt-2 mb-0"></p>
                    <p class="text-muted small mt-2 mb-0">Precios de referencia en pesos colombianos; pueden variar en la tienda.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ventana para elegir un componente (una sola, se reutiliza para las 8 categorías) -->
<div class="modal fade" id="ventana-elegir" tabindex="-1" aria-labelledby="titulo-elegir" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="titulo-elegir">Elegir componente</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 align-items-center mb-3">
                    <div class="col-md-5">
                        <input type="search" class="form-control" id="filtro-texto" placeholder="Buscar por nombre o marca" aria-label="Buscar por nombre o marca">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="filtro-orden" aria-label="Ordenar">
                            <option value="precio_asc">Precio: menor a mayor</option>
                            <option value="precio_desc">Precio: mayor a menor</option>
                            <option value="nombre">Nombre (A-Z)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="filtro-compatibles" checked>
                            <label class="form-check-label" for="filtro-compatibles">Solo compatibles</label>
                        </div>
                    </div>
                </div>
                <p id="conteo-elegir" class="small text-muted" role="status" aria-live="polite"></p>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-3" id="lista-elegir"></div>
            </div>
        </div>
    </div>
</div>

<!-- Catálogo para el armador, en formato JSON (lo lee armador.js) -->
<script type="application/json" id="datos-catalogo"><?= json_encode($catalogo, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<script src="recursos/js/armador.js"></script>
