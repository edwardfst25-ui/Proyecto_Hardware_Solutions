<?php
/** Página de inicio: presenta la plataforma y lleva a sus tres funciones. */
class InicioControlador
{
    public function __construct(private PDO $pdo, private array $categorias)
    {
    }

    public function index(): void
    {
        // Accesos directos al catálogo por categoría, con el número de componentes de cada una.
        $accesos = [];
        foreach ((new Categoria($this->pdo))->todas() as $cat) {
            if (isset($this->categorias[$cat['tabla']])) {
                $accesos[] = [
                    'nombre' => $cat['nombre'],
                    'tabla'  => $cat['tabla'],
                    'total'  => (new Componente($this->pdo, $cat['tabla'], $this->categorias[$cat['tabla']]))->contar(),
                ];
            }
        }
        vista('inicio/index', ['titulo' => 'Inicio', 'accesos' => $accesos], 'publico');
    }

    /** Glosario: explica en lenguaje sencillo cada pieza y cada término técnico. */
    public function glosario(): void
    {
        $nombres = [];
        foreach ((new Categoria($this->pdo))->todas() as $cat) {
            $nombres[$cat['tabla']] = $cat['nombre'];
        }
        vista('inicio/glosario', ['titulo' => 'Glosario', 'nombres' => $nombres, 'glosario' => glosario()], 'publico');
    }
}
