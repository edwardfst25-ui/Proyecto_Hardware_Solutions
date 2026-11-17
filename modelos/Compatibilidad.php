<?php
/**
 * MOTOR DE COMPATIBILIDAD (RF02)
 *
 * Recibe un armado (los componentes elegidos) y revisa 8 reglas:
 *   1. Socket del procesador  = socket de la placa madre
 *   2. Tipo de memoria RAM    = tipo que acepta la placa madre
 *   3. Tamaño de la placa     ≤ tamaño máximo que recibe el gabinete
 *   4. Socket del procesador  está entre los sockets que soporta el cooler
 *   5. Potencia de la fuente  ≥ consumo estimado del equipo (+ margen recomendado)
 *   6. Salida de video        hay tarjeta gráfica, o el procesador trae gráficos integrados
 *   7. Largo de la gráfica    ≤ largo máximo que admite el gabinete
 *   8. Altura del disipador   ≤ altura máxima que admite el gabinete
 *
 * Es una clase independiente: no usa la base de datos, ni $_POST, ni HTML.
 * Solo recibe arreglos y devuelve un arreglo, por eso sirve igual para la página
 * pública, para el panel de administración o para las pruebas.
 *
 * Diferencia entre ERROR y ADVERTENCIA:
 *   - error:       el equipo no se puede armar o no encendería así.
 *   - advertencia: se puede armar, pero hay algo que conviene revisar.
 *   - sin_dato:    la regla no se pudo evaluar porque falta registrar una medida.
 *   - pendiente:   faltan piezas por elegir para evaluar la regla.
 */
class Compatibilidad
{
    /**
     * Consumo aproximado de lo que la base de datos no registra
     * (placa madre, memoria RAM, discos y ventiladores).
     */
    public const CONSUMO_BASE_W = 75;

    /** Margen recomendado de la fuente sobre el consumo estimado (20 %). */
    public const MARGEN_FUENTE = 0.20;

    /** Consumo que se asume para un cooler que no lo tiene registrado. */
    public const CONSUMO_COOLER_W = 5;

    /** Tamaño de cada factor de forma, de menor a mayor. */
    private const TAMANOS = ['Mini-ITX' => 1, 'Micro-ATX' => 2, 'ATX' => 3];

    /** Las 8 piezas de un armado y cómo se nombran en los mensajes. */
    public const PIEZAS = [
        'cpu'            => 'procesador',
        'placa_madre'    => 'placa madre',
        'ram'            => 'memoria RAM',
        'gpu'            => 'tarjeta gráfica',
        'fuente_poder'   => 'fuente de poder',
        'almacenamiento' => 'almacenamiento',
        'gabinete'       => 'gabinete',
        'cooler'         => 'refrigeración',
    ];

    /**
     * Evalúa un armado.
     *
     * @param array $armado ['cpu' => fila|null, 'placa_madre' => fila|null, ...]
     *        Cada fila es el registro tal como sale de su tabla. La fila del cooler
     *        debe traer además 'sockets' => ['AM4', 'AM5', ...] (tabla cooler_socket).
     *        Las piezas que falten simplemente no se evalúan.
     *
     * @return array [
     *     'compatible'             => bool   (true si no hay ningún error),
     *     'completo'               => bool   (true si están todas las piezas obligatorias),
     *     'faltantes'              => string[] piezas obligatorias sin elegir,
     *     'errores'                => string[] mensajes de error,
     *     'advertencias'           => string[] mensajes de advertencia,
     *     'verificaciones'         => array[]  detalle regla por regla,
     *     'consumo_estimado_w'     => int,
     *     'potencia_recomendada_w' => int,
     * ]
     */
    public function evaluar(array $armado): array
    {
        $cpu      = $armado['cpu'] ?? null;
        $placa    = $armado['placa_madre'] ?? null;
        $ram      = $armado['ram'] ?? null;
        $gpu      = $armado['gpu'] ?? null;
        $fuente   = $armado['fuente_poder'] ?? null;
        $gabinete = $armado['gabinete'] ?? null;
        $cooler   = $armado['cooler'] ?? null;

        $consumo = $this->consumoEstimado($cpu, $gpu, $cooler);

        // La tarjeta gráfica es la única pieza opcional (la base de datos permite armados sin ella).
        $faltantes = [];
        foreach (self::PIEZAS as $clave => $nombre) {
            if ($clave !== 'gpu' && empty($armado[$clave])) {
                $faltantes[] = $nombre;
            }
        }

        $verificaciones = [
            $this->reglaSocket($cpu, $placa),
            $this->reglaRam($ram, $placa),
            $this->reglaFactorForma($placa, $gabinete),
            $this->reglaCooler($cooler, $cpu),
            $this->reglaPotencia($fuente, $consumo, $cpu, $gpu),
            $this->reglaVideo($cpu, $gpu, !$faltantes),
            $this->reglaLargoGrafica($gpu, $gabinete),
            $this->reglaAlturaDisipador($cooler, $gabinete),
        ];

        $errores = [];
        $advertencias = [];
        foreach ($verificaciones as $v) {
            if ($v['estado'] === 'error') {
                $errores[] = $v['mensaje'];
            } elseif ($v['estado'] === 'advertencia') {
                $advertencias[] = $v['mensaje'];
            }
        }

        return [
            'compatible'             => !$errores,
            'completo'               => !$faltantes,
            'faltantes'              => $faltantes,
            'errores'                => $errores,
            'advertencias'           => $advertencias,
            'verificaciones'         => $verificaciones,
            'consumo_estimado_w'     => $consumo,
            'potencia_recomendada_w' => $this->potenciaRecomendada($consumo),
        ];
    }

