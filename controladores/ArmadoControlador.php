<?php
/**
 * GESTIÓN DE ARMADOS RECOMENDADOS (panel de administración)
 *
 * El administrador crea armados a mano para un perfil y un rango de presupuesto.
 * Antes de guardar, el armado pasa por el motor de compatibilidad: si tiene
 * errores, no se guarda. Así nunca se recomienda un equipo que no se puede armar.
 */
class ArmadoControlador
{
    private ArmadoRecomendado $modelo;
    private Catalogo $catalogo;

    public function __construct(private PDO $pdo, private array $categorias)
    {
        requerir_admin();
        $this->modelo = new ArmadoRecomendado($pdo);
        $this->catalogo = new Catalogo($pdo, $categorias);
    }

    public function listar(): void
    {
        $completo = $this->catalogo->completo();
        $motor = new Compatibilidad();

        $armados = [];
        foreach ($this->modelo->todos() as $armado) {
            $piezas = ArmadoRecomendado::piezas($armado, $completo);
            $armado['piezas'] = $piezas;
            $armado['total'] = array_sum(array_map(fn($f) => $f ? (float)$f['precio'] : 0, $piezas));
            // Un armado guardado puede dejar de ser compatible si después se edita un componente.
            $armado['evaluacion'] = $motor->evaluar($piezas);
            $armados[] = $armado;
        }

        vista('armados/lista', ['titulo' => 'Armados recomendados', 'armados' => $armados]);
    }

    public function formulario(): void
    {
        $registro = null;
        if (isset($_GET['id'])) {
            $registro = $this->buscarOVolver((int)$_GET['id']);
        }
        $this->mostrarFormulario($registro, $registro ?? [], []);
    }

    public function guardar(): void
    {
        csrf_verificar();
        $id = (int)($_POST['id'] ?? 0);
        $registro = $id > 0 ? $this->buscarOVolver($id) : null;

        $completo = $this->catalogo->completo();
        $errores = [];
        $datos = [];

        // Perfil y rango.
        $datos['perfil_id'] = (int)($_POST['perfil_id'] ?? 0);
        if (!(new Perfil($this->pdo))->buscar($datos['perfil_id'])) {
            $errores['perfil_id'] = 'Selecciona un perfil de uso.';
        }
        $datos['rango_presupuesto'] = (string)($_POST['rango_presupuesto'] ?? '');
        if (!in_array($datos['rango_presupuesto'], ArmadoRecomendado::RANGOS, true)) {
            $errores['rango_presupuesto'] = 'Selecciona un rango de presupuesto.';
        }

        // Las 8 piezas: cada id debe existir en su categoría. Solo la gráfica es opcional.
        foreach (ArmadoRecomendado::COLUMNAS as $cat => $columna) {
            $idPieza = (int)($_POST[$columna] ?? 0);
            if ($idPieza === 0 && $cat === 'gpu') {
                $datos[$columna] = null;
            } elseif (!isset($completo[$cat][$idPieza])) {
                $datos[$columna] = null;
                $errores[$columna] = 'Selecciona ' . Compatibilidad::PIEZAS[$cat] . '.';
            } else {
                $datos[$columna] = $idPieza;
            }
        }

        // Motor de compatibilidad: con errores no se guarda.
        $evaluacion = null;
        if (!$errores) {
            $evaluacion = (new Compatibilidad())->evaluar(ArmadoRecomendado::piezas($datos, $completo));
            if (!$evaluacion['compatible']) {
                $errores['compatibilidad'] = $evaluacion['errores'];
            }
        }

        if ($errores) {
            $this->mostrarFormulario($registro, $_POST, $errores);
            return;
        }

        if ($registro) {
            $this->modelo->actualizar($id, $datos);
        } else {
            $this->modelo->crear($datos, (int)$_SESSION['admin_id']); // queda registrado quién lo creó
        }

        if ($evaluacion['advertencias']) {
            flash('warning', 'Armado guardado, con advertencias: ' . implode(' ', $evaluacion['advertencias']));
        } else {
            flash('success', 'Armado guardado con éxito. Es compatible.');
        }
        redirigir(url('armados', 'listar'));
    }

    public function eliminar(): void
    {
        csrf_verificar();
        $registro = $this->buscarOVolver((int)($_POST['id'] ?? 0));
        $this->modelo->eliminar((int)$registro['id']);
        flash('success', 'Armado eliminado.');
        redirigir(url('armados', 'listar'));
    }

    private function buscarOVolver(int $id): array
    {
        $registro = $id > 0 ? $this->modelo->buscar($id) : null;
        if (!$registro) {
            flash('danger', 'Ese armado no existe o ya fue eliminado.');
            redirigir(url('armados', 'listar'));
        }
        return $registro;
    }

    private function mostrarFormulario(?array $registro, array $valores, array $errores): void
    {
        // Opciones de cada lista: "Nombre · datos clave · precio".
        $listas = [];
        foreach ($this->catalogo->completo() as $cat => $filas) {
            foreach ($filas as $fila) {
                $listas[$cat][(int)$fila['id']] = $fila['nombre'] . ' · ' . $this->catalogo->resumen($cat, $fila) . ' · ' . precio($fila['precio']);
            }
        }

        vista('armados/formulario', [
            'titulo'   => ($registro ? 'Editar' : 'Agregar') . ' armado recomendado',
            'registro' => $registro,
            'valores'  => $valores,
            'errores'  => $errores,
            'perfiles' => (new Perfil($this->pdo))->todos(),
            'listas'   => $listas,
        ]);
    }
}
