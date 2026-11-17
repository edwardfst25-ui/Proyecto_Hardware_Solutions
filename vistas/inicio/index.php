<section class="text-center py-4">
    <h1 class="display-6 fw-bold">Arma tu computador sin equivocarte</h1>
    <p class="lead text-muted mx-auto" style="max-width: 44rem;">
        Explora componentes, comprueba que sean compatibles entre sí y conoce cuánto cuesta tu equipo antes de comprar.
    </p>
</section>

<section class="row g-3 mb-5">
    <div class="col-md-4">
        <div class="card tarjeta h-100 text-center">
            <div class="card-body d-flex flex-column">
                <i class="bi bi-magic icono-grande" aria-hidden="true"></i>
                <h2 class="h5">Recomendaciones</h2>
                <p class="text-muted flex-grow-1">Dinos para qué lo vas a usar y cuánto quieres gastar: te proponemos equipos completos.</p>
                <a href="<?= e(url('recomendaciones')) ?>" class="btn btn-primary">Quiero una recomendación</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card tarjeta h-100 text-center">
            <div class="card-body d-flex flex-column">
                <i class="bi bi-tools icono-grande" aria-hidden="true"></i>
                <h2 class="h5">Armado personalizado</h2>
                <p class="text-muted flex-grow-1">Elige pieza por pieza. Verificamos la compatibilidad y sumamos el costo al instante.</p>
                <a href="<?= e(url('armador')) ?>" class="btn btn-primary">Armar mi PC</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card tarjeta h-100 text-center">
            <div class="card-body d-flex flex-column">
                <i class="bi bi-grid icono-grande" aria-hidden="true"></i>
                <h2 class="h5">Catálogo</h2>
                <p class="text-muted flex-grow-1">Busca, filtra y compara las características y precios de cada componente.</p>
                <a href="<?= e(url('catalogo')) ?>" class="btn btn-primary">Ver el catálogo</a>
            </div>
        </div>
    </div>
</section>

<section class="alert alert-light border d-flex flex-wrap align-items-center gap-2 mb-5">
    <i class="bi bi-lightbulb fs-4" aria-hidden="true"></i>
    <span class="me-auto">¿Primera vez armando un computador? En el glosario explicamos cada pieza y cada término con palabras sencillas.</span>
    <a href="<?= e(url('inicio', 'glosario')) ?>" class="btn btn-outline-primary btn-sm">Abrir el glosario</a>
</section>

<section>
    <h2 class="h5 mb-3">Explorar por categoría</h2>
    <div class="row row-cols-2 row-cols-md-4 g-3">
        <?php foreach ($accesos as $acceso): ?>
            <div class="col">
                <a href="<?= e(url('catalogo', 'index', ['cat' => [$acceso['tabla']]])) ?>" class="card tarjeta h-100 text-center text-decoration-none">
                    <div class="card-body">
                        <i class="bi <?= icono_categoria($acceso['tabla']) ?> icono-mediano" aria-hidden="true"></i>
                        <div class="fw-semibold text-body"><?= e($acceso['nombre']) ?></div>
                        <div class="small text-muted"><?= (int)$acceso['total'] ?> componentes</div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
