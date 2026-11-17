<?php
/** Panel principal del administrador: una tarjeta por categoría. */
class PanelControlador
{
    public function __construct(private PDO $pdo, private array $categorias)
    {
    }

    public function index(): void
    {
        requerir_admin();

        // El menú sale de la tabla `categorias` (menú dinámico de la sesión #3).
        // Una tarjeta queda activa si su tabla está definida en config/categorias.php.
        $tarjetas = [];
        foreach ((new Categoria($this->pdo))->todas() as $cat) {
            $disponible = isset($this->categorias[$cat['tabla']]);
            $tarjetas[] = [
                'nombre'     => $cat['nombre'],
                'tabla'      => $cat['tabla'],
                'disponible' => $disponible,
                'total'      => $disponible
                    ? (new Componente($this->pdo, $cat['tabla'], $this->categorias[$cat['tabla']]))->contar()
                    : null,
            ];
        }

        vista('panel/dashboard', [
            'titulo'   => 'Panel de administración',
            'tarjetas' => $tarjetas,
            'armados'  => (new ArmadoRecomendado($this->pdo))->contar(),
            'administradores' => (new Administrador($this->pdo))->contar(),
        ]);
    }
}
