<?php
/**
 * MODELO GENÉRICO DE COMPONENTES
 *
 * Una sola clase atiende las 8 tablas (cpu, gpu, ram...). Al crearla se le dice
 * con qué tabla trabajar y cuáles son sus campos (vienen de config/categorias.php).
 *
 * Seguridad: el nombre de la tabla y de las columnas NUNCA viene del usuario,
 * siempre de la configuración. Los valores siempre viajan en consultas preparadas (?).
 */
class Componente
{
    private array $columnas; // nombres de las columnas editables de esta tabla

    public function __construct(private PDO $pdo, private string $tabla, array $definicion)
    {
        $this->columnas = array_keys($definicion['campos']);
    }

    /** Todos los registros de la tabla, ordenados por nombre. */
    public function todos(): array
    {
        return $this->pdo->query("SELECT * FROM `{$this->tabla}` ORDER BY nombre")->fetchAll();
    }

    /** Un registro por su id, o null si no existe. */
    public function buscar(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `{$this->tabla}` WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** ¿Ya existe otro registro con ese nombre en la tabla? ($exceptoId = el que se está editando) */
    public function existeNombre(string $nombre, int $exceptoId = 0): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `{$this->tabla}` WHERE nombre = ? AND id <> ?");
        $stmt->execute([$nombre, $exceptoId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Cuántos registros tiene la tabla. */
    public function contar(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM `{$this->tabla}`")->fetchColumn();
    }

    /** Inserta un registro y devuelve su id. $datos = ['columna' => valor]. */
    public function crear(array $datos): int
    {
        $columnas = implode(', ', array_map(fn($c) => "`$c`", $this->columnas));
        $marcas   = implode(', ', array_fill(0, count($this->columnas), '?'));
        $stmt = $this->pdo->prepare("INSERT INTO `{$this->tabla}` ($columnas) VALUES ($marcas)");
        $stmt->execute($this->valores($datos));
        return (int)$this->pdo->lastInsertId();
    }

    /** Actualiza un registro existente. */
    public function actualizar(int $id, array $datos): void
    {
        $asignaciones = implode(', ', array_map(fn($c) => "`$c` = ?", $this->columnas));
        $stmt = $this->pdo->prepare("UPDATE `{$this->tabla}` SET $asignaciones WHERE id = ?");
        $stmt->execute([...$this->valores($datos), $id]);
    }

    /** Guarda (o borra, con null) la ruta de la imagen de un registro. */
    public function actualizarImagen(int $id, ?string $ruta): void
    {
        $stmt = $this->pdo->prepare("UPDATE `{$this->tabla}` SET imagen = ? WHERE id = ?");
        $stmt->execute([$ruta, $id]);
    }

    /**
     * Elimina un registro. Si el componente está usado en un armado recomendado,
     * MySQL lo impide (llave foránea) y PDO lanza una PDOException.
     */
    public function eliminar(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM `{$this->tabla}` WHERE id = ?");
        $stmt->execute([$id]);
    }

    /** Devuelve los valores en el mismo orden que $this->columnas. */
    private function valores(array $datos): array
    {
        return array_map(fn($c) => $datos[$c] ?? null, $this->columnas);
    }

    /* ---------- Relación cooler <-> sockets (tabla cooler_socket) ---------- */

    /** Sockets compatibles de un cooler: ['AM4', 'AM5', ...]. */
    public function sockets(int $coolerId): array
    {
        $stmt = $this->pdo->prepare('SELECT socket FROM cooler_socket WHERE cooler_id = ? ORDER BY socket');
        $stmt->execute([$coolerId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /** Sockets de todos los coolers en una sola consulta: [cooler_id => ['AM4', ...]]. */
    public function socketsDeTodos(): array
    {
        $mapa = [];
        foreach ($this->pdo->query('SELECT cooler_id, socket FROM cooler_socket ORDER BY socket') as $fila) {
            $mapa[$fila['cooler_id']][] = $fila['socket'];
        }
        return $mapa;
    }

    /**
     * Reemplaza los sockets de un cooler. Se hace dentro de una transacción:
     * o se guardan todos los cambios, o no se guarda ninguno.
     */
    public function guardarSockets(int $coolerId, array $sockets): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare('DELETE FROM cooler_socket WHERE cooler_id = ?')->execute([$coolerId]);
            $insertar = $this->pdo->prepare('INSERT INTO cooler_socket (cooler_id, socket) VALUES (?, ?)');
            foreach ($sockets as $socket) {
                $insertar->execute([$coolerId, $socket]);
            }
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
