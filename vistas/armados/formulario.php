<?php
/** Formulario de un armado recomendado: perfil, rango y las 8 piezas. */
$urlLista = url('armados', 'listar');
?>
<a href="<?= e($urlLista) ?>" class="small text-decoration-none">← Volver a armados recomendados</a>
<h1 class="h4 mb-4"><?= e($titulo) ?></h1>

<?php if (isset($errores['compatibilidad'])): ?>
    <div class="alert alert-danger" role="alert">
        <strong>El armado no es compatible y no se guardó.</strong>
        <ul class="mb-0 mt-2">
            <?php foreach ($errores['compatibilidad'] as $mensaje): ?><li><?= e($mensaje) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php elseif ($errores): ?>
    <div class="alert alert-danger" role="alert">Hay campos por corregir. Revisa los mensajes en rojo.</div>
<?php endif; ?>

<form method="POST" action="<?= e(url('armados', 'guardar')) ?>" class="bg-white p-4 rounded shadow-sm" novalidate>
    <?= csrf_campo() ?>
    <?php if ($registro): ?><input type="hidden" name="id" value="<?= (int)$registro['id'] ?>"><?php endif; ?>

    <div class="row g-3">
        <div class="col-md-6">
            <label for="perfil_id" class="form-label">Perfil de uso <span class="text-danger">*</span></label>
            <select name="perfil_id" id="perfil_id" class="form-select<?= isset($errores['perfil_id']) ? ' is-invalid' : '' ?>">
                <option value="">-- Selecciona --</option>
                <?php foreach ($perfiles as $perfil): ?>
                    <option value="<?= (int)$perfil['id'] ?>" <?= (int)($valores['perfil_id'] ?? 0) === (int)$perfil['id'] ? 'selected' : '' ?>><?= e($perfil['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errores['perfil_id'])): ?><div class="invalid-feedback"><?= e($errores['perfil_id']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-6">
            <label for="rango_presupuesto" class="form-label">Rango de presupuesto <span class="text-danger">*</span></label>
            <select name="rango_presupuesto" id="rango_presupuesto" class="form-select<?= isset($errores['rango_presupuesto']) ? ' is-invalid' : '' ?>">
                <option value="">-- Selecciona --</option>
                <?php foreach (ArmadoRecomendado::RANGOS as $rango): ?>
                    <option value="<?= e($rango) ?>" <?= ($valores['rango_presupuesto'] ?? '') === $rango ? 'selected' : '' ?>><?= e($rango) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errores['rango_presupuesto'])): ?><div class="invalid-feedback"><?= e($errores['rango_presupuesto']) ?></div><?php endif; ?>
        </div>

        <?php foreach (ArmadoRecomendado::COLUMNAS as $cat => $columna): $error = $errores[$columna] ?? null; ?>
            <div class="col-md-6">
                <label for="<?= e($columna) ?>" class="form-label">
                    <?= e(ucfirst(Compatibilidad::PIEZAS[$cat])) ?>
                    <?= $cat === 'gpu' ? '<span class="text-muted">(opcional si el procesador tiene gráficos integrados)</span>' : '<span class="text-danger">*</span>' ?>
                </label>
                <select name="<?= e($columna) ?>" id="<?= e($columna) ?>" class="form-select<?= $error ? ' is-invalid' : '' ?>">
                    <option value=""><?= $cat === 'gpu' ? '-- Sin tarjeta gráfica --' : '-- Selecciona --' ?></option>
                    <?php foreach ($listas[$cat] ?? [] as $id => $texto): ?>
                        <option value="<?= (int)$id ?>" <?= (int)($valores[$columna] ?? 0) === (int)$id ? 'selected' : '' ?>><?= e($texto) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($error): ?><div class="invalid-feedback"><?= e($error) ?></div><?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <p class="form-text mt-3 mb-0">Al guardar se verifica la compatibilidad. Si hay algún error, el armado no se guarda y se indica qué corregir.</p>
    <button type="submit" class="btn btn-primary mt-3">Guardar</button>
    <a href="<?= e($urlLista) ?>" class="btn btn-secondary mt-3">Cancelar</a>
</form>
