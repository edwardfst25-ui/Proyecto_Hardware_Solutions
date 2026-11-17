<?php
/** Modelo de la tabla `administradores`. */
class Administrador
{
    /** Longitud mínima de una contraseña. */
    public const PASSWORD_MIN = 8;

    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Comprueba usuario y contraseña.
     * Devuelve los datos del administrador si son correctos, o null si no.
     */
    public function verificar(string $usuario, string $password): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, usuario, password_hash, nombre_completo FROM administradores WHERE usuario = ?'
        );
        $stmt->execute([$usuario]);
        $admin = $stmt->fetch();

        // La contraseña nunca se guarda en texto: se compara contra su hash.
        if ($admin && password_verify($password, $admin['password_hash'])) {
            return $admin;
        }
        return null;
    }

    /** Todos los administradores (sin el hash de la contraseña). */
    public function todos(): array
    {
        return $this->pdo->query(
            'SELECT id, usuario, nombre_completo, correo, fecha_creacion FROM administradores ORDER BY id'
        )->fetchAll();
    }

    public function buscar(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, usuario, password_hash, nombre_completo, correo FROM administradores WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** ¿Ya hay un administrador con ese nombre de usuario? */
    public function existeUsuario(string $usuario): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM administradores WHERE usuario = ?');
        $stmt->execute([$usuario]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** ¿Otro administrador ya usa ese correo? ($exceptoId = el propio) */
    public function existeCorreo(string $correo, int $exceptoId = 0): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM administradores WHERE correo = ? AND id <> ?');
        $stmt->execute([$correo, $exceptoId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Guarda el correo de un administrador. */
    public function actualizarCorreo(int $id, string $correo): void
    {
        $this->pdo->prepare('UPDATE administradores SET correo = ? WHERE id = ?')->execute([$correo, $id]);
    }

    /** Registra un administrador. La contraseña se guarda cifrada con password_hash(). */
    public function crear(string $usuario, string $nombreCompleto, string $password, string $correo): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO administradores (usuario, password_hash, nombre_completo, correo) VALUES (?, ?, ?, ?)');
        $stmt->execute([$usuario, password_hash($password, PASSWORD_DEFAULT), $nombreCompleto, $correo]);
        return (int)$this->pdo->lastInsertId();
    }

    /** Guarda una contraseña nueva (cifrada) para un administrador. */
    public function cambiarPassword(int $id, string $password): void
    {
        $stmt = $this->pdo->prepare('UPDATE administradores SET password_hash = ? WHERE id = ?');
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    public function contar(): int
    {
        return (int)$this->pdo->query('SELECT COUNT(*) FROM administradores')->fetchColumn();
    }
}
