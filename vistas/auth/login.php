<div class="d-flex justify-content-center">
    <div class="card shadow-sm" style="width: 100%; max-width: 400px;">
        <div class="card-body p-4">
            <h1 class="h4 card-title mb-4 text-center">Panel de Administración</h1>

            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= e(url('auth', 'login')) ?>">
                <?= csrf_campo() ?>
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" value="<?= e($usuario) ?>" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>
            <p class="form-text text-center mt-3 mb-0">¿Olvidaste tu contraseña? Pide a otro administrador que te genere una temporal desde el panel.</p>
        </div>
    </div>
</div>
