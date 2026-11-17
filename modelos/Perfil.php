<?php
/** Modelo de la tabla `perfiles_uso` (Gaming, Edición de video...). */
class Perfil
{
    public function __construct(private PDO $pdo)
    {
    }

    public function todos(): array
    {
        return $this->pdo->query('SELECT id, nombre, descripcion FROM perfiles_uso ORDER BY id')->fetchAll();
    }

    public function buscar(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, nombre, descripcion FROM perfiles_uso WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Criterios de recomendación de un perfil (config/perfiles.php), o los de 'defecto'. */
    public static function criterios(int $id): array
    {
        $config = require RAIZ . '/config/perfiles.php';
        return $config[$id] ?? $config['defecto'];
    }
}
