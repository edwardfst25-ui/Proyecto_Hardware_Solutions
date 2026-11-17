<?php
/**
 * PRUEBAS DEL MOTOR DE COMPATIBILIDAD con componentes reales de la base de datos.
 *
 * Se ejecuta desde la terminal de Laragon, parado en la carpeta del proyecto:
 *     php pruebas/compatibilidad_prueba.php
 *
 * Cada prueba arma un caso con piezas reales (buscadas por sus características,
 * no por id, para que sigan sirviendo aunque cambie el catálogo), lo pasa por el
 * motor y compara el estado de una regla con el esperado.
 */
if (PHP_SAPI !== 'cli') {
    exit('Este archivo solo se ejecuta desde la terminal.');
}

require __DIR__ . '/../config/conexion.php';
require __DIR__ . '/../modelos/Compatibilidad.php';

/** Devuelve la primera fila de una consulta (o null). */
function una(PDO $pdo, string $sql, array $parametros = []): ?array
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);
    return $stmt->fetch() ?: null;
}

/** Trae un cooler con su lista de sockets, como la espera el motor. */
function cooler(PDO $pdo, string $sql, array $parametros = []): ?array
{
    $fila = una($pdo, $sql, $parametros);
    if ($fila) {
        $stmt = $pdo->prepare('SELECT socket FROM cooler_socket WHERE cooler_id = ?');
        $stmt->execute([$fila['id']]);
        $fila['sockets'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    return $fila;
}

$motor = new Compatibilidad();
$aprobadas = 0;
$falladas = 0;

/** Compara el estado de una regla (por su posición 0-4) con el esperado. */
function probar(string $nombre, array $armado, int $regla, string $esperado): void
{
    global $motor, $aprobadas, $falladas;
    if (in_array(null, $armado, true)) {
        echo "[OMITIDA] $nombre (el catálogo no tiene piezas para este caso)\n";
        return;
    }
    $resultado = $motor->evaluar($armado);
    $v = $resultado['verificaciones'][$regla];
    $paso = $v['estado'] === $esperado;
    $paso ? $aprobadas++ : $falladas++;
    echo ($paso ? '[OK]    ' : '[FALLA] ') . "$nombre\n";
    echo "          esperado: $esperado | obtenido: {$v['estado']}\n";
    echo "          {$v['mensaje']}\n";
}

// Posición de cada regla dentro de 'verificaciones'
const SOCKET = 0, RAM = 1, FACTOR = 2, COOLER = 3, POTENCIA = 4, VIDEO = 5, LARGO_GPU = 6, ALTO_COOLER = 7;

echo "=== Regla 1: socket CPU - placa madre ===\n";
$cpuAm5   = una($pdo, "SELECT * FROM cpu WHERE socket = 'AM5' ORDER BY id LIMIT 1");
$placaAm5 = una($pdo, "SELECT * FROM placa_madre WHERE socket = 'AM5' ORDER BY id LIMIT 1");
$placaOtra = una($pdo, "SELECT * FROM placa_madre WHERE socket <> 'AM5' ORDER BY id LIMIT 1");
probar('CPU AM5 + placa AM5', ['cpu' => $cpuAm5, 'placa_madre' => $placaAm5], SOCKET, 'ok');
probar('CPU AM5 + placa de otro socket', ['cpu' => $cpuAm5, 'placa_madre' => $placaOtra], SOCKET, 'error');
probar('Solo CPU, sin placa', ['cpu' => $cpuAm5], SOCKET, 'pendiente');

echo "\n=== Regla 2: tipo de RAM - placa madre ===\n";
$placaDdr4 = una($pdo, "SELECT * FROM placa_madre WHERE tipo_ram = 'DDR4' ORDER BY id LIMIT 1");
$ramDdr4   = una($pdo, "SELECT * FROM ram WHERE tipo = 'DDR4' ORDER BY id LIMIT 1");
$ramDdr5   = una($pdo, "SELECT * FROM ram WHERE tipo = 'DDR5' ORDER BY id LIMIT 1");
probar('RAM DDR4 + placa DDR4', ['ram' => $ramDdr4, 'placa_madre' => $placaDdr4], RAM, 'ok');
probar('RAM DDR5 + placa DDR4', ['ram' => $ramDdr5, 'placa_madre' => $placaDdr4], RAM, 'error');

echo "\n=== Regla 3: factor de forma placa - gabinete ===\n";
$placaAtx   = una($pdo, "SELECT * FROM placa_madre WHERE factor_forma = 'ATX' ORDER BY id LIMIT 1");
$placaMatx  = una($pdo, "SELECT * FROM placa_madre WHERE factor_forma = 'Micro-ATX' ORDER BY id LIMIT 1");
$gabAtx     = una($pdo, "SELECT * FROM gabinete WHERE factor_forma_soportado = 'ATX' ORDER BY id LIMIT 1");
$gabMatx    = una($pdo, "SELECT * FROM gabinete WHERE factor_forma_soportado = 'Micro-ATX' ORDER BY id LIMIT 1");
probar('Placa ATX en gabinete ATX', ['placa_madre' => $placaAtx, 'gabinete' => $gabAtx], FACTOR, 'ok');
probar('Placa Micro-ATX en gabinete ATX (más grande)', ['placa_madre' => $placaMatx, 'gabinete' => $gabAtx], FACTOR, 'ok');
probar('Placa ATX en gabinete Micro-ATX (no cabe)', ['placa_madre' => $placaAtx, 'gabinete' => $gabMatx], FACTOR, 'error');

echo "\n=== Regla 4: cooler - socket del CPU ===\n";
$coolerAm5 = cooler($pdo, "SELECT c.* FROM cooler c JOIN cooler_socket s ON s.cooler_id = c.id WHERE s.socket = 'AM5' ORDER BY c.id LIMIT 1");
// Pareja real (cooler, CPU) donde el cooler tiene sockets registrados pero no el de ese CPU.
$pareja = una($pdo, "SELECT c.id AS cooler_id, p.id AS cpu_id FROM cooler c, cpu p
                      WHERE EXISTS (SELECT 1 FROM cooler_socket s WHERE s.cooler_id = c.id)
                        AND NOT EXISTS (SELECT 1 FROM cooler_socket s WHERE s.cooler_id = c.id AND s.socket = p.socket)
                      ORDER BY c.id, p.id LIMIT 1");
$coolerNoSirve = $pareja ? cooler($pdo, 'SELECT * FROM cooler WHERE id = ?', [$pareja['cooler_id']]) : null;
$cpuNoSirve    = $pareja ? una($pdo, 'SELECT * FROM cpu WHERE id = ?', [$pareja['cpu_id']]) : null;
$coolerSinSockets = cooler($pdo, "SELECT c.* FROM cooler c WHERE NOT EXISTS (SELECT 1 FROM cooler_socket s WHERE s.cooler_id = c.id) ORDER BY c.id LIMIT 1");
probar('Cooler con soporte AM5 + CPU AM5', ['cooler' => $coolerAm5, 'cpu' => $cpuAm5], COOLER, 'ok');
probar('Cooler que no soporta el socket del CPU', ['cooler' => $coolerNoSirve, 'cpu' => $cpuNoSirve], COOLER, 'error');
probar('Cooler sin sockets registrados', ['cooler' => $coolerSinSockets, 'cpu' => $cpuAm5], COOLER, 'advertencia');

echo "\n=== Regla 5: potencia de la fuente ===\n";
$cpuMax    = una($pdo, 'SELECT * FROM cpu ORDER BY consumo_w DESC LIMIT 1');
$cpuMin    = una($pdo, 'SELECT * FROM cpu ORDER BY consumo_w ASC LIMIT 1');
$gpuMax    = una($pdo, 'SELECT * FROM gpu ORDER BY consumo_w DESC LIMIT 1');
$fuenteMin = una($pdo, 'SELECT * FROM fuente_poder ORDER BY potencia_w ASC LIMIT 1');
$fuenteMax = una($pdo, 'SELECT * FROM fuente_poder ORDER BY potencia_w DESC LIMIT 1');
probar('CPU de menor consumo, sin GPU, fuente más pequeña',
    ['cpu' => $cpuMin, 'fuente_poder' => $fuenteMin], POTENCIA, 'ok');
probar('CPU y GPU de mayor consumo con la fuente más pequeña',
    ['cpu' => $cpuMax, 'gpu' => $gpuMax, 'fuente_poder' => $fuenteMin], POTENCIA, 'error');
probar('CPU y GPU de mayor consumo con la fuente más grande',
    ['cpu' => $cpuMax, 'gpu' => $gpuMax, 'fuente_poder' => $fuenteMax], POTENCIA, 'ok');

// Caso "justo": se busca en el catálogo una combinación real donde la fuente alcanza
// pero con menos del 20 % de margen (consumo <= potencia < consumo * 1.2).
$justo = una($pdo,
    'SELECT c.id AS cpu_id, g.id AS gpu_id, f.id AS fuente_id
       FROM cpu c, gpu g, fuente_poder f
      WHERE f.potencia_w >= c.consumo_w + g.consumo_w + :base
        AND f.potencia_w <  (c.consumo_w + g.consumo_w + :base2) * 1.2
      LIMIT 1',
    ['base' => Compatibilidad::CONSUMO_BASE_W, 'base2' => Compatibilidad::CONSUMO_BASE_W]);
probar('Fuente que alcanza pero con menos del 20 % de margen', [
    'cpu'          => $justo ? una($pdo, 'SELECT * FROM cpu WHERE id = ?', [$justo['cpu_id']]) : null,
    'gpu'          => $justo ? una($pdo, 'SELECT * FROM gpu WHERE id = ?', [$justo['gpu_id']]) : null,
    'fuente_poder' => $justo ? una($pdo, 'SELECT * FROM fuente_poder WHERE id = ?', [$justo['fuente_id']]) : null,
], POTENCIA, 'advertencia');

echo "\n=== Regla 6: salida de video ===\n";
$cpuConGraficos = una($pdo, 'SELECT * FROM cpu WHERE graficos_integrados = 1 ORDER BY id LIMIT 1');
$cpuSinGraficos = una($pdo, 'SELECT * FROM cpu WHERE graficos_integrados = 0 ORDER BY id LIMIT 1');
$gpuCualquiera  = una($pdo, 'SELECT * FROM gpu ORDER BY id LIMIT 1');
// Las otras 6 piezas obligatorias, para probar el caso de armado completo.
$resto = [
    'placa_madre'    => $placaAm5,
    'ram'            => $ramDdr5,
    'fuente_poder'   => $fuenteMax,
    'almacenamiento' => una($pdo, 'SELECT * FROM almacenamiento ORDER BY id LIMIT 1'),
    'gabinete'       => $gabAtx,
    'cooler'         => $coolerAm5,
];
probar('CPU sin gráficos integrados + tarjeta gráfica', ['cpu' => $cpuSinGraficos, 'gpu' => $gpuCualquiera], VIDEO, 'ok');
probar('CPU con gráficos integrados, sin tarjeta gráfica', ['cpu' => $cpuConGraficos], VIDEO, 'ok');
probar('CPU sin gráficos integrados, sin tarjeta, armado a medias', ['cpu' => $cpuSinGraficos], VIDEO, 'advertencia');
probar('CPU sin gráficos integrados, sin tarjeta, armado completo', ['cpu' => $cpuSinGraficos] + $resto, VIDEO, 'error');

echo "\n=== Reglas 7 y 8: dimensiones físicas ===\n";
// Se toman piezas reales y se les asignan medidas de prueba, porque las medidas
// son opcionales y pueden no estar registradas todavía en el catálogo.
$gpuMedida      = ['largo_mm' => 320] + $gpuCualquiera;
$gabAmplio      = ['gpu_largo_max_mm' => 400, 'cooler_alto_max_mm' => 170] + $gabAtx;
$gabEstrecho    = ['gpu_largo_max_mm' => 300, 'cooler_alto_max_mm' => 150] + $gabAtx;
$gabSinMedidas  = ['gpu_largo_max_mm' => null, 'cooler_alto_max_mm' => null] + $gabAtx;
$coolerAire     = ['tipo' => 'Aire', 'alto_mm' => 160] + $coolerAm5;
$coolerLiquido  = ['tipo' => 'Líquido (AIO)', 'alto_mm' => null] + $coolerAm5;
probar('Gráfica de 320 mm en gabinete que admite 400 mm', ['gpu' => $gpuMedida, 'gabinete' => $gabAmplio], LARGO_GPU, 'ok');
probar('Gráfica de 320 mm en gabinete que admite 300 mm', ['gpu' => $gpuMedida, 'gabinete' => $gabEstrecho], LARGO_GPU, 'error');
probar('Gráfica con medida y gabinete sin medida registrada', ['gpu' => $gpuMedida, 'gabinete' => $gabSinMedidas], LARGO_GPU, 'sin_dato');
probar('Disipador de 160 mm en gabinete que admite 170 mm', ['cooler' => $coolerAire, 'gabinete' => $gabAmplio], ALTO_COOLER, 'ok');
probar('Disipador de 160 mm en gabinete que admite 150 mm', ['cooler' => $coolerAire, 'gabinete' => $gabEstrecho], ALTO_COOLER, 'error');
probar('Refrigeración líquida (no se mide por altura)', ['cooler' => $coolerLiquido, 'gabinete' => $gabAmplio], ALTO_COOLER, 'sin_dato');

echo "\n=== Armado completo y compatible ===\n";
$completo = [
    'cpu'            => $cpuAm5,
    'placa_madre'    => $placaAm5,
    'ram'            => una($pdo, 'SELECT * FROM ram WHERE tipo = ? ORDER BY id LIMIT 1', [$placaAm5['tipo_ram'] ?? '']),
    'gpu'            => una($pdo, 'SELECT * FROM gpu ORDER BY consumo_w ASC LIMIT 1'),
    'fuente_poder'   => $fuenteMax,
    'almacenamiento' => una($pdo, 'SELECT * FROM almacenamiento ORDER BY id LIMIT 1'),
    'gabinete'       => $gabAtx,
    'cooler'         => $coolerAm5,
];
$r = $motor->evaluar($completo);
$paso = $r['compatible'] && $r['completo'] && !$r['errores'];
$paso ? $aprobadas++ : $falladas++;
echo ($paso ? '[OK]    ' : '[FALLA] ') . "Armado de 8 piezas compatibles: compatible y completo\n";
foreach ($completo as $pieza => $fila) {
    echo "          $pieza: {$fila['nombre']}\n";
}
echo "          consumo estimado: {$r['consumo_estimado_w']} W | fuente recomendada: {$r['potencia_recomendada_w']} W\n";

echo "\nRESULTADO: $aprobadas aprobadas, $falladas falladas.\n";
exit($falladas > 0 ? 1 : 0);
