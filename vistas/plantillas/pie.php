    </main>
<?php if ($zona !== 'admin'): ?>
    <footer class="pie text-center py-3 small">
        © Hardware Solutions. Todos los derechos reservados.
        <span class="mx-1">·</span>
        <a href="<?= e(url('inicio', 'glosario')) ?>">Glosario</a>
        <span class="mx-1">·</span>
        <a href="<?= e(isset($_SESSION['admin_id']) ? url('panel') : url('auth', 'login')) ?>">Administración</a>
    </footer>
<?php endif; ?>
    <!-- JavaScript de Bootstrap: lo necesita el menú desplegable en pantallas pequeñas -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
