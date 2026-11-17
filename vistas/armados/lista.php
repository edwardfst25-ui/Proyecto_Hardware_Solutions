<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <a href="<?= e(url('panel')) ?>" class="small text-decoration-none">← Volver al panel</a>
        <h1 class="h4 mb-0">Armados recomendados <span class="text-muted fs-6">(<?= count($armados) ?>)</span></h1>
    </div>
    <a href="<?= e(url('armados', 'formulario')) ?>" class="btn btn-primary">+ Agregar armado</a>
</div>

<?php if (!$armados): ?>
    <div class="alert alert-info">
        Todavía no hay armados creados por el equipo. La página pública de recomendaciones funciona igual
        (calcula armados automáticamente); los que agregues aquí aparecerán primero, como «Seleccionados por nuestro equipo».
    </div>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-bordered table-hover bg-white align-middle">
        <thead class="table-dark">
            <tr><th>Perfil</th><th>Rango</th><th>Piezas principales</th><th>Total</th><th>Estado</th><th>Creado por</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            <?php foreach ($armados as $a): ?>
            <tr>
                <td><?= e($a['perfil']) ?></td>
                <td><?= e($a['rango_presupuesto']) ?></td>
                <td class="small">
                    <?= e($a['piezas']['cpu']['nombre'] ?? '—') ?><br>
                    <?= e($a['piezas']['gpu']['nombre'] ?? 'Sin tarjeta gráfica') ?><br>
                    <?= e($a['piezas']['ram']['nombre'] ?? '—') ?>
                </td>
                <td class="text-nowrap"><?= precio($a['total']) ?></td>
                <td>
                    <?php if ($a['evaluacion']['compatible']): ?>
                        <span class="badge text-bg-success">Compatible</span>
                    <?php else: ?>
                        <span class="badge text-bg-danger" title="<?= e(implode(' ', $a['evaluacion']['errores'])) ?>">Con errores</span>
                    <?php endif; ?>
                </td>
                <td class="small"><?= e($a['autor']) ?></td>
                <td class="text-nowrap">
                    <a href="<?= e(url('armados', 'formulario', ['id' => $a['id']])) ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                    <form method="POST" action="<?= e(url('armados', 'eliminar')) ?>" class="d-inline"
                          onsubmit="return confirm('¿Seguro que quieres eliminar este armado? Esta acción no se puede deshacer.');">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
