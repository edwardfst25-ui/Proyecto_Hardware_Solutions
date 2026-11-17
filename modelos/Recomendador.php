<?php
/**
 * RECOMENDADOR DE ARMADOS (RF03)
 *
 * Dado un perfil de uso y un presupuesto, arma hasta 3 configuraciones completas,
 * compatibles y que no se pasan del presupuesto.
 *
 * Igual que el motor de compatibilidad, es una clase independiente: recibe el
 * catálogo como arreglo y no toca la base de datos ni el HTML.
 *
 * CÓMO FUNCIONA (por cada socket de procesador que haya en el catálogo):
 *   1. Punto de partida: el armado MÁS BARATO posible para ese socket.
 *      Si ni ese cabe en el presupuesto, ese socket se descarta.
 *   2. Mejoras: con el dinero que sobra se va subiendo de gama pieza por pieza,
 *      en el orden de prioridad del perfil, sin que ninguna pieza supere la parte
 *      del presupuesto que el perfil le asigna (los "pesos").
 *   3. Cada cambio se valida con el motor de compatibilidad: si genera un error,
 *      se descarta. La fuente de poder se reajusta sola cuando sube el consumo.
 * Al final se ordenan los armados por qué tan bien aprovechan el presupuesto.
 *
 * PERFILES SIN TARJETA GRÁFICA ('sin_gpu' en config/perfiles.php, hoy Ofimática):
 *   el armado no lleva gráfica y solo se consideran procesadores con gráficos
 *   integrados (cpu.graficos_integrados = 1), para que el equipo sí dé imagen.
 *
 * CÓMO SE DECIDE QUÉ PIEZA ES "MEJOR" (ver el método calidad()):
 *   - RAM: más capacidad y luego más velocidad.
 *   - Almacenamiento: NVMe > SSD SATA > HDD y luego más capacidad.
 *   - Fuente: mejor certificación y luego más potencia.
 *   - Procesador, gráfica, placa, gabinete y cooler: el catálogo no tiene pruebas de
 *     rendimiento, así que se toma el precio como indicador de gama (LIMITACIÓN).
 */
class Recomendador
{
    /** Las piezas se dejan mejorar hasta 1,5 veces su parte del presupuesto en la segunda pasada. */
    private const HOLGURA_SEGUNDA_PASADA = 1.5;

    /** Una alternativa se muestra solo si alcanza este porcentaje del puntaje de la mejor. */
    private const PUNTAJE_MINIMO = 0.80;

    private const CERTIFICACIONES = ['Sin certificación', '80+ White', '80+ Bronze', '80+ Silver', '80+ Gold', '80+ Platinum', '80+ Titanium'];
    private const TIPOS_ALMACENAMIENTO = ['HDD' => 1, 'SSD SATA' => 2, 'SSD NVMe' => 3];

    private array $catalogo = [];
    private array $criterios = [];
    private float $presupuesto = 0;
    private bool $sinGpu = false; // true: armados sin tarjeta gráfica (procesador con gráficos integrados)

    public function __construct(private Compatibilidad $motor)
    {
    }

