<h1 class="h4 mb-4">Categorías de componentes</h1>
<div class="row g-3 mb-5">
    <?php foreach ($tarjetas as $t): ?>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h2 class="h6 card-title"><i class="bi <?= icono_categoria($t['tabla']) ?> me-1" aria-hidden="true"></i> <?= e($t['nombre']) ?></h2>
                    <?php if ($t['disponible']): ?>
                        <p class="text-muted small flex-grow-1"><?= (int)$t['total'] ?> componentes registrados.</p>
                        <a href="<?= e(url('componentes', 'listar', ['cat' => $t['tabla']])) ?>" class="btn btn-sm btn-primary mt-2">Gestionar</a>
                    <?php else: ?>
                        <p class="text-muted small flex-grow-1">Próximamente.</p>
                        <button class="btn btn-sm btn-outline-secondary mt-2" disabled>Gestionar</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<h2 class="h4 mb-4">Recomendaciones y acceso</h2>
<div class="row g-3">
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <h3 class="h6 card-title"><i class="bi bi-magic me-1" aria-hidden="true"></i> Armados recomendados</h3>
                <p class="text-muted small flex-grow-1"><?= (int)$armados ?> armados creados por el equipo.</p>
                <a href="<?= e(url('armados', 'listar')) ?>" class="btn btn-sm btn-primary mt-2">Gestionar</a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <h3 class="h6 card-title"><i class="bi bi-people me-1" aria-hidden="true"></i> Administradores</h3>
                <p class="text-muted small flex-grow-1"><?= (int)$administradores ?> personas con acceso al panel.</p>
                <a href="<?= e(url('administradores', 'listar')) ?>" class="btn btn-sm btn-primary mt-2">Gestionar</a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <h3 class="h6 card-title"><i class="bi bi-key me-1" aria-hidden="true"></i> Mi cuenta</h3>
                <p class="text-muted small flex-grow-1">Tu correo y la contraseña con la que inicias sesión.</p>
                <a href="<?= e(url('administradores', 'contrasena')) ?>" class="btn btn-sm btn-primary mt-2">Abrir</a>
            </div>
        </div>
    </div>
</div>
