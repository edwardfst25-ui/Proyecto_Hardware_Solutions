<?php
/**
 * Comparación lado a lado: una columna por componente y una fila por característica.
 * Todos son de la misma categoría, así que sus fichas tienen las mismas filas.
 */
$etiquetas = array_column($componentes[0]['ficha'], 'etiqueta');
?>
<nav aria-label="Ruta de navegación">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="<?= e(url('catalogo')) ?>">Catálogo</a></li>
        <li class="breadcrumb-item"><a href="<?= e(url('catalogo', 'index', ['cat' => [$cat]])) ?>"><?= e($categoria) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Comparar</li>
    </ol>
</nav>
<h1 class="h3 mb-3"><?= e($titulo) ?></h1>

<div class="table-responsive">
    <table class="table table-bordered bg-white align-middle text-center tabla-comparar">
        <thead>
            <tr>
                <td></td>
                <?php foreach ($componentes as $c): ?>
                    <th scope="col">
                        <?= foto_componente($cat, $c['fila']['imagen'], 'foto-tarjeta') ?>
                        <div class="mt-2"><?= e($c['fila']['nombre']) ?></div>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th scope="row" class="text-start">Precio</th>
                <?php foreach ($componentes as $c): $menor = (float)$c['fila']['precio'] === $precioMenor; ?>
                    <td class="fw-bold">
                        <?= precio($c['fila']['precio']) ?>
                        <?php if ($menor): ?><div class="badge text-bg-success fw-normal">Menor precio</div><?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
            <?php foreach ($etiquetas as $i => $etiqueta): ?>
                <tr>
                    <th scope="row" class="text-start fw-normal text-muted"><?= e($etiqueta) ?></th>
                    <?php foreach ($componentes as $c): $valor = $c['ficha'][$i]['valor']; ?>
                        <td><?= $valor !== null ? e($valor) : '<span class="text-muted">Sin dato</span>' ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td></td>
                <?php foreach ($componentes as $c): ?>
                    <td>
                        <a href="<?= e(url('catalogo', 'detalle', ['cat' => $cat, 'id' => $c['fila']['id']])) ?>" class="btn btn-outline-primary btn-sm mb-1">Ver detalles</a>
                        <a href="<?= e(url('armador', 'index', [$cat => $c['fila']['id']])) ?>" class="btn btn-primary btn-sm mb-1">Agregar al armado</a>
                    </td>
                <?php endforeach; ?>
            </tr>
        </tbody>
    </table>
</div>
<a href="<?= e(url('catalogo', 'index', ['cat' => [$cat]])) ?>" class="btn btn-outline-secondary">← Volver al catálogo</a>