    /** Consumo estimado del equipo en vatios: procesador + gráfica + cooler + base fija. */
    public function consumoEstimado(?array $cpu, ?array $gpu, ?array $cooler): int
    {
        $total = self::CONSUMO_BASE_W;
        $total += (int)($cpu['consumo_w'] ?? 0);
        $total += (int)($gpu['consumo_w'] ?? 0);
        if ($cooler) {
            $total += (int)($cooler['consumo_w'] ?? self::CONSUMO_COOLER_W);
        }
        return $total;
    }

    /** Potencia de fuente recomendada: consumo + 20 %, redondeado hacia arriba a múltiplos de 50 W. */
    public function potenciaRecomendada(int $consumo): int
    {
        return (int)(ceil($consumo * (1 + self::MARGEN_FUENTE) / 50) * 50);
    }

    /* ======================= LAS 5 REGLAS ======================= */

    /** Regla 1: el procesador y la placa madre deben tener el mismo socket. */
    private function reglaSocket(?array $cpu, ?array $placa): array
    {
        $regla = 'Socket del procesador y la placa madre';
        if (!$cpu || !$placa) {
            return $this->pendiente($regla, 'Elige procesador y placa madre para verificar el socket.');
        }
        if ($cpu['socket'] === $placa['socket']) {
            return $this->resultado($regla, 'ok', "El procesador y la placa madre usan el mismo socket ({$cpu['socket']}).");
        }
        return $this->resultado(
            $regla,
            'error',
            "El procesador {$cpu['nombre']} usa socket {$cpu['socket']}, pero la placa madre {$placa['nombre']} "
            . "es de socket {$placa['socket']}. No encajan: elige una placa {$cpu['socket']} o un procesador {$placa['socket']}.",
            ['cpu', 'placa_madre']
        );
    }

    /** Regla 2: la memoria RAM debe ser del tipo que acepta la placa (DDR4 o DDR5). */
    private function reglaRam(?array $ram, ?array $placa): array
    {
        $regla = 'Tipo de memoria RAM';
        if (!$ram || !$placa) {
            return $this->pendiente($regla, 'Elige memoria RAM y placa madre para verificar el tipo de memoria.');
        }
        if ($ram['tipo'] === $placa['tipo_ram']) {
            return $this->resultado($regla, 'ok', "La memoria y la placa madre son {$ram['tipo']}.");
        }
        return $this->resultado(
            $regla,
            'error',
            "La memoria {$ram['nombre']} es {$ram['tipo']}, pero la placa madre {$placa['nombre']} solo acepta "
            . "{$placa['tipo_ram']}. Las ranuras son físicamente distintas: elige una memoria {$placa['tipo_ram']}.",
            ['ram', 'placa_madre']
        );
    }

    /**
     * Regla 3: la placa debe caber en el gabinete.
     * El gabinete guarda el tamaño MÁXIMO que recibe: uno ATX también recibe
     * Micro-ATX y Mini-ITX; uno Micro-ATX recibe Micro-ATX y Mini-ITX.
     */
    private function reglaFactorForma(?array $placa, ?array $gabinete): array
    {
        $regla = 'Tamaño de la placa madre y el gabinete';
        if (!$placa || !$gabinete) {
            return $this->pendiente($regla, 'Elige placa madre y gabinete para verificar que la placa quepa.');
        }
        $tamPlaca    = self::TAMANOS[$placa['factor_forma']] ?? null;
        $tamGabinete = self::TAMANOS[$gabinete['factor_forma_soportado']] ?? null;

        if ($tamPlaca === null || $tamGabinete === null) {
            return $this->resultado($regla, 'advertencia',
                'No se pudo verificar el tamaño: la placa o el gabinete no tienen un factor de forma reconocido.',
                ['placa_madre', 'gabinete']);
        }
        if ($tamPlaca <= $tamGabinete) {
            return $this->resultado($regla, 'ok',
                "La placa {$placa['factor_forma']} cabe en el gabinete (recibe hasta {$gabinete['factor_forma_soportado']}).");
        }
        return $this->resultado(
            $regla,
            'error',
            "La placa madre {$placa['nombre']} es {$placa['factor_forma']} y no cabe en el gabinete {$gabinete['nombre']}, "
            . "que recibe placas de tamaño {$gabinete['factor_forma_soportado']} o menor. Elige un gabinete más grande o una placa más pequeña.",
            ['placa_madre', 'gabinete']
        );
    }

