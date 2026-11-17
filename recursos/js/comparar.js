/**
 * COMPARADOR — recuerda qué componentes marcó el usuario con "Comparar"
 * y muestra una barra con el botón para ver la comparación.
 *
 * La selección se guarda en sessionStorage para que no se pierda al cambiar de
 * página del catálogo. Solo se comparan componentes de la misma categoría.
 */
(function () {
    'use strict';

    const barra = document.getElementById('barra-comparar');
    if (!barra) return;

    const texto = document.getElementById('texto-comparar');
    const boton = document.getElementById('btn-comparar');
    const casillas = Array.from(document.querySelectorAll('.casilla-comparar'));
    const maximo = Number(barra.dataset.maximo);
    const CLAVE = 'hs_comparar';

    // seleccion = { cat: 'cpu', categoria: 'Procesador', ids: ['3', '8'] }
    let seleccion = leer();
    let aviso = '';

    function leer() {
        try {
            const guardado = JSON.parse(sessionStorage.getItem(CLAVE));
            if (guardado && Array.isArray(guardado.ids)) return guardado;
        } catch (error) { /* almacenamiento no disponible: se sigue sin recordar */ }
        return { cat: '', categoria: '', ids: [] };
    }

    function guardar() {
        try { sessionStorage.setItem(CLAVE, JSON.stringify(seleccion)); } catch (error) { /* se ignora */ }
    }

    /** Actualiza las casillas y la barra según la selección. */
    function dibujar() {
        casillas.forEach(function (casilla) {
            casilla.checked = casilla.dataset.cat === seleccion.cat && seleccion.ids.includes(casilla.dataset.id);
        });

        const cantidad = seleccion.ids.length;
        barra.hidden = cantidad === 0;
        document.body.classList.toggle('con-barra-comparar', cantidad > 0);
        if (cantidad === 0) return;

        texto.textContent = aviso || (cantidad === 1
            ? '1 componente de ' + seleccion.categoria + ' elegido. Marca al menos uno más para comparar.'
            : cantidad + ' componentes de ' + seleccion.categoria + ' elegidos para comparar.');

        const listo = cantidad >= 2;
        boton.classList.toggle('disabled', !listo);
        boton.setAttribute('aria-disabled', String(!listo));
        boton.href = listo
            ? barra.dataset.url + '&cat=' + encodeURIComponent(seleccion.cat) + '&ids=' + seleccion.ids.join(',')
            : '#';
    }

    casillas.forEach(function (casilla) {
        casilla.addEventListener('change', function () {
            aviso = '';
            const id = casilla.dataset.id;

            if (!casilla.checked) {
                seleccion.ids = seleccion.ids.filter(function (otro) { return otro !== id; });
            } else if (casilla.dataset.cat !== seleccion.cat) {
                // Otra categoría: se empieza una selección nueva.
                if (seleccion.ids.length > 0) {
                    aviso = 'Solo se comparan componentes de la misma categoría. Empezamos una selección nueva de '
                        + casilla.dataset.categoria + '.';
                }
                seleccion = { cat: casilla.dataset.cat, categoria: casilla.dataset.categoria, ids: [id] };
            } else if (seleccion.ids.length >= maximo) {
                aviso = 'Puedes comparar máximo ' + maximo + ' componentes a la vez.';
            } else {
                seleccion.ids.push(id);
            }

            guardar();
            dibujar();
        });
    });

    document.getElementById('btn-limpiar-comparar').addEventListener('click', function () {
        seleccion = { cat: '', categoria: '', ids: [] };
        aviso = '';
        guardar();
        dibujar();
    });

    dibujar();
})();
