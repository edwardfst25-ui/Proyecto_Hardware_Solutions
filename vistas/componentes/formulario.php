<?php
/**
 * Formulario genérico (crear y editar). Dibuja un control por cada campo de
 * config/categorias.php según su tipo. $valores trae lo que hay que mostrar
 * (el registro al editar, o lo que el usuario escribió si hubo errores).
 */
$urlLista = url('componentes', 'listar', ['cat' => $cat]);
?>
<a href="<?= e($urlLista) ?>" class="small text-decoration-none">← Volver a <?= e($def['plural']) ?></a>
<h1 class="h4 mb-4"><?= e($titulo) ?></h1>

<?php if ($errores): ?>
    <div class="alert alert-danger" role="alert">
        <?= e($errores['general'] ?? 'Hay campos por corregir. Revisa los mensajes en rojo.') ?>
    </div>
<?php endif; ?>

<!-- enctype="multipart/form-data" es obligatorio para poder enviar archivos. novalidate deja la validación al servidor. -->
<form method="POST" action="<?= e(url('componentes', 'guardar', ['cat' => $cat])) ?>" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm" novalidate>
    <?= csrf_campo() ?>
    <?php if ($registro): ?>
        <input type="hidden" name="id" value="<?= (int)$registro['id'] ?>">
    <?php endif; ?>

    <div class="row g-3">
        <?php foreach ($def['campos'] as $nombreCampo => $campo):
            $valor    = $valores[$nombreCampo] ?? '';
            $error    = $errores[$nombreCampo] ?? null;
            $invalido = $error ? ' is-invalid' : '';
            $ancho    = $campo['tipo'] === 'textolargo' ? 'col-12' : (in_array($nombreCampo, ['nombre', 'marca'], true) ? 'col-md-6' : 'col-md-4');
            $idCampo  = 'campo_' . $nombreCampo;
        ?>
            <div class="<?= $ancho ?>">
                <label for="<?= $idCampo ?>" class="form-label">
                    <?= e($campo['etiqueta']) ?><?= !empty($campo['requerido']) ? ' <span class="text-danger">*</span>' : '' ?>
                </label>

                <?php if ($campo['tipo'] === 'enum'): ?>
                    <select name="<?= $nombreCampo ?>" id="<?= $idCampo ?>" class="form-select<?= $invalido ?>">
                        <option value="">-- Selecciona --</option>
                        <?php foreach ($campo['opciones'] as $opcion): ?>
                            <option value="<?= e($opcion) ?>" <?= (string)$valor === $opcion ? 'selected' : '' ?>><?= e($opcion) ?></option>
                        <?php endforeach; ?>
                    </select>

                <?php elseif ($campo['tipo'] === 'sino'): ?>
                    <select name="<?= $nombreCampo ?>" id="<?= $idCampo ?>" class="form-select<?= $invalido ?>">
                        <option value="">-- Selecciona --</option>
                        <option value="1" <?= (string)$valor === '1' ? 'selected' : '' ?>>Sí</option>
                        <option value="0" <?= (string)$valor === '0' ? 'selected' : '' ?>>No</option>
                    </select>

                <?php elseif ($campo['tipo'] === 'textolargo'): ?>
                    <textarea name="<?= $nombreCampo ?>" id="<?= $idCampo ?>" class="form-control<?= $invalido ?>" rows="3"><?= e($valor) ?></textarea>

                <?php elseif ($campo['tipo'] === 'texto'): ?>
                    <input type="text" name="<?= $nombreCampo ?>" id="<?= $idCampo ?>" class="form-control<?= $invalido ?>"
                           value="<?= e($valor) ?>" maxlength="<?= (int)$campo['max'] ?>">

                <?php else: /* entero, decimal o precio */ ?>
                    <input type="number" name="<?= $nombreCampo ?>" id="<?= $idCampo ?>" class="form-control<?= $invalido ?>"
                           value="<?= e($valor) ?>" step="<?= $campo['tipo'] === 'entero' ? '1' : e($campo['paso'] ?? '0.01') ?>"
                           min="<?= e($campo['min'] ?? 0) ?>" max="<?= e($campo['max'] ?? Validador::PRECIO_MAX) ?>">
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="invalid-feedback"><?= e($error) ?></div>
                <?php elseif (!empty($campo['ayuda'])): ?>
                    <div class="form-text"><?= e($campo['ayuda']) ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if (isset($def['sockets'])): ?>
            <!-- Solo en Refrigeración: relación muchos-a-muchos con la tabla cooler_socket -->
            <div class="col-12">
                <span class="form-label d-block">Sockets compatibles <span class="text-danger">*</span></span>
                <?php foreach ($def['sockets'] as $socket): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="sockets[]" value="<?= e($socket) ?>" id="socket_<?= e($socket) ?>"
                               <?= in_array($socket, $socketsMarcados, true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="socket_<?= e($socket) ?>"><?= e($socket) ?></label>
                    </div>
                <?php endforeach; ?>
                <?php if (isset($errores['sockets'])): ?>
                    <div class="text-danger small mt-1"><?= e($errores['sockets']) ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="col-12">
            <label for="campo_imagen" class="form-label">Imagen del producto</label>
            <?php if ($registro && !empty($registro['imagen'])): ?>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <img src="galeria/componentes/<?= e($registro['imagen']) ?>" alt="Imagen actual" class="vista-previa">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="quitar_imagen" value="1" id="quitar_imagen">
                        <label class="form-check-label" for="quitar_imagen">Quitar la imagen actual</label>
                    </div>
                </div>
            <?php endif; ?>
            <input type="file" name="imagen" id="campo_imagen" class="form-control" accept="image/jpeg,image/png,image/webp">
            <div class="form-text">Opcional. JPG, PNG o WebP de máximo 2 MB.<?= $registro && !empty($registro['imagen']) ? ' Si eliges un archivo, reemplaza la imagen actual.' : '' ?></div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-4">Guardar</button>
    <a href="<?= e($urlLista) ?>" class="btn btn-secondary mt-4">Cancelar</a>
</form>
