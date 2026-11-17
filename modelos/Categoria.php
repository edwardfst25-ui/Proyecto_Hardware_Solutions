<?php
/** Modelo de la tabla `categorias` (las 8 categorías del catálogo). */
class Categoria
{
    public function __construct(private PDO $pdo)
    {
    }

    /** Todas las categorías, en el orden en que se muestran en el panel. */
    public function todas(): array
    {
        return $this->pdo->query('SELECT id, nombre, tabla FROM categorias ORDER BY id')->fetchAll();
    }
}