    /** Regla 4: el cooler debe traer montaje para el socket del procesador (tabla cooler_socket). */
    private function reglaCooler(?array $cooler, ?array $cpu): array
    {
        $regla = 'Refrigeración y socket del procesador';
        if (!$cooler || !$cpu) {
            return $this->pendiente($regla, 'Elige refrigeración y procesador para verificar el montaje.');
        }
        $sockets = $cooler['sockets'] ?? [];

        if (!$sockets) {
            return $this->resultado($regla, 'advertencia',
                "No se pudo verificar la refrigeración: {$cooler['nombre']} no tiene sockets compatibles registrados en el catálogo.",
                ['cooler']);
        }
        if (in_array($cpu['socket'], $sockets, true)) {
            return $this->resultado($regla, 'ok', "{$cooler['nombre']} es compatible con el socket {$cpu['socket']}.");
        }
        return $this->resultado(
            $regla,
            'error',
            "{$cooler['nombre']} no trae montaje para el socket {$cpu['socket']} del procesador {$cpu['nombre']}. "
            . 'Solo es compatible con: ' . implode(', ', $sockets) . '.',
            ['cooler', 'cpu']
        );
    }

    /**
     * Regla 5: la fuente debe cubrir el consumo estimado.
     *   - fuente < consumo            -> error (no alcanza)
     *   - fuente < consumo + 20 %     -> advertencia (alcanza, pero muy justa)
     * Solo se marca como involucrada la fuente: es la pieza que se debe cambiar.
     */
    private function reglaPotencia(?array $fuente, int $consumo, ?array $cpu, ?array $gpu): array
    {
        $regla = 'Potencia de la fuente de poder';
        if (!$fuente || !$cpu) {
            return $this->pendiente($regla, 'Elige al menos procesador y fuente de poder para verificar la potencia.');
        }
        $potencia    = (int)$fuente['potencia_w'];
        $recomendada = $this->potenciaRecomendada($consumo);
        $detalle     = $gpu ? '' : ' (calculado sin tarjeta gráfica)';

        if ($potencia < $consumo) {
            return $this->resultado($regla, 'error',
                "La fuente {$fuente['nombre']} entrega {$potencia} W y el equipo necesita cerca de {$consumo} W{$detalle}. "
                . "No alcanza: elige una fuente de {$recomendada} W o más.",
                ['fuente_poder']);
        }
        if ($potencia < $consumo * (1 + self::MARGEN_FUENTE)) {
            return $this->resultado($regla, 'advertencia',
                "La fuente {$fuente['nombre']} ({$potencia} W) alcanza para los {$consumo} W estimados{$detalle}, pero queda con menos del "
                . "20 % de margen. Se recomienda una de {$recomendada} W o más para que no trabaje al límite.",
                ['fuente_poder']);
        }
        return $this->resultado($regla, 'ok',
            "La fuente de {$potencia} W cubre el consumo estimado de {$consumo} W{$detalle} con margen suficiente.");
    }

    /**
     * Regla 6: el equipo debe poder dar imagen.
     *   - Con tarjeta gráfica: correcto.
     *   - Sin tarjeta gráfica: solo sirve si el procesador trae gráficos integrados
     *     (columna cpu.graficos_integrados).
     * Mientras el armado está a medias, la falta de gráfica es solo una advertencia
     * (el usuario puede no haberla elegido todavía); con las demás piezas completas es un error.
     */
    private function reglaVideo(?array $cpu, ?array $gpu, bool $restoCompleto): array
    {
        $regla = 'Salida de video';
        if (!$cpu) {
            return $this->pendiente($regla, 'Elige el procesador para verificar cómo dará imagen el equipo.');
        }
        if ($gpu) {
            return $this->resultado($regla, 'ok', "La tarjeta gráfica {$gpu['nombre']} se encarga de la imagen.");
        }
        if ((int)($cpu['graficos_integrados'] ?? 0) === 1) {
            return $this->resultado($regla, 'ok',
                "Sin tarjeta gráfica: el equipo usa los gráficos integrados del procesador {$cpu['nombre']}. "
                . 'Sirven para oficina, navegación y video, no para juegos exigentes.');
        }
        return $this->resultado(
            $regla,
            $restoCompleto ? 'error' : 'advertencia',
            "El procesador {$cpu['nombre']} no tiene gráficos integrados: sin tarjeta gráfica el equipo no daría imagen. "
            . 'Agrega una tarjeta gráfica o elige un procesador con gráficos integrados.'
        );
    }

