<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <a href="<?= e(url('panel')) ?>" class="small text-decoration-none">← Volver al panel</a>
        <h1 class="h4 mb-0">Administradores <span class="text-muted fs-6">(<?= count($administradores) ?>)</span></h1>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= e(url('administradores', 'contrasena')) ?>" class="btn btn-outline-secondary">Mi cuenta</a>
        <a href="<?= e(url('administradores', 'formulario')) ?>" class="btn btn-primary">+ Registrar administrador</a>
    </div>
</div>

<?php if ($temporal): ?>
    <div class="alert alert-warning" role="alert">
        <strong>Contraseña temporal de <?= e($temporal['nombre']) ?> (usuario <?= e($temporal['usuario']) ?>):</strong>
        <code class="fs-5 mx-2 user-select-all"><?= e($temporal['password']) ?></code><br>
        Entrégasela en persona. Solo se muestra esta vez; al entrar debe cambiarla en «Restablecer mi contraseña».
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-bordered table-hover bg-white align-middle">
        <thead class="table-dark">
            <tr><th>Usuario</th><th>Nombre completo</th><th>Correo</th><th>Registrado el</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            <?php foreach ($administradores as $a): $esYo = (int)$a['id'] === (int)$_SESSION['admin_id']; ?>
            <tr>
                <td><?= e($a['usuario']) ?><?= $esYo ? ' <span class="badge text-bg-secondary">Tú</span>' : '' ?></td>
                <td><?= e($a['nombre_completo']) ?></td>
                <td><?= $a['correo'] ? e($a['correo']) : '<span class="text-muted">Sin registrar</span>' ?></td>
                <td><?= e(date('d/m/Y', strtotime($a['fecha_creacion']))) ?></td>
                <td>
                    <?php if ($esYo): ?>
                        <a href="<?= e(url('administradores', 'contrasena')) ?>" class="btn btn-sm btn-outline-secondary">Mi cuenta</a>
                    <?php else: ?>
                        <form method="POST" action="<?= e(url('administradores', 'restablecer')) ?>" class="d-inline"
                              onsubmit="return confirm('Se generará una contraseña temporal para <?= e(addslashes($a['nombre_completo'] ?: $a['usuario'])) ?> y la actual dejará de servir. ¿Continuar?');">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Generar contraseña temporal</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<p class="small text-muted">La plataforma no envía correos: si un administrador olvida su contraseña, otro administrador le genera una temporal desde esta página.</p>
