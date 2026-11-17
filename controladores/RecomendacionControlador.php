<?php
/**
 * RECOMENDACIONES PERSONALIZADAS (RF03)
 *
 * index:   el usuario elige su perfil de uso y su presupuesto.
 * generar: muestra los armados recomendados (los creados por el equipo y los
 *          que arma el Recomendador automáticamente).
 */
class RecomendacionControlador
{
    public function __construct(private PDO $pdo, private array $categorias)
    {
    }

    public function index(): void
    {
        $this->mostrarFormulario(0, '', null);
    }

    public function generar(): void
    {
        $perfilId = (int)($_GET['perfil'] ?? 0);
        $entrada  = trim((string)($_GET['presupuesto'] ?? ''));
        // Se aceptan puntos de miles: "5.000.000" -> 5000000
        $presupuesto = (float)preg_replace('/[^\d]/', '', $entrada);

        $perfil = (new Perfil($this->pdo))->buscar($perfilId);
        if (!$perfil) {
            $this->mostrarFormulario($perfilId, $entrada, 'Elige para qué vas a usar el computador.');
            return;
        }
        if ($presupuesto <= 0 || $presupuesto > Validador::PRECIO_MAX) {
            $this->mostrarFormulario($perfilId, $entrada, 'Escribe tu presupuesto en pesos, solo con números. Ejemplo: 5000000.');
            return;
        }

        $catalogo  = (new Catalogo($this->pdo, $this->categorias))->completo();
        $motor     = new Compatibilidad();
        $criterios = Perfil::criterios($perfilId);

        // 1) Armados generados automáticamente.
        $generados = (new Recomendador($motor))->recomendar($catalogo, $criterios, $presupuesto);

        // 2) Armados creados por el equipo para este perfil que caben en el presupuesto.
        $delEquipo = [];
        foreach ((new ArmadoRecomendado($this->pdo))->todos($perfilId) as $armado) {
            $piezas = ArmadoRecomendado::piezas($armado, $catalogo);
            $total = array_sum(array_map(fn($f) => $f ? (float)$f['precio'] : 0, $piezas));
            if ($total <= $presupuesto) {
                $delEquipo[] = [
                    'titulo'     => 'Armado ' . mb_strtolower($armado['rango_presupuesto']) . ' para ' . $perfil['nombre'],
                    'piezas'     => $piezas,
                    'total'      => $total,
                    'sobrante'   => $presupuesto - $total,
                    'evaluacion' => $motor->evaluar($piezas),
                ];
            }
        }

        vista('recomendaciones/resultados', [
            'titulo'      => 'Recomendaciones de armado',
            'perfil'      => $perfil,
            'criterio'    => $criterios['criterio'],
            'presupuesto' => $presupuesto,
            'generados'   => $generados['armados'],
            'costoMinimo' => $generados['costo_minimo'],
            'delEquipo'   => $delEquipo,
            'catalogo'    => new Catalogo($this->pdo, $this->categorias),
        ], 'publico');
    }

    private function mostrarFormulario(int $perfilId, string $presupuesto, ?string $error): void
    {
        $perfiles = (new Perfil($this->pdo))->todos();
        foreach ($perfiles as &$perfil) {
            $perfil['icono'] = Perfil::criterios((int)$perfil['id'])['icono'];
        }
        unset($perfil);

        vista('recomendaciones/index', [
            'titulo'      => 'Seleccionar preferencias de uso',
            'perfiles'    => $perfiles,
            'perfilId'    => $perfilId,
            'presupuesto' => $presupuesto,
            'error'       => $error,
        ], 'publico');
    }
}
