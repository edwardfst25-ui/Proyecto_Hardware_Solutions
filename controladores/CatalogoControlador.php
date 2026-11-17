<?php
/**
 * CATÁLOGO PÚBLICO (RF01): listado con búsqueda y filtros, detalle de un
 * componente y comparación lado a lado.
 */
class CatalogoControlador
{
    private Catalogo $catalogo;
    private array $nombres = []; // [tabla => nombre visible de la categoría], desde la tabla `categorias`

    public function __construct(private PDO $pdo, private array $categorias)
    {
        $this->catalogo = new Catalogo($pdo, $categorias);
        foreach ((new Categoria($pdo))->todas() as $cat) {
            if (isset($categorias[$cat['tabla']])) {
                $this->nombres[$cat['tabla']] = $cat['nombre'];
            }
        }
    }

    /** Listado: ?q=texto&cat[]=cpu&cat[]=gpu&orden=precio_asc&pagina=2 */
    public function index(): void
    {
        // Todo lo que llega por la URL se valida contra listas conocidas.
        $texto = trim((string)($_GET['q'] ?? ''));
        $texto = mb_substr($texto, 0, 80);

        $pedidas = $_GET['cat'] ?? [];
        $pedidas = is_array($pedidas) ? $pedidas : [$pedidas];
        $filtro = array_values(array_intersect(array_keys($this->nombres), $pedidas));

        $orden = (string)($_GET['orden'] ?? 'nombre');
        if (!isset(Catalogo::ORDENES[$orden])) {
            $orden = 'nombre';
        }

        $porPagina = (int)ajuste('por_pagina');
        $pagina = max(1, (int)($_GET['pagina'] ?? 1));

        // Sin categorías marcadas se busca en todas.
        $resultado = $this->catalogo->buscar($texto, $filtro ?: array_keys($this->nombres), $orden, $pagina, $porPagina);
        $paginas = max(1, (int)ceil($resultado['total'] / $porPagina));

        vista('catalogo/index', [
            'titulo'   => $texto !== '' ? 'Buscar componentes' : 'Catálogo de componentes',
            'busqueda' => $texto,
            'filtro'   => $filtro,
            'orden'    => $orden,
            'pagina'   => min($pagina, $paginas),
            'paginas'  => $paginas,
            'total'    => $resultado['total'],
            'filas'    => $resultado['filas'],
            'nombres'  => $this->nombres,
        ], 'publico');
    }

    /** Ficha de un componente: ?cat=cpu&id=12 */
    public function detalle(): void
    {
        $cat = (string)($_GET['cat'] ?? '');
        $fila = $this->catalogo->componente($cat, (int)($_GET['id'] ?? 0));
        if (!$fila) {
            $this->noEncontrado('Ese componente no existe o fue retirado del catálogo.');
        }

        vista('catalogo/detalle', [
            'titulo'    => $fila['nombre'],
            'cat'       => $cat,
            'categoria' => $this->nombres[$cat],
            'fila'      => $fila,
            'ficha'     => $this->catalogo->ficha($cat, $fila),
        ], 'publico');
    }

    /** Comparación de 2 a 4 componentes de la misma categoría: ?cat=cpu&ids=1,5,9 */
    public function comparar(): void
    {
        $cat = (string)($_GET['cat'] ?? '');
        if (!isset($this->nombres[$cat])) {
            $this->noEncontrado('Esa categoría no existe.');
        }

        // "1,5,9" -> [1, 5, 9] sin repetidos y con el máximo permitido.
        $ids = array_unique(array_filter(array_map('intval', explode(',', (string)($_GET['ids'] ?? '')))));
        $ids = array_slice($ids, 0, (int)ajuste('max_comparar'));

        $componentes = [];
        foreach ($ids as $id) {
            $fila = $this->catalogo->componente($cat, $id);
            if ($fila) {
                $componentes[] = ['fila' => $fila, 'ficha' => $this->catalogo->ficha($cat, $fila)];
            }
        }
        if (count($componentes) < 2) {
            flash('warning', 'Elige al menos dos componentes de la misma categoría para compararlos.');
            redirigir(url('catalogo', 'index', ['cat' => [$cat]]));
        }

        vista('catalogo/comparar', [
            'titulo'      => 'Comparar ' . mb_strtolower($this->categorias[$cat]['plural']),
            'cat'         => $cat,
            'categoria'   => $this->nombres[$cat],
            'componentes' => $componentes,
            'precioMenor' => min(array_map(fn($c) => (float)$c['fila']['precio'], $componentes)),
        ], 'publico');
    }

    private function noEncontrado(string $mensaje): void
    {
        http_response_code(404);
        vista('plantillas/error', ['titulo' => 'No encontrado', 'mensaje' => $mensaje], 'publico');
        exit;
    }
}
