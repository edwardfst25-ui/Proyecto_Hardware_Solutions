<?php $urlLista = url('administradores', 'listar'); ?>
<a href="<?= e($urlLista) ?>" class="small text-decoration-none">← Volver a administradores</a>
<h1 class="h4 mb-4">Registrar administrador</h1>

<?php if ($errores): ?>
    <div class="alert alert-danger" role="alert">Los datos no son válidos o están incompletos. Corrige la información antes de continuar.</div>
<?php endif; ?>

<form method="POST" action="<?= e(url('administradores', 'guardar')) ?>" class="bg-white p-4 rounded shadow-sm" novalidate autocomplete="off">
    <?= csrf_campo() ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="usuario" class="form-label">Usuario <span class="text-danger">*</span></label>
            <input type="text" name="usuario" id="usuario" maxlength="50" value="<?= e($valores['usuario'] ?? '') ?>"
                   class="form-control<?= isset($errores['usuario']) ? ' is-invalid' : '' ?>">
            <?php if (isset($errores['usuario'])): ?>
                <div class="invalid-feedback"><?= e($errores['usuario']) ?></div>
            <?php else: ?>
                <div class="form-text">Con este dato iniciará sesión (por ejemplo, su número de documento).</div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <label for="nombre_completo" class="form-label">Nombre completo <span class="text-danger">*</span></label>
            <input type="text" name="nombre_completo" id="nombre_completo" maxlength="100" value="<?= e($valores['nombre_completo'] ?? '') ?>"
                   class="form-control<?= isset($errores['nombre_completo']) ? ' is-invalid' : '' ?>">
            <?php if (isset($errores['nombre_completo'])): ?><div class="invalid-feedback"><?= e($errores['nombre_completo']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-6">
            <label for="correo" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
            <input type="email" name="correo" id="correo" maxlength="120" value="<?= e($valores['correo'] ?? '') ?>"
                   class="form-control<?= isset($errores['correo']) ? ' is-invalid' : '' ?>">
            <?php if (isset($errores['correo'])): ?>
                <div class="invalid-feedback"><?= e($errores['correo']) ?></div>
            <?php else: ?>
                <div class="form-text">Se usará más adelante para restablecer la contraseña por correo.</div>
            <?php endif; ?>
        </div>
        <div class="col-md-6 d-none d-md-block"></div>
        <div class="col-md-6">
            <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
            <input type="password" name="password" id="password" autocomplete="new-password"
                   class="form-control<?= isset($errores['password']) ? ' is-invalid' : '' ?>">
            <?php if (isset($errores['password'])): ?>
                <div class="invalid-feedback"><?= e($errores['password']) ?></div>
            <?php else: ?>
                <div class="form-text">Mínimo <?= Administrador::PASSWORD_MIN ?> caracteres. Se guarda cifrada.</div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <label for="password_confirmar" class="form-label">Repetir contraseña <span class="text-danger">*</span></label>
            <input type="password" name="password_confirmar" id="password_confirmar" autocomplete="new-password"
                   class="form-control<?= isset($errores['password_confirmar']) ? ' is-invalid' : '' ?>">
            <?php if (isset($errores['password_confirmar'])): ?><div class="invalid-feedback"><?= e($errores['password_confirmar']) ?></div><?php endif; ?>
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-4">Confirmar</button>
    <a href="<?= e($urlLista) ?>" class="btn btn-secondary mt-4">Cancelar</a>
</form>
