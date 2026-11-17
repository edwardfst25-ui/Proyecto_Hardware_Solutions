<?php
/**
 * ARMADOR PÚBLICO ("Arma tu PC")
 *
 * index:   muestra la página con las 8 piezas del armado y el catálogo para elegirlas.
 * evaluar: recibe los ids elegidos y responde en JSON con el resultado del motor
 *          de compatibilidad y el costo total. La página lo llama con fetch()
 *          cada vez que el usuario cambia una pieza.
 */
class ArmadorControlador
{
    public function __construct(private PDO $pdo, private array $categorias)
    {
    }

    public function index(): void
    {
        $modelo = new Catalogo($this->pdo, $this->categorias);

        // Catálogo que usa la página para mostrar las tarjetas de cada categoría.
        // Se envía una sola vez, como JSON; el navegador lo filtra sin volver a pedirlo.
        $catalogo = [];
        foreach ($modelo->completo() as $cat => $filas) {
            foreach ($filas as $fila) {
                $catalogo[$cat][] = [
                    'id'      => (int)$fila['id'],
                    'nombre'  => $fila['nombre'],
                    'marca'   => $fila['marca'],
                    'detalle' => $modelo->resumen($cat, $fila),
                    'precio'  => (float)$fila['precio'],
                    'precioTexto' => precio($fila['precio']),
                    'imagen'  => $fila['imagen'] ? 'galeria/componentes/' . $fila['imagen'] : null,
                    'enlace'  => url('catalogo', 'detalle', ['cat' => $cat, 'id' => $fila['id']]),
                ];
            }
        }

        vista('armador/index', [
            'titulo'   => 'Arma tu PC',
            'piezas'   => Compatibilidad::PIEZAS,
            'catalogo' => $catalogo,
        ], 'publico');
    }

    public function evaluar(): void
    {
        $modelo = new Catalogo($this->pdo, $this->categorias);
        $catalogo = $modelo->completo();

        // 1) Armado con las piezas elegidas (los ids llegan en la URL: ?cpu=3&ram=7...).
        //    Un id que no exista en el catálogo simplemente se ignora.
        $armado = [];
        foreach (array_keys(Compatibilidad::PIEZAS) as $cat) {
            $id = (int)($_GET[$cat] ?? 0);
            $armado[$cat] = $catalogo[$cat][$id] ?? null;
        }

        // 2) Motor de compatibilidad.
        $motor = new Compatibilidad();
        $resultado = $motor->evaluar($armado);

        // 3) Costo total y datos de cada pieza para el resumen.
        $total = 0;
        $piezas = [];
        foreach ($armado as $cat => $fila) {
            if (!$fila) {
                $piezas[$cat] = null;
                continue;
            }
            $total += (float)$fila['precio'];
            $piezas[$cat] = [
                'id'      => (int)$fila['id'],
                'nombre'  => $fila['nombre'],
                'detalle' => $modelo->resumen($cat, $fila),
                'enlace'  => url('catalogo', 'detalle', ['cat' => $cat, 'id' => $fila['id']]),
                'precio'  => precio($fila['precio']),
                'imagen'  => $fila['imagen'] ? 'galeria/componentes/' . $fila['imagen'] : null,
            ];
        }

        // 4) Opciones incompatibles de cada lista: se prueba cada candidato dentro del
        //    armado actual y se marca si el motor reporta un error que lo involucra.
        //    Así la página puede desactivar lo que no sirve sin repetir las reglas en JavaScript.
        $incompatibles = [];
        foreach ($catalogo as $cat => $filas) {
            $incompatibles[$cat] = [];
            foreach ($filas as $id => $candidato) {
                $prueba = $armado;
                $prueba[$cat] = $candidato;
                foreach ($motor->evaluar($prueba)['verificaciones'] as $v) {
                    if ($v['estado'] === 'error' && in_array($cat, $v['componentes'], true)) {
                        $incompatibles[$cat][] = $id;
                        break;
                    }
                }
            }
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'elegidos'      => count(array_filter($armado)),
            'total'         => precio($total),
            'total_numero'  => $total,
            'piezas'        => $piezas,
            'resultado'     => $resultado,
            'incompatibles' => $incompatibles,
        ], JSON_UNESCAPED_UNICODE);
    }
}