    /**
     * @param array $catalogo  [categoria => [id => fila]] (Catalogo::completo())
     * @param array $criterios criterios del perfil (config/perfiles.php)
     * @return array [
     *     'armados'      => [ ['titulo', 'piezas' => [cat => fila], 'total', 'sobrante', 'evaluacion'], ... ],
     *     'costo_minimo' => float|null  (el armado más barato posible; null si el catálogo no permite ninguno)
     * ]
     */
    public function recomendar(array $catalogo, array $criterios, float $presupuesto, int $maximo = 3): array
    {
        $this->catalogo = $catalogo;
        $this->criterios = $criterios;
        $this->sinGpu = !empty($criterios['sin_gpu']);

        $sockets = array_unique(array_column($catalogo['cpu'], 'socket'));
        $armados = [];
        $costoMinimo = null;

        foreach ($sockets as $socket) {
            // Primero se intenta respetando las preferencias del perfil (RAM mínima, SSD);
            // si así no alcanza el dinero, se intenta sin ellas.
            $minimoSocket = $this->armadoMasBarato($socket, false);
            if ($minimoSocket === null) {
                continue; // el catálogo no permite ningún armado con este socket
            }
            if ($costoMinimo === null || $this->total($minimoSocket) < $costoMinimo) {
                $costoMinimo = $this->total($minimoSocket);
            }

            foreach ([true, false] as $conPreferencias) {
                $base = $conPreferencias ? $this->armadoMasBarato($socket, true) : $minimoSocket;
                if ($base === null) {
                    continue;
                }
                $costo = $this->total($base);
                if ($costo <= $presupuesto) {
                    $this->presupuesto = $presupuesto;
                    $armados[] = $this->mejorar($base, $conPreferencias);
                    break; // ya hay armado para este socket
                }
            }
        }

        // Los que mejor aprovechan el presupuesto según el perfil van primero.
        usort($armados, fn($a, $b) => $this->puntaje($b) <=> $this->puntaje($a));
        // Se descartan las alternativas muy inferiores a la mejor (menos del 80 % de su puntaje).
        if ($armados) {
            $mejorPuntaje = $this->puntaje($armados[0]);
            $armados = array_filter($armados, fn($a) => $this->puntaje($a) >= $mejorPuntaje * self::PUNTAJE_MINIMO);
        }
        $armados = array_slice($armados, 0, $maximo);

        // Si hay menos de 3 opciones, se agrega una versión más económica de la mejor.
        if ($armados && count($armados) < $maximo) {
            $socket = $armados[0]['cpu']['socket'];
            $base = $this->armadoMasBarato($socket, true) ?? $this->armadoMasBarato($socket, false);
            if ($base !== null && $this->total($base) <= $presupuesto * 0.8) {
                $this->presupuesto = $presupuesto * 0.8;
                $economico = $this->mejorar($base, true);
                if ($this->ids($economico) !== $this->ids($armados[0])) {
                    $economico['_economico'] = true;
                    $armados[] = $economico;
                }
            }
        }

        // Presentación de cada armado.
        $salida = [];
        foreach ($armados as $i => $piezas) {
            $economico = !empty($piezas['_economico']);
            unset($piezas['_economico']);
            $total = $this->total($piezas);
            $plataforma = $piezas['cpu']['marca'] . ' ' . $piezas['cpu']['socket'];
            $salida[] = [
                'titulo'     => $economico ? 'Opción más económica' : ($i === 0 ? 'Mejor aprovechamiento de tu presupuesto' : "Alternativa en plataforma $plataforma"),
                'plataforma' => $plataforma,
                'piezas'     => $piezas,
                'total'      => $total,
                'sobrante'   => $presupuesto - $total,
                'evaluacion' => $this->motor->evaluar($piezas),
            ];
        }

        return ['armados' => $salida, 'costo_minimo' => $costoMinimo];
    }

    /* ======================= PASO 1: ARMADO MÁS BARATO ======================= */

