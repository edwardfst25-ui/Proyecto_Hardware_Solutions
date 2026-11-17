<?php
/**
 * CATÁLOGO PÚBLICO (RF01)
 *
 * Reúne las 8 tablas de componentes para las páginas públicas:
 * listado con búsqueda y filtros, detalle, comparación, armador y recomendaciones.
 */
class Catalogo
{
    /** Formas de ordenar permitidas: clave de la URL => ORDER BY (lista blanca). */
    public const ORDENES = [
        'nombre'      => ['texto' => 'Nombre (A-Z)',          'sql' => 'nombre ASC'],
        'precio_asc'  => ['texto' => 'Precio: menor a mayor', 'sql' => 'precio ASC, nombre ASC'],
        'precio_desc' => ['texto' => 'Precio: mayor a menor', 'sql' => 'precio DESC, nombre ASC'],
    ];

    public function __construct(private PDO $pdo, private array $categorias)
    {
    }

    /**
     * Busca componentes en una o varias categorías, con paginación.
     *
     * Como cada categoría es una tabla distinta, se arma un SELECT por tabla con las
     * columnas que todas comparten y se unen con UNION ALL. Así MySQL ordena y pagina
     * el resultado completo en una sola consulta.
     *
     * @param string   $texto  palabras a buscar en nombre o marca ('' = todo)
     * @param string[] $cats   tablas donde buscar (ya validadas contra la configuración)
     * @return array ['filas' => [...], 'total' => int]
     */
    public function buscar(string $texto, array $cats, string $orden, int $pagina, int $porPagina): array
    {
        $cats = array_values(array_intersect(array_keys($this->categorias), $cats));
        if (!$cats) {
            return ['filas' => [], 'total' => 0];
        }

        // Cada palabra debe aparecer en el nombre o en la marca.
        $palabras = array_filter(preg_split('/\s+/', trim($texto)));
        $condicion = '';
        $valoresPorTabla = [];
        foreach ($palabras as $palabra) {
            // Se escapan % y _ para que se busquen como texto y no como comodines de LIKE.
            $patron = '%' . addcslashes($palabra, '%_\\') . '%';
            $condicion .= ' AND (nombre LIKE ? OR marca LIKE ?)';
            array_push($valoresPorTabla, $patron, $patron);
        }

        $partes = [];
        $valores = [];
        foreach ($cats as $cat) {
            // $cat viene de la configuración (lista blanca), nunca del usuario.
            $partes[] = "SELECT '$cat' AS categoria, id, nombre, marca, precio, imagen FROM `$cat` WHERE 1 = 1$condicion";
            $valores = array_merge($valores, $valoresPorTabla);
        }
        $union = implode(' UNION ALL ', $partes);

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM ($union) AS todo");
        $stmt->execute($valores);
        $total = (int)$stmt->fetchColumn();

        $ordenSql = self::ORDENES[$orden]['sql'] ?? self::ORDENES['nombre']['sql'];
        $limite = max(1, $porPagina);
        $salto  = max(0, ($pagina - 1) * $limite);
        $stmt = $this->pdo->prepare("SELECT * FROM ($union) AS todo ORDER BY $ordenSql LIMIT $limite OFFSET $salto");
        $stmt->execute($valores);

        return ['filas' => $stmt->fetchAll(), 'total' => $total];
    }

    /** Un componente completo por categoría e id (el cooler incluye sus 'sockets'), o null. */
    public function componente(string $cat, int $id): ?array
    {
        if (!isset($this->categorias[$cat])) {
            return null;
        }
        $modelo = new Componente($this->pdo, $cat, $this->categorias[$cat]);
        $fila = $modelo->buscar($id);
        if ($fila && $cat === 'cooler') {
            $fila['sockets'] = $modelo->sockets($id);
        }
        return $fila;
    }

    /**
     * Todo el catálogo en memoria: [categoria => [id => fila]].
     * Lo usan el armador y el recomendador, que necesitan probar muchas combinaciones.
     * A cada cooler se le agrega 'sockets' (lo que necesita el motor de compatibilidad).
     */
    public function completo(): array
    {
        $catalogo = [];
        foreach (array_keys(Compatibilidad::PIEZAS) as $cat) {
            $modelo = new Componente($this->pdo, $cat, $this->categorias[$cat]);
            $catalogo[$cat] = array_column($modelo->todos(), null, 'id'); // indexado por id

            if ($cat === 'cooler') {
                $sockets = $modelo->socketsDeTodos();
                foreach ($catalogo[$cat] as $id => $fila) {
                    $catalogo[$cat][$id]['sockets'] = $sockets[$id] ?? [];
                }
            }
        }
        return $catalogo;
    }

    /**
     * Datos clave de un componente en una línea: "AM5 · 6 núcleos · 65 W".
     * Sale de los campos marcados con 'lista' en config/categorias.php.
     */
    public function resumen(string $cat, array $fila): string
    {
        $partes = [];
        foreach ($this->categorias[$cat]['campos'] as $campo => $def) {
            if (empty($def['lista']) || in_array($campo, ['nombre', 'marca', 'precio'], true)) {
                continue;
            }
            if (!isset($fila[$campo]) || $fila[$campo] === '') {
                continue;
            }
            if ($def['tipo'] === 'sino') {
                // Sí/No: en el resumen solo se menciona cuando es "sí" (ej. "gráficos integrados").
                if ((int)$fila[$campo] === 1) {
                    $partes[] = mb_strtolower($def['etiqueta']);
                }
                continue;
            }
            $texto = $fila[$campo] . (isset($def['sufijo']) ? ' ' . $def['sufijo'] : '');
            if ($campo === 'nucleos') {
                $texto .= ' núcleos';
            }
            $partes[] = $texto;
        }
        return implode(' · ', $partes);
    }

    /**
     * Ficha técnica de un componente: [['etiqueta' => 'Socket', 'valor' => 'AM5'], ...].
     * La usan la página de detalle y la tabla de comparación.
     */
    public function ficha(string $cat, array $fila): array
    {
        $ficha = [];
        foreach ($this->categorias[$cat]['campos'] as $campo => $def) {
            if (in_array($campo, ['nombre', 'precio', 'asin', 'descripcion'], true)) {
                continue;
            }
            $valor = $fila[$campo] ?? null;
            if ($def['tipo'] === 'sino' && $valor !== null) {
                $valor = (int)$valor === 1 ? 'Sí' : 'No';
            }
            $ficha[] = [
                'campo'    => $campo, // nombre de la columna: sirve para buscar su explicación en el glosario
                'etiqueta' => preg_replace('/\s*\(.*\)$/', '', $def['etiqueta']), // "Consumo (W)" -> "Consumo"
                'valor'    => ($valor === null || $valor === '') ? null : $valor . (isset($def['sufijo']) ? ' ' . $def['sufijo'] : ''),
            ];
        }
        if ($cat === 'cooler') {
            $ficha[] = ['campo' => 'sockets', 'etiqueta' => 'Sockets compatibles', 'valor' => implode(', ', $fila['sockets'] ?? []) ?: null];
        }
        return $ficha;
    }
}
