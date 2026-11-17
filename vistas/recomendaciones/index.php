<?php
/**
 * Selección de preferencias (prototipo de la figura 13): perfil de uso + presupuesto.
 * Es un formulario GET: el resultado queda en la URL y se puede compartir.
 */
?>
<h1 class="h3 mb-1">Seleccionar preferencias de uso</h1>
<p class="text-muted">Indica para qué vas a usar el computador y cuánto quieres invertir. Te proponemos equipos completos y compatibles.</p>

<?php if ($error): ?>
    <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
<?php endif; ?>

<form method="GET" action="index.php">
    <input type="hidden" name="c" value="recomendaciones">
    <input type="hidden" name="a" value="generar">

    <fieldset class="mb-4">
        <legend class="h5">1. ¿Para qué lo vas a usar?</legend>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
            <?php foreach ($perfiles as $perfil): ?>
                <div class="col">
                    <!-- El radio está oculto a la vista pero sigue funcionando con teclado; la tarjeta es su etiqueta -->
                    <input type="radio" class="btn-check" name="perfil" id="perfil_<?= (int)$perfil['id'] ?>" value="<?= (int)$perfil['id'] ?>"
                           <?= $perfilId === (int)$perfil['id'] ? 'checked' : '' ?> required>
                    <label class="card tarjeta tarjeta-perfil h-100 text-center" for="perfil_<?= (int)$perfil['id'] ?>">
                        <span class="card-body d-block">
                            <i class="bi <?= e($perfil['icono']) ?> icono-grande" aria-hidden="true"></i>
                            <span class="h5 d-block"><?= e($perfil['nombre']) ?></span>
                            <span class="text-muted small d-block"><?= e($perfil['descripcion']) ?></span>
                        </span>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <fieldset class="mb-4">
        <legend class="h5"><label for="presupuesto">2. ¿Cuál es tu presupuesto?</label></legend>
        <div class="row g-2 align-items-center">
            <div class="col-sm-6 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="text" inputmode="numeric" class="form-control" id="presupuesto" name="presupuesto"
                           value="<?= e($presupuesto) ?>" placeholder="Ej. 5000000" required aria-describedby="ayuda-presupuesto">
                    <span class="input-group-text">COP</span>
                </div>
            </div>
            <div class="col-auto d-flex flex-wrap gap-2">
                <?php foreach ([4000000, 6000000, 9000000, 14000000] as $monto): ?>
                    <button type="button" class="btn btn-outline-secondary btn-sm btn-monto" data-monto="<?= $monto ?>"><?= precio($monto) ?></button>
                <?php endforeach; ?>
            </div>
        </div>
        <div id="ayuda-presupuesto" class="form-text">Valor total para el equipo (torre), en pesos colombianos. No incluye monitor ni periféricos.</div>
    </fieldset>

    <button type="submit" class="btn btn-primary btn-lg">Generar recomendaciones</button>
</form>

<script>
// Los botones de monto rápido solo escriben el valor en el campo.
document.querySelectorAll('.btn-monto').forEach(function (boton) {
    boton.addEventListener('click', function () {
        document.getElementById('presupuesto').value = boton.dataset.monto;
    });
});
</script>
