<?php /** Ficha de un componente. */ ?>
<nav aria-label="Ruta de navegación">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="<?= e(url('catalogo')) ?>">Catálogo</a></li>
        <li class="breadcrumb-item"><a href="<?= e(url('catalogo', 'index', ['cat' => [$cat]])) ?>"><?= e($categoria) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= e($fila['nombre']) ?></li>
    </ol>
</nav>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card tarjeta text-center p-4">
            <?= foto_componente($cat, $fila['imagen'], 'foto-detalle') ?>
        </div>
    </div>

    <div class="col-md-7">
        <span class="badge etiqueta-categoria mb-2"><?= e($categoria) ?></span>
        <h1 class="h3 mb-1"><?= e($fila['nombre']) ?></h1>
        <p class="text-muted"><?= e($fila['marca']) ?></p>

        <p class="display-6 fw-bold mb-0"><?= precio($fila['precio']) ?></p>
        <p class="small text-muted">
            Precio de referencia en pesos colombianos, actualizado el <?= e(date('d/m/Y', strtotime($fila['fecha_actualizacion']))) ?>.
        </p>

        <div class="d-flex flex-wrap gap-2 mb-4">
            <!-- Abre el armador con esta pieza ya elegida -->
            <a href="<?= e(url('armador', 'index', [$cat => $fila['id']])) ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Agregar al armado
            </a>
            <?php if (!empty($fila['asin'])): ?>
                <!-- rel="sponsored" identifica el enlace como comercial (afiliados) -->
                <a href="<?= e(enlace_amazon($fila['asin'])) ?>" class="btn btn-outline-primary" target="_blank" rel="noopener sponsored">
                    Ver en Amazon <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i><span class="visually-hidden"> (se abre en otra pestaña)</span>
                </a>
            <?php endif; ?>
            <a href="<?= e(url('catalogo', 'index', ['cat' => [$cat]])) ?>" class="btn btn-outline-secondary">Comparar con otros</a>
        </div>

        <?php $ayudas = glosario(); ?>
        <p class="small"><i class="bi bi-lightbulb" aria-hidden="true"></i> <?= e($ayudas['categorias'][$cat] ?? '') ?></p>

        <h2 class="h5">Características</h2>
        <table class="table table-sm">
            <tbody>
                <?php foreach ($ficha as $dato): ?>
                    <tr>
                        <th scope="row" class="fw-normal w-50">
                            <?= e($dato['etiqueta']) ?>
                            <?php $termino = $ayudas['campos'][$cat][$dato['campo']] ?? ($dato['campo'] === 'sockets' ? 'socket' : null); ?>
                            <?php if ($termino): ?>
                                <!-- Explicación en lenguaje sencillo, tomada del glosario -->
                                <div class="small text-muted"><?= e($ayudas['terminos'][$termino]['texto']) ?></div>
                            <?php endif; ?>
                        </th>
                        <td><?= $dato['valor'] !== null ? e($dato['valor']) : '<span class="text-muted">Sin dato</span>' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (!empty($fila['descripcion'])): ?>
            <h2 class="h5 mt-4">Descripción</h2>
            <p><?= nl2br(e($fila['descripcion'])) ?></p>
        <?php endif; ?>
    </div>
</div>