    /** El armado compatible más barato para un socket, o null si el catálogo no lo permite. */
    private function armadoMasBarato(string $socket, bool $conPreferencias): ?array
    {
        // En perfiles sin tarjeta gráfica el procesador debe traer gráficos integrados.
        $cpu     = $this->masBarato('cpu', fn($f) => $f['socket'] === $socket
            && (!$this->sinGpu || (int)($f['graficos_integrados'] ?? 0) === 1));
        $cooler  = $this->masBarato('cooler', fn($f) => in_array($socket, $f['sockets'], true));
        $gpu     = $this->sinGpu ? null : $this->masBarato('gpu');
        $almacen = $this->masBarato('almacenamiento', fn($f) => !$conPreferencias || $this->cumplePreferencias('almacenamiento', $f))
                ?? $this->masBarato('almacenamiento');
        if (!$cpu || !$cooler || (!$gpu && !$this->sinGpu) || !$almacen) {
            return null;
        }

        // Placa, RAM y gabinete dependen entre sí (tipo de RAM y tamaño), así que se
        // prueba cada placa del socket con su RAM y su gabinete más baratos y se
        // conserva el trío de menor costo.
        $mejorTrio = null;
        foreach ($this->catalogo['placa_madre'] as $placa) {
            if ($placa['socket'] !== $socket) {
                continue;
            }
            $ram = $this->masBarato('ram', fn($f) => $f['tipo'] === $placa['tipo_ram']
                && (!$conPreferencias || $this->cumplePreferencias('ram', $f)));
            $gabinete = $this->masBarato('gabinete', fn($f) => $this->sinErrores(['placa_madre' => $placa, 'gabinete' => $f]));
            if (!$ram || !$gabinete) {
                continue;
            }
            $costo = $placa['precio'] + $ram['precio'] + $gabinete['precio'];
            if ($mejorTrio === null || $costo < $mejorTrio['costo']) {
                $mejorTrio = ['costo' => $costo, 'placa' => $placa, 'ram' => $ram, 'gabinete' => $gabinete];
            }
        }
        if ($mejorTrio === null) {
            return null;
        }

        $armado = [
            'cpu'            => $cpu,
            'placa_madre'    => $mejorTrio['placa'],
            'ram'            => $mejorTrio['ram'],
            'gpu'            => $gpu,
            'fuente_poder'   => null,
            'almacenamiento' => $almacen,
            'gabinete'       => $mejorTrio['gabinete'],
            'cooler'         => $cooler,
        ];
        return $this->ajustarFuente($armado);
    }

    /* ======================= PASO 2: MEJORAS ======================= */

    /** Sube de gama las piezas en el orden de prioridad del perfil, sin pasarse del presupuesto. */
    private function mejorar(array $armado, bool $conPreferencias): array
    {
        foreach ([1.0, self::HOLGURA_SEGUNDA_PASADA] as $holgura) {
            foreach ($this->criterios['prioridad'] as $cat) {
                $tope = $this->criterios['pesos'][$cat] * $this->presupuesto * $holgura;
                $armado = $this->mejorarPieza($armado, $cat, $tope, $conPreferencias);
            }
        }
        return $armado;
    }

    /**
     * Busca la mejor pieza de una categoría que: sea de mejor calidad que la actual,
     * no supere su tope (o no cueste más que la actual), deje el total dentro del
     * presupuesto y no genere errores de compatibilidad.
     */
    private function mejorarPieza(array $armado, string $cat, float $tope, bool $conPreferencias): array
    {
        $actual = $armado[$cat];
        if ($actual === null) {
            return $armado; // pieza que este armado no lleva (la gráfica en perfiles sin GPU)
        }
        $candidatos = $this->catalogo[$cat];
        // De mejor a peor calidad; a igual calidad, primero el más barato.
        usort($candidatos, fn($a, $b) => [$this->calidad($cat, $b), $a['precio']] <=> [$this->calidad($cat, $a), $b['precio']]);

        foreach ($candidatos as $candidato) {
            if ($this->calidad($cat, $candidato) <= $this->calidad($cat, $actual)) {
                break; // de aquí en adelante ninguno mejora a la pieza actual
            }
            // Un candidato mejor Y más barato que el actual siempre se acepta, aunque pase del tope.
            if ($candidato['precio'] > $tope && $candidato['precio'] > $actual['precio']) {
                continue;
            }
            if ($conPreferencias && !$this->cumplePreferencias($cat, $candidato)) {
                continue;
            }

            $prueba = $armado;
            $prueba[$cat] = $candidato;
            // Si cambió el consumo, puede hacer falta una fuente más grande.
            $prueba = $cat === 'fuente_poder' ? $prueba : $this->ajustarFuente($prueba);

            // sinErrores() también descarta, en armados sin gráfica, un procesador sin gráficos integrados (regla 6).
            if ($prueba !== null && $this->fuenteSuficiente($prueba)
                && $this->total($prueba) <= $this->presupuesto && $this->sinErrores($prueba)) {
                return $prueba;
            }
        }
        return $armado; // no hubo mejora posible
    }

