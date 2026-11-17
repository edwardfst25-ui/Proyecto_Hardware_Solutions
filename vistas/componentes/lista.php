<?php
/**
 * Listado genérico. Las columnas salen de los campos marcados con 'lista' => true
 * en config/categorias.php, por eso la misma vista sirve para las 8 categorías.
 */
$columnas = array_filter($def['campos'], fn($campo) => !empty($campo['lista']));
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <a href="<?= e(url('panel')) ?>" class="small text-decoration-none">← Volver al panel</a>
        <h1 class="h4 mb-0"><?= e($def['plural']) ?> <span class="text-muted fs-6">(<?= count($filas) ?>)</span></h1>
    </div>
    <a href="<?= e(url('componentes', 'formulario', ['cat' => $cat])) ?>" class="btn btn-primary">+ Agregar <?= e($def['singular']) ?></a>
</div>

<?php if (!$filas): ?>
    <div class="alert alert-info">Todavía no hay registros en esta categoría. Usa el botón «Agregar» para crear el primero.</div>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-bordered table-hover bg-white align-middle">
        <thead class="table-dark">
            <tr>
                <th>Imagen</th>
                <?php foreach ($columnas as $campo): ?>
                    <th><?= e(preg_replace('/\s*\(.*\)$/', '', $campo['etiqueta'])) ?></th>
                <?php endforeach; ?>
                <?php if (isset($def['sockets'])): ?><th>Sockets</th><?php endif; ?>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($filas as $fila): ?>
            <tr>
                <td class="text-center">
                    <?php if (!empty($fila['imagen'])): ?>
                        <img src="galeria/componentes/<?= e($fila['imagen']) ?>" alt="" class="miniatura">
                    <?php else: ?>
                        <span class="text-muted small">Sin foto</span>
                    <?php endif; ?>
                </td>
                <?php foreach ($columnas as $nombreCampo => $campo): $valor = $fila[$nombreCampo]; ?>
                    <td>
                        <?php if ($valor === null || $valor === ''): ?>
                            <span class="text-muted">—</span>
                        <?php elseif ($campo['tipo'] === 'sino'): ?>
                            <?= (int)$valor === 1 ? 'Sí' : 'No' ?>
                        <?php elseif ($campo['tipo'] === 'precio'): ?>
                            <?= precio($valor) ?>
                        <?php else: ?>
                            <?= e($valor) ?><?= isset($campo['sufijo']) ? ' ' . e($campo['sufijo']) : '' ?>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
                <?php if (isset($def['sockets'])): ?>
                    <td class="small"><?= e(implode(', ', $sockets[$fila['id']] ?? [])) ?: '<span class="text-danger">Sin sockets</span>' ?></td>
                <?php endif; ?>
                <td class="text-nowrap">
                    <a href="<?= e(url('componentes', 'formulario', ['cat' => $cat, 'id' => $fila['id']])) ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                    <!-- Eliminar va por POST (con código CSRF) y pide confirmación antes de enviarse. -->
                    <form method="POST" action="<?= e(url('componentes', 'eliminar', ['cat' => $cat])) ?>" class="d-inline"
                          onsubmit="return confirm('¿Seguro que quieres eliminar «<?= e(addslashes($fila['nombre'])) ?>»? Esta acción no se puede deshacer.');">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="id" value="<?= (int)$fila['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
