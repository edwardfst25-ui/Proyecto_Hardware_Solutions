<?php
/**
 * Tarjeta de un armado recomendado (prototipo de la figura 14).
 * Variables: $numero, $armado ['titulo', 'piezas', 'total', 'sobrante', 'evaluacion'],
 *            $catalogo (modelo Catalogo) y $presupuesto.
 */
$ev = $armado['evaluacion'];

// Enlace que abre el armador con todas las piezas de este armado ya elegidas.
$ids = [];
foreach ($armado['piezas'] as $cat => $fila) {
    if ($fila) {
        $ids[$cat] = $fila['id'];
    }
}
$urlArmador = url('armador', 'index', $ids + ['presupuesto' => (int)$presupuesto, 'reemplazar' => 1]);
?>
<article class="card tarjeta mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-2 mb-3">
            <h3 class="h5 mb-0">Configuración <?= (int)$numero ?> <span class="text-muted fw-normal">· <?= e($armado['titulo']) ?></span></h3>
            <?php if ($ev['compatible']): ?>
                <span class="badge text-bg-success"><i class="bi bi-check-lg" aria-hidden="true"></i> Compatible</span>
            <?php else: ?>
                <span class="badge text-bg-danger"><i class="bi bi-x-lg" aria-hidden="true"></i> Requiere revisión</span>
            <?php endif; ?>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <ul class="list-group list-group-flush">
                    <?php foreach ($armado['piezas'] as $cat => $fila): if (!$fila) continue; ?>
                        <li class="list-group-item d-flex align-items-center gap-3 px-0">
                            <?= foto_componente($cat, $fila['imagen'], 'foto-seleccion') ?>
                            <div class="flex-grow-1">
                                <div class="small text-muted"><?= e(ucfirst(Compatibilidad::PIEZAS[$cat])) ?></div>
                                <a href="<?= e(url('catalogo', 'detalle', ['cat' => $cat, 'id' => $fila['id']])) ?>"><?= e($fila['nombre']) ?></a>
                                <div class="small text-muted"><?= e($catalogo->resumen($cat, $fila)) ?></div>
                            </div>
                            <span class="text-nowrap"><?= precio($fila['precio']) ?></span>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($armado['piezas']['gpu'])): ?>
                        <li class="list-group-item px-0 small text-muted">
                            <i class="bi bi-info-circle" aria-hidden="true"></i>
                            Sin tarjeta gráfica: usa los gráficos integrados del procesador, suficientes para oficina, navegación y video.
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="col-lg-4">
                <div class="resumen-armado p-3 rounded h-100 d-flex flex-column">
                    <div class="small text-muted">Costo total</div>
                    <div class="fs-3 fw-bold"><?= precio($armado['total']) ?></div>
                    <div class="small mb-2">Te quedan <?= precio($armado['sobrante']) ?> de tu presupuesto.</div>
                    <div class="small text-muted mb-3">
                        Consumo estimado: <?= (int)$ev['consumo_estimado_w'] ?> W.
                        Fuente recomendada: <?= (int)$ev['potencia_recomendada_w'] ?> W o más.
                    </div>
                    <?php foreach (array_merge($ev['errores'], $ev['advertencias']) as $aviso): ?>
                        <div class="small text-warning-emphasis mb-2"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i> <?= e($aviso) ?></div>
                    <?php endforeach; ?>
                    <a href="<?= e($urlArmador) ?>" class="btn btn-primary mt-auto">Seleccionar armado</a>
                    <div class="form-text text-center">Podrás cambiar cualquier pieza.</div>
                </div>
            </div>
        </div>
    </div>
</article>
