<?php /** Resultados de la recomendación (prototipo de la figura 14). */ ?>
<h1 class="h3 mb-1">Recomendaciones de armado</h1>
<p class="text-muted mb-1">
    Configuraciones para <strong><?= e($perfil['nombre']) ?></strong> con un presupuesto de <strong><?= precio($presupuesto) ?></strong>.
    <a href="<?= e(url('recomendaciones', 'index')) ?>">Cambiar preferencias</a>
</p>
<p class="small text-muted mb-4"><i class="bi bi-info-circle" aria-hidden="true"></i> <?= e($criterio) ?></p>

<?php if (!$generados && !$delEquipo): ?>
    <div class="alert alert-warning">
        <?php if ($costoMinimo !== null): ?>
            Con <?= precio($presupuesto) ?> no alcanza para un equipo completo con los componentes del catálogo.
            El armado más económico cuesta <strong><?= precio($costoMinimo) ?></strong>.
            <a class="alert-link" href="<?= e(url('recomendaciones', 'generar', ['perfil' => $perfil['id'], 'presupuesto' => (int)ceil($costoMinimo)])) ?>">Ver ese armado</a>.
        <?php else: ?>
            Por ahora el catálogo no tiene componentes suficientes para proponer un equipo completo.
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php $numero = 0; ?>

<?php if ($delEquipo): ?>
    <h2 class="h5 mb-3">Seleccionados por nuestro equipo</h2>
    <?php foreach ($delEquipo as $armado): $numero++; ?>
        <?php require __DIR__ . '/_armado.php'; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($generados): ?>
    <?php if ($delEquipo): ?><h2 class="h5 mb-3">Calculados para tu presupuesto</h2><?php endif; ?>
    <?php foreach ($generados as $armado): $numero++; ?>
        <?php require __DIR__ . '/_armado.php'; ?>
    <?php endforeach; ?>
    <p class="small text-muted">
        Las configuraciones calculadas se arman automáticamente: se parte del equipo compatible más económico y se mejora cada pieza
        según la prioridad del perfil, sin superar tu presupuesto. Los precios son de referencia.
    </p>
<?php endif; ?>