    /**
     * Regla 7: la tarjeta gráfica debe caber a lo largo dentro del gabinete.
     * Las dos medidas son opcionales en la base de datos: si falta alguna, la regla
     * queda "sin dato" (ni aprueba ni rechaza).
     */
    private function reglaLargoGrafica(?array $gpu, ?array $gabinete): array
    {
        $regla = 'Largo de la tarjeta gráfica y el gabinete';
        if (!$gpu || !$gabinete) {
            return $this->pendiente($regla, 'Elige tarjeta gráfica y gabinete para verificar que la tarjeta quepa.');
        }
        $largo  = (int)($gpu['largo_mm'] ?? 0);
        $maximo = (int)($gabinete['gpu_largo_max_mm'] ?? 0);

        if ($largo === 0 || $maximo === 0) {
            return $this->resultado($regla, 'sin_dato',
                'No se pudo verificar: falta registrar el largo de la tarjeta gráfica o el espacio del gabinete. Confírmalo en las fichas de los fabricantes.');
        }
        if ($largo <= $maximo) {
            return $this->resultado($regla, 'ok', "La tarjeta gráfica mide {$largo} mm y el gabinete admite hasta {$maximo} mm.");
        }
        return $this->resultado($regla, 'error',
            "La tarjeta gráfica {$gpu['nombre']} mide {$largo} mm de largo y el gabinete {$gabinete['nombre']} solo admite "
            . "tarjetas de hasta {$maximo} mm. No cabe: elige un gabinete más amplio o una tarjeta más corta.",
            ['gpu', 'gabinete']);
    }

    /**
     * Regla 8: el disipador debe caber a lo alto dentro del gabinete.
     * Solo aplica a la refrigeración por aire; en la líquida lo que importa es el
     * tamaño del radiador, que el catálogo no registra.
     */
    private function reglaAlturaDisipador(?array $cooler, ?array $gabinete): array
    {
        $regla = 'Altura del disipador y el gabinete';
        if (!$cooler || !$gabinete) {
            return $this->pendiente($regla, 'Elige refrigeración y gabinete para verificar que el disipador quepa.');
        }
        if (($cooler['tipo'] ?? '') !== 'Aire') {
            return $this->resultado($regla, 'sin_dato',
                'La refrigeración líquida depende del tamaño de radiador que admita el gabinete, un dato que el catálogo no registra. Confírmalo en la ficha del gabinete.');
        }
        $alto   = (int)($cooler['alto_mm'] ?? 0);
        $maximo = (int)($gabinete['cooler_alto_max_mm'] ?? 0);

        if ($alto === 0 || $maximo === 0) {
            return $this->resultado($regla, 'sin_dato',
                'No se pudo verificar: falta registrar la altura del disipador o el espacio del gabinete. Confírmalo en las fichas de los fabricantes.');
        }
        if ($alto <= $maximo) {
            return $this->resultado($regla, 'ok', "El disipador mide {$alto} mm de alto y el gabinete admite hasta {$maximo} mm.");
        }
        return $this->resultado($regla, 'error',
            "El disipador {$cooler['nombre']} mide {$alto} mm de alto y el gabinete {$gabinete['nombre']} solo admite "
            . "hasta {$maximo} mm. No cabe: elige un gabinete más ancho o un disipador más bajo.",
            ['cooler', 'gabinete']);
    }

    /* ======================= APOYO ======================= */

    /**
     * Arma el resultado de una regla.
     * $componentes = piezas que causan el problema (sirve para marcar opciones incompatibles).
     */
    private function resultado(string $regla, string $estado, string $mensaje, array $componentes = []): array
    {
        return ['regla' => $regla, 'estado' => $estado, 'mensaje' => $mensaje, 'componentes' => $componentes];
    }

    /** Regla que aún no se puede evaluar porque faltan piezas. */
    private function pendiente(string $regla, string $mensaje): array
    {
        return $this->resultado($regla, 'pendiente', $mensaje);
    }
}
