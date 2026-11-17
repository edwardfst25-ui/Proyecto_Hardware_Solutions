<?php
/**
 * Modelo de la tabla `armados_recomendados`: armados creados a mano por un
 * administrador para un perfil de uso y un rango de presupuesto.
 */
class ArmadoRecomendado
{
    public const RANGOS = ['Económico', 'Intermedio', 'Alto'];

    /** Columna de la tabla para cada pieza del armado. */
    public const COLUMNAS = [
        'cpu'            => 'cpu_id',
        'placa_madre'    => 'placa_id',
        'ram'            => 'ram_id',
        'gpu'            => 'gpu_id',
        'fuente_poder'   => 'fuente_id',
        'almacenamiento' => 'almacenamiento_id',
        'gabinete'       => 'gabinete_id',
        'cooler'         => 'cooler_id',
    ];

    public function __construct(private PDO $pdo)
    {
    }

    /** Todos los armados con el nombre del perfil y de quien lo creó. Opcional: solo los de un perfil. */
    public function todos(?int $perfilId = null): array
    {
        $sql = 'SELECT a.*, p.nombre AS perfil, COALESCE(ad.nombre_completo, ad.usuario) AS autor
                  FROM armados_recomendados a
                  JOIN perfiles_uso p ON p.id = a.perfil_id
                  JOIN administradores ad ON ad.id = a.creado_por'
             . ($perfilId !== null ? ' WHERE a.perfil_id = ?' : '')
             . ' ORDER BY p.id, a.id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($perfilId !== null ? [$perfilId] : []);
        return $stmt->fetchAll();
    }

    public function buscar(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM armados_recomendados WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** $datos = ['perfil_id', 'rango_presupuesto', 'cpu_id', ..., 'cooler_id']. */
    public function crear(array $datos, int $adminId): int
    {
        $columnas = array_merge(['perfil_id', 'rango_presupuesto'], array_values(self::COLUMNAS));
        $sql = 'INSERT INTO armados_recomendados (creado_por, ' . implode(', ', $columnas) . ')
                VALUES (?' . str_repeat(', ?', count($columnas)) . ')';
        $this->pdo->prepare($sql)->execute([$adminId, ...array_map(fn($c) => $datos[$c], $columnas)]);
        return (int)$this->pdo->lastInsertId();
    }

    public function actualizar(int $id, array $datos): void
    {
        $columnas = array_merge(['perfil_id', 'rango_presupuesto'], array_values(self::COLUMNAS));
        $sql = 'UPDATE armados_recomendados SET ' . implode(', ', array_map(fn($c) => "$c = ?", $columnas)) . ' WHERE id = ?';
        $this->pdo->prepare($sql)->execute([...array_map(fn($c) => $datos[$c], $columnas), $id]);
    }

    public function eliminar(int $id): void
    {
        $this->pdo->prepare('DELETE FROM armados_recomendados WHERE id = ?')->execute([$id]);
    }

    public function contar(): int
    {
        return (int)$this->pdo->query('SELECT COUNT(*) FROM armados_recomendados')->fetchColumn();
    }

    /**
     * Convierte una fila de la tabla (que solo tiene ids) en las piezas completas,
     * usando el catálogo en memoria: [categoria => fila|null].
     */
    public static function piezas(array $armado, array $catalogo): array
    {
        $piezas = [];
        foreach (self::COLUMNAS as $cat => $columna) {
            $piezas[$cat] = $catalogo[$cat][(int)($armado[$columna] ?? 0)] ?? null;
        }
        return $piezas;
    }
}
