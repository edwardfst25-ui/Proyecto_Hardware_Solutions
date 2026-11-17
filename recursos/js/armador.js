/**
 * ARMADOR — lógica del navegador (JavaScript sin librerías; solo usa la ventana
 * modal de Bootstrap).
 *
 * Cada vez que cambia una pieza:
 *   1. se envían los ids elegidos al servidor con fetch();
 *   2. el servidor (motor de compatibilidad en PHP) responde en JSON;
 *   3. aquí solo se DIBUJA esa respuesta. Las reglas viven en un solo lugar: PHP.
 */
(function () {
    'use strict';

    const armador = document.getElementById('armador');
    const ranuras = Array.from(document.querySelectorAll('.ranura'));
    const catalogo = JSON.parse(document.getElementById('datos-catalogo').textContent);
    const cajaEstado = document.getElementById('estado');
    const listaVerificaciones = document.getElementById('verificaciones');
    const listaAvisos = document.getElementById('avisos');
    const textoTotal = document.getElementById('total');
    const textoConsumo = document.getElementById('consumo');
    const campoPresupuesto = document.getElementById('presupuesto');
    const cajaPresupuesto = document.getElementById('caja-presupuesto');
    const barraPresupuesto = document.getElementById('barra-presupuesto');
    const rellenoPresupuesto = document.getElementById('relleno-presupuesto');
    const textoPresupuesto = document.getElementById('texto-presupuesto');
    // Ventana para elegir
    const ventana = document.getElementById('ventana-elegir');
    const tituloVentana = document.getElementById('titulo-elegir');
    const listaElegir = document.getElementById('lista-elegir');
    const conteoElegir = document.getElementById('conteo-elegir');
    const filtroTexto = document.getElementById('filtro-texto');
    const filtroOrden = document.getElementById('filtro-orden');
    const filtroCompatibles = document.getElementById('filtro-compatibles');

    const CLAVE = 'hs_armado';   // nombre con que se guarda el armado en este navegador
    const categorias = ranuras.map(function (r) { return r.dataset.cat; });

    // Símbolo y texto de cada estado. El texto acompaña al símbolo para que
    // el significado no dependa solo del color.
    const ESTADOS = {
        ok:          { simbolo: '✔', clase: 'text-success',   texto: 'Correcto' },
        error:       { simbolo: '✖', clase: 'text-danger',    texto: 'Incompatible' },
        advertencia: { simbolo: '⚠', clase: 'text-warning-emphasis', texto: 'Advertencia' },
        sin_dato:    { simbolo: '?', clase: 'text-secondary', texto: 'Sin dato para verificar' },
        pendiente:   { simbolo: '○', clase: 'text-secondary', texto: 'Pendiente' },
    };

    let seleccion = {};          // { cpu: '22', ram: '16', ... } ids elegidos
    let incompatibles = {};      // { cpu: [ids], ... } según la última respuesta del servidor
    let categoriaAbierta = '';   // categoría que se está eligiendo en la ventana
    let totalActual = 0;
    let peticionActual = 0;      // para ignorar respuestas viejas si el usuario cambia rápido

    /* ---------- Comunicación con el servidor ---------- */

    function parametros() {
        const p = new URLSearchParams();
        categorias.forEach(function (cat) { if (seleccion[cat]) p.set(cat, seleccion[cat]); });
        return p;
    }

    /** Pide la evaluación al servidor y dibuja el resultado. */
    async function evaluar() {
        const numero = ++peticionActual;
        const p = parametros();
        try {
            const respuesta = await fetch(armador.dataset.urlEvaluar + '&' + p.toString());
            if (!respuesta.ok) throw new Error('Respuesta ' + respuesta.status);
            const datos = await respuesta.json();
            if (numero !== peticionActual) return; // llegó una respuesta más nueva
            incompatibles = datos.incompatibles;
            dibujarRanuras(datos.piezas);
            dibujarEstado(datos);
            dibujarVerificaciones(datos.resultado);
            dibujarCosto(datos);
            recordar(p);
        } catch (error) {
            cajaEstado.className = 'alert alert-danger mb-3';
            cajaEstado.textContent = 'No se pudo verificar la compatibilidad. Revisa tu conexión e inténtalo de nuevo.';
        }
    }

    /* ---------- Dibujo ---------- */

    /** Foto del componente o, si no tiene, el ícono de su categoría. */
    function crearFoto(imagen, icono, clase) {
        if (imagen) {
            const foto = document.createElement('img');
            foto.src = imagen; foto.alt = ''; foto.className = clase;
            return foto;
        }
        const caja = document.createElement('span');
        caja.className = clase + ' foto-vacia';
        const i = document.createElement('i');
        i.className = 'bi ' + icono;
        caja.append(i);
        return caja;
    }

    /** Cada tarjeta de la izquierda muestra la pieza elegida o "Sin elegir". */
    function dibujarRanuras(piezas) {
        ranuras.forEach(function (ranura) {
            const pieza = piezas[ranura.dataset.cat];
            const contenido = ranura.querySelector('.ranura-contenido');
            ranura.querySelector('.ranura-foto').replaceChildren(
                crearFoto(pieza ? pieza.imagen : null, ranura.dataset.icono, 'foto-ranura'));
            ranura.classList.toggle('ranura-elegida', Boolean(pieza));
            ranura.querySelector('.btn-quitar').hidden = !pieza;
            ranura.querySelector('.btn-elegir').textContent = pieza ? 'Cambiar' : 'Elegir';
            ranura.querySelector('.btn-elegir').className =
                'btn btn-sm btn-elegir ' + (pieza ? 'btn-outline-primary' : 'btn-primary');
            ranura.querySelector('.ranura-precio').textContent = pieza ? pieza.precio : '';

            if (!pieza) {
                contenido.className = 'ranura-contenido text-muted small';
                contenido.textContent = 'Sin elegir';
                return;
            }
            const nombre = document.createElement('a');
            nombre.href = pieza.enlace;          // ficha del componente
            nombre.className = 'd-block fw-semibold';
            nombre.textContent = pieza.nombre;
            const detalle = document.createElement('div');
            detalle.className = 'text-muted small';
            detalle.textContent = pieza.detalle;
            contenido.className = 'ranura-contenido';
            contenido.replaceChildren(nombre, detalle);
        });
    }

    /** Caja principal: resume el estado del armado en una frase. */
    function dibujarEstado(datos) {
        const r = datos.resultado;
        let clase = 'alert-secondary';
        let texto = 'Elige un componente para comenzar.';

        if (datos.elegidos > 0) {
            if (!r.compatible) {
                clase = 'alert-danger';
                texto = r.errores.length === 1
                    ? 'Hay 1 incompatibilidad. Revisa el detalle.'
                    : 'Hay ' + r.errores.length + ' incompatibilidades. Revisa el detalle.';
            } else if (!r.completo) {
                clase = 'alert-info';
                texto = 'Sin problemas hasta ahora. Falta elegir: ' + r.faltantes.join(', ') + '.';
            } else if (r.advertencias.length > 0) {
                clase = 'alert-warning';
                texto = 'El armado es compatible, con advertencias.';
            } else {
                clase = 'alert-success';
                texto = 'El armado es compatible.';
            }
        }
        cajaEstado.className = 'alert ' + clase + ' mb-3';
        cajaEstado.textContent = texto;
    }

    /** Lista regla por regla y, debajo, las advertencias generales. */
    function dibujarVerificaciones(r) {
        listaVerificaciones.replaceChildren();
        const mensajesDeReglas = [];

        r.verificaciones.forEach(function (v) {
            const estado = ESTADOS[v.estado] || ESTADOS.pendiente;
            const item = document.createElement('li');
            item.className = 'mb-2';

            const titulo = document.createElement('div');
            titulo.className = 'fw-semibold ' + estado.clase;
            titulo.textContent = estado.simbolo + ' ' + v.regla + ' — ' + estado.texto;

            const mensaje = document.createElement('div');
            mensaje.className = 'text-body-secondary';
            mensaje.textContent = v.mensaje;

            item.append(titulo, mensaje);
            listaVerificaciones.append(item);
            mensajesDeReglas.push(v.mensaje);
        });

        listaAvisos.replaceChildren();
        r.advertencias
            .filter(function (aviso) { return !mensajesDeReglas.includes(aviso); })
            .forEach(function (aviso) {
                const item = document.createElement('li');
                item.textContent = aviso;
                listaAvisos.append(item);
            });
    }

    function dibujarCosto(datos) {
        textoTotal.textContent = datos.total;
        totalActual = datos.total_numero;
        dibujarPresupuesto();
        textoConsumo.textContent = datos.elegidos > 0
            ? 'Consumo estimado: ' + datos.resultado.consumo_estimado_w + ' W · Fuente recomendada: '
              + datos.resultado.potencia_recomendada_w + ' W o más.'
            : '';
    }

    /* ---------- Ventana para elegir un componente ---------- */

    function abrirVentana(ranura) {
        categoriaAbierta = ranura.dataset.cat;
        tituloVentana.textContent = 'Elegir ' + ranura.dataset.nombre;
        filtroTexto.value = '';
        dibujarOpciones();
        bootstrap.Modal.getOrCreateInstance(ventana).show();
    }

    /** Dibuja las tarjetas de la categoría abierta, aplicando búsqueda, orden y filtro. */
    function dibujarOpciones() {
        const cat = categoriaAbierta;
        const ranura = ranuras.find(function (r) { return r.dataset.cat === cat; });
        const noSirven = (incompatibles[cat] || []).map(String);
        const palabras = filtroTexto.value.toLowerCase().split(/\s+/).filter(Boolean);

        let opciones = (catalogo[cat] || []).filter(function (c) {
            const texto = (c.nombre + ' ' + c.marca).toLowerCase();
            return palabras.every(function (palabra) { return texto.includes(palabra); });
        });
        const totalEncontrados = opciones.length;
        if (filtroCompatibles.checked) {
            opciones = opciones.filter(function (c) { return !noSirven.includes(String(c.id)); });
        }
        opciones.sort(function (a, b) {
            if (filtroOrden.value === 'precio_asc') return a.precio - b.precio;
            if (filtroOrden.value === 'precio_desc') return b.precio - a.precio;
            return a.nombre.localeCompare(b.nombre, 'es');
        });

        const ocultos = totalEncontrados - opciones.length;
        conteoElegir.textContent = opciones.length + (opciones.length === 1 ? ' opción' : ' opciones')
            + (ocultos > 0 ? ' · ' + ocultos + ' no compatibles con tu armado están ocultas' : '') + '.';

        listaElegir.replaceChildren();
        opciones.forEach(function (c) {
            const incompatible = noSirven.includes(String(c.id));
            const elegido = String(seleccion[cat] || '') === String(c.id);

            const columna = document.createElement('div');
            columna.className = 'col';
            const tarjeta = document.createElement('div');
            tarjeta.className = 'card tarjeta h-100 text-center' + (elegido ? ' tarjeta-elegida' : '');
            const cuerpo = document.createElement('div');
            cuerpo.className = 'card-body d-flex flex-column';

            const nombre = document.createElement('h3');
            nombre.className = 'h6 mb-1 mt-2';
            nombre.textContent = c.nombre;
            const detalle = document.createElement('div');
            detalle.className = 'small text-muted';
            detalle.textContent = c.marca + (c.detalle ? ' · ' + c.detalle : '');
            const precio = document.createElement('div');
            precio.className = 'fw-bold my-2 mt-auto pt-2';
            precio.textContent = c.precioTexto;

            const boton = document.createElement('button');
            boton.type = 'button';
            boton.className = 'btn btn-sm ' + (incompatible ? 'btn-outline-secondary' : 'btn-primary');
            boton.textContent = elegido ? 'Elegido' : (incompatible ? 'No compatible' : 'Agregar al armado');
            boton.disabled = incompatible || elegido;
            boton.addEventListener('click', function () {
                seleccion[cat] = String(c.id);
                bootstrap.Modal.getOrCreateInstance(ventana).hide();
                evaluar();
            });

            const ficha = document.createElement('a');
            ficha.href = c.enlace;
            ficha.target = '_blank';
            ficha.rel = 'noopener';
            ficha.className = 'small mt-2';
            ficha.textContent = 'Ver ficha completa';

            cuerpo.append(crearFoto(c.imagen, ranura.dataset.icono, 'foto-tarjeta'), nombre, detalle, precio, boton, ficha);
            tarjeta.append(cuerpo);
            columna.append(tarjeta);
            listaElegir.append(columna);
        });
    }

    /* ---------- Presupuesto (RF04: costo ajustable) ---------- */

    function presupuesto() {
        return Number(campoPresupuesto.value.replace(/[^0-9]/g, '')) || 0;
    }

    function pesos(valor) {
        return '$' + Math.round(valor).toLocaleString('es-CO');
    }

    /** Compara el total con el presupuesto: barra de avance y cuánto sobra o falta. */
    function dibujarPresupuesto() {
        const tope = presupuesto();
        cajaPresupuesto.hidden = tope === 0;
        if (tope === 0) return;

        const porcentaje = Math.round(totalActual / tope * 100);
        const excedido = totalActual > tope;
        rellenoPresupuesto.style.width = Math.min(porcentaje, 100) + '%';
        rellenoPresupuesto.className = 'progress-bar ' + (excedido ? 'bg-danger' : 'bg-success');
        barraPresupuesto.setAttribute('aria-valuenow', String(Math.min(porcentaje, 100)));
        textoPresupuesto.className = 'small mb-2 ' + (excedido ? 'text-danger fw-semibold' : 'text-success');
        textoPresupuesto.textContent = excedido
            ? 'Te pasas por ' + pesos(totalActual - tope) + ' (' + porcentaje + ' % de tu presupuesto).'
            : 'Te quedan ' + pesos(tope - totalActual) + ' (llevas el ' + porcentaje + ' % de tu presupuesto).';
    }

    /* ---------- Recordar y compartir el armado ---------- */

    /**
     * Guarda la selección en dos lugares:
     *  - en la dirección de la página, para poder compartir el armado con un enlace;
     *  - en localStorage, para que no se pierda al visitar el catálogo y volver.
     */
    function recordar(p) {
        if (presupuesto() > 0) p.set('presupuesto', String(presupuesto()));
        const texto = p.toString();
        history.replaceState(null, '', 'index.php?c=armador' + (texto ? '&' + texto : ''));
        try { localStorage.setItem(CLAVE, texto); } catch (error) { /* almacenamiento no disponible */ }
    }

    /**
     * Al abrir la página se recupera la selección:
     *  1. primero lo guardado en este navegador;
     *  2. encima, lo que venga en la dirección (por ejemplo "Agregar al armado" desde el catálogo).
     * Con &reemplazar=1 (botón "Seleccionar armado") se ignora lo guardado.
     */
    function recuperar() {
        const deUrl = new URLSearchParams(location.search);
        let guardado = new URLSearchParams();
        if (!deUrl.has('reemplazar')) {
            try { guardado = new URLSearchParams(localStorage.getItem(CLAVE) || ''); } catch (error) { /* se ignora */ }
        }
        categorias.forEach(function (cat) {
            const valor = deUrl.get(cat) || guardado.get(cat);
            // Solo se acepta un id que exista en el catálogo.
            if (valor && (catalogo[cat] || []).some(function (c) { return String(c.id) === valor; })) {
                seleccion[cat] = valor;
            }
        });
        campoPresupuesto.value = (deUrl.get('presupuesto') || guardado.get('presupuesto') || '').replace(/[^0-9]/g, '');
    }

    /** Copia al portapapeles la dirección de la página, que contiene el armado. */
    async function compartir() {
        const aviso = document.getElementById('aviso-compartir');
        try {
            await navigator.clipboard.writeText(location.href);
            aviso.textContent = 'Enlace copiado. Quien lo abra verá este mismo armado.';
        } catch (error) {
            // Navegadores sin permiso de portapapeles: se muestra para copiarlo a mano.
            window.prompt('Copia este enlace para compartir tu armado:', location.href);
        }
        setTimeout(function () { aviso.textContent = ''; }, 5000);
    }

    /* ---------- Eventos ---------- */

    ranuras.forEach(function (ranura) {
        ranura.querySelector('.btn-elegir').addEventListener('click', function () { abrirVentana(ranura); });
        ranura.querySelector('.btn-quitar').addEventListener('click', function () {
            delete seleccion[ranura.dataset.cat];
            evaluar();
        });
    });
    [filtroTexto, filtroOrden, filtroCompatibles].forEach(function (control) {
        control.addEventListener('input', dibujarOpciones);
    });
    campoPresupuesto.addEventListener('input', function () {
        dibujarPresupuesto();
        recordar(parametros()); // el presupuesto no cambia la compatibilidad: solo se guarda
    });
    document.getElementById('btn-limpiar').addEventListener('click', function () {
        seleccion = {};
        campoPresupuesto.value = '';
        evaluar();
    });
    document.getElementById('btn-compartir').addEventListener('click', compartir);

    recuperar();
    evaluar();
})();
