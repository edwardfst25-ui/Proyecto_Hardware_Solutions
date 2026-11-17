<?php /** Glosario: cada pieza y cada término técnico explicados en lenguaje sencillo. */ ?>
<h1 class="h3 mb-1">Glosario</h1>
<p class="text-muted">Si es tu primera vez armando un computador, aquí está lo que significa cada pieza y cada término que verás en la plataforma.</p>

<h2 class="h5 mt-4 mb-3">Las piezas de un computador</h2>
<div class="row row-cols-1 row-cols-md-2 g-3">
    <?php foreach ($glosario['categorias'] as $tabla => $texto): ?>
        <div class="col">
            <div class="card tarjeta h-100">
                <div class="card-body d-flex gap-3">
                    <i class="bi <?= icono_categoria($tabla) ?> icono-mediano" aria-hidden="true"></i>
                    <div>
                        <h3 class="h6 mb-1"><?= e($nombres[$tabla] ?? $tabla) ?></h3>
                        <p class="small mb-1"><?= e($texto) ?></p>
                        <a class="small" href="<?= e(url('catalogo', 'index', ['cat' => [$tabla]])) ?>">Ver en el catálogo</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<h2 class="h5 mt-5 mb-3">Términos técnicos</h2>
<dl class="bg-white p-4 rounded tarjeta">
    <?php foreach ($glosario['terminos'] as $termino): ?>
        <dt><?= e($termino['nombre']) ?></dt>
        <dd class="text-muted"><?= e($termino['texto']) ?></dd>
    <?php endforeach; ?>
</dl>