    /**
     * Calidad de una pieza dentro de su categoría, como lista de valores que se
     * comparan en orden (el primero manda; el siguiente desempata).
     */
    private function calidad(string $cat, array $fila): array
    {
        switch ($cat) {
            case 'ram':
                return [(int)$fila['capacidad_gb'], (int)$fila['velocidad_mhz']];
            case 'almacenamiento':
                return [self::TIPOS_ALMACENAMIENTO[$fila['tipo']] ?? 0, (int)$fila['capacidad_gb']];
            case 'fuente_poder':
                return [(int)array_search($fila['certificacion'], self::CERTIFICACIONES, true), (int)$fila['potencia_w']];
            default:
                return [(float)$fila['precio']]; // sin datos de rendimiento: el precio indica la gama
        }
    }

    /* ======================= APOYO ======================= */

    /**
     * Deja el armado con una fuente adecuada: conserva la actual si alcanza con
     * margen; si no, pone la más barata que sí alcance. Devuelve null si no existe.
     */
    private function ajustarFuente(array $armado): ?array
    {
        if ($armado['fuente_poder'] !== null && $this->fuenteSuficiente($armado)) {
            return $armado;
        }
        $necesaria = $this->potenciaNecesaria($armado);
        $fuente = $this->masBarato('fuente_poder', fn($f) => $f['potencia_w'] >= $necesaria);
        if ($fuente === null) {
            return null;
        }
        $armado['fuente_poder'] = $fuente;
        return $armado;
    }

    /** Consumo estimado + 20 % de margen (la misma cuenta del motor de compatibilidad). */
    private function potenciaNecesaria(array $armado): float
    {
        $consumo = $this->motor->consumoEstimado($armado['cpu'], $armado['gpu'], $armado['cooler']);
        return $consumo * (1 + Compatibilidad::MARGEN_FUENTE);
    }

    private function fuenteSuficiente(array $armado): bool
    {
        return $armado['fuente_poder']['potencia_w'] >= $this->potenciaNecesaria($armado);
    }

    /** Preferencias del perfil: RAM mínima y almacenamiento de estado sólido. */
    private function cumplePreferencias(string $cat, array $fila): bool
    {
        if ($cat === 'ram') {
            return $fila['capacidad_gb'] >= $this->criterios['ram_min_gb'];
        }
        if ($cat === 'almacenamiento') {
            return $fila['tipo'] !== 'HDD';
        }
        return true;
    }

    /** La pieza más barata de una categoría que cumpla una condición (o null). */
    private function masBarato(string $cat, ?callable $condicion = null): ?array
    {
        $mejor = null;
        foreach ($this->catalogo[$cat] as $fila) {
            if ($condicion !== null && !$condicion($fila)) {
                continue;
            }
            if ($mejor === null || $fila['precio'] < $mejor['precio']) {
                $mejor = $fila;
            }
        }
        return $mejor;
    }

    /** ¿El motor de compatibilidad no reporta ningún error para estas piezas? */
    private function sinErrores(array $piezas): bool
    {
        return $this->motor->evaluar($piezas)['compatible'];
    }

    private function total(array $armado): float
    {
        return array_sum(array_map(fn($fila) => is_array($fila) && isset($fila['precio']) ? (float)$fila['precio'] : 0, $armado));
    }

    /**
     * Puntaje para ordenar los armados (0 a 1): por cada pieza, qué proporción de las
     * piezas de su categoría iguala o supera en calidad, ponderado por el peso del perfil.
     */
    private function puntaje(array $armado): float
    {
        $puntaje = 0;
        foreach ($this->criterios['pesos'] as $cat => $peso) {
            if ($armado[$cat] === null) {
                continue; // pieza que este armado no lleva
            }
            $calidad = $this->calidad($cat, $armado[$cat]);
            $superadas = count(array_filter($this->catalogo[$cat], fn($f) => $this->calidad($cat, $f) <= $calidad));
            $puntaje += $peso * $superadas / count($this->catalogo[$cat]);
        }
        return $puntaje;
    }

    /** Ids de las piezas de un armado (para saber si dos armados son iguales). */
    private function ids(array $armado): array
    {
        unset($armado['_economico']);
        return array_map(fn($fila) => $fila === null ? 0 : (int)$fila['id'], $armado);
    }
}
