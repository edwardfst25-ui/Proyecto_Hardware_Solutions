<?php
/**
 * Restablecer la contraseña propia en dos pasos (figura 21 del documento):
 * primero se verifica la identidad y después se establece la nueva contraseña.
 */
?>
<a href="<?= e(url('panel')) ?>" class="small text-decoration-none">← Volver al panel</a>
<h1 class="h4 mb-4">Mi cuenta</h1>

<div class="bg-white p-4 rounded shadow-sm mb-4" style="max-width: 480px;">
    <h2 class="h5">Correo electrónico</h2>
    <form method="POST" action="<?= e(url('administradores', 'correo')) ?>" novalidate>
        <?= csrf_campo() ?>
        <label for="correo" class="form-label">Tu correo</label>
        <input type="email" name="correo" id="correo" maxlength="120" class="form-control" value="<?= e($correo) ?>" placeholder="nombre@dominio.com">
        <div class="form-text">Se usará más adelante para restablecer la contraseña por correo.</div>
        <button type="submit" class="btn btn-primary mt-3">Guardar correo</button>
    </form>
</div>

<div class="bg-white p-4 rounded shadow-sm" style="max-width: 480px;">
    <h2 class="h5">Restablecer contraseña</h2>
<?php if (!$verificada): ?>
    <h2 class="h6">Paso 1 de 2: verifica tu identidad</h2>
    <form method="POST" action="<?= e(url('administradores', 'contrasena')) ?>" novalidate>
        <?= csrf_campo() ?>
        <input type="hidden" name="paso" value="verificar">
        <label for="password_actual" class="form-label">Contraseña actual</label>
        <input type="password" name="password_actual" id="password_actual" autocomplete="current-password" autofocus
               class="form-control<?= isset($errores['password_actual']) ? ' is-invalid' : '' ?>">
        <?php if (isset($errores['password_actual'])): ?><div class="invalid-feedback"><?= e($errores['password_actual']) ?></div><?php endif; ?>
        <button type="submit" class="btn btn-primary mt-3">Confirmar</button>
    </form>
    <p class="form-text mt-3 mb-0">¿No la recuerdas? Pide a otro administrador que te genere una contraseña temporal.</p>
<?php else: ?>
    <h2 class="h6">Paso 2 de 2: escribe la nueva contraseña</h2>
    <form method="POST" action="<?= e(url('administradores', 'contrasena')) ?>" novalidate>
        <?= csrf_campo() ?>
        <input type="hidden" name="paso" value="cambiar">
        <div class="mb-3">
            <label for="password" class="form-label">Nueva contraseña</label>
            <input type="password" name="password" id="password" autocomplete="new-password" autofocus
                   class="form-control<?= isset($errores['password']) ? ' is-invalid' : '' ?>">
            <?php if (isset($errores['password'])): ?>
                <div class="invalid-feedback"><?= e($errores['password']) ?></div>
            <?php else: ?>
                <div class="form-text">Mínimo <?= Administrador::PASSWORD_MIN ?> caracteres.</div>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="password_confirmar" class="form-label">Repetir la nueva contraseña</label>
            <input type="password" name="password_confirmar" id="password_confirmar" autocomplete="new-password"
                   class="form-control<?= isset($errores['password_confirmar']) ? ' is-invalid' : '' ?>">
            <?php if (isset($errores['password_confirmar'])): ?><div class="invalid-feedback"><?= e($errores['password_confirmar']) ?></div><?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Confirmar</button>
    </form>
<?php endif; ?>
</div>
