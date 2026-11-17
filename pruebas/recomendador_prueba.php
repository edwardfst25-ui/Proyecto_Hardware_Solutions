<?php
/**
 * PRUEBAS DEL RECOMENDADOR con el catálogo real.
 *
 *     php pruebas/recomendador_prueba.php
 *
 * Para cada perfil y varios presupuestos comprueba que TODOS los armados propuestos:
 *   1. tengan las 8 piezas,
 *   2. no superen el presupuesto,
 *   3. sean compatibles según el motor (sin errores),
 *   4. tengan una fuente con el 20 % de margen (sin advertencia de potencia),
 *   5. en perfiles sin tarjeta gráfica (Ofimática): no lleven gráfica y el procesador
 *      tenga gráficos integrados; en los demás perfiles: sí lleven gráfica.
 * Y que con un presupuesto imposible no se proponga nada.
 */
if (PHP_SAPI !== 'cli') {
    exit('Este archivo solo se ejecuta desde la terminal.');
}

define('RAIZ', dirname(__DIR__));
require RAIZ . '/config/conexion.php';
foreach (['Compatibilidad', 'Componente', 'Catalogo', 'Recomendador', 'Perfil'] as $clase) {
    require RAIZ . "/modelos/$clase.php";
}

$catalogo = (new Catalogo($pdo, require RAIZ . '/config/categorias.php'))->completo();
$motor = new Compatibilidad();
$recomendador = new Recomendador($motor);
$aprobadas = 0;
$falladas = 0;

function comprobar(bool $condicion, string $nombre): void
{
    global $aprobadas, $falladas;
    $condicion ? $aprobadas++ : $falladas++;
    echo ($condicion ? '[OK]    ' : '[FALLA] ') . $nombre . "\n";
}

$minimo = $recomendador->recomendar($catalogo, Perfil::criterios(1), 1)['costo_minimo'];
echo 'Armado más barato posible con el catálogo actual: $' . number_format($minimo, 0, ',', '.') . "\n\n";

foreach ((new Perfil($pdo))->todos() as $perfil) {
    // Cada perfil tiene su propio mínimo (los perfiles sin tarjeta gráfica cuestan menos).
    $minimoPerfil = $recomendador->recomendar($catalogo, Perfil::criterios((int)$perfil['id']), 1)['costo_minimo'];
    foreach ([$minimoPerfil, 4000000, 6000000, 9000000, 14000000, 30000000] as $presupuesto) {
        $resultado = $recomendador->recomendar($catalogo, Perfil::criterios((int)$perfil['id']), $presupuesto);
        $etiqueta = "{$perfil['nombre']} con \$" . number_format($presupuesto, 0, ',', '.');

        comprobar(count($resultado['armados']) >= 1, "$etiqueta: propone al menos un armado (" . count($resultado['armados']) . ')');
        foreach ($resultado['armados'] as $i => $armado) {
            $n = $i + 1;
            $ev = $motor->evaluar($armado['piezas']);
            $potenciaOk = $ev['verificaciones'][4]['estado'] === 'ok';
            $sinGpu = !empty(Perfil::criterios((int)$perfil['id'])['sin_gpu']);
            $videoOk = $sinGpu
                ? ($armado['piezas']['gpu'] === null && (int)$armado['piezas']['cpu']['graficos_integrados'] === 1)
                : $armado['piezas']['gpu'] !== null;
            $bien = $ev['completo'] && $ev['compatible'] && $potenciaOk && $videoOk && $armado['total'] <= $presupuesto;
            comprobar($bien, "   armado $n ({$armado['plataforma']}" . ($sinGpu ? ', sin gráfica' : '') . "): completo, compatible, fuente con margen y total \$"
                . number_format($armado['total'], 0, ',', '.') . ' dentro del presupuesto');
        }
    }
}

$imposible = $recomendador->recomendar($catalogo, Perfil::criterios(1), $minimo - 1);
comprobar(count($imposible['armados']) === 0, 'Con $1 menos que el mínimo no se propone ningún armado');

echo "\nRESULTADO: $aprobadas aprobadas, $falladas falladas.\n";
exit($falladas > 0 ? 1 : 0);
