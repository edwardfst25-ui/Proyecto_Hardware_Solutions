<?php
/**
 * CRUD GENÉRICO DE COMPONENTES
 *
 * Un solo controlador para las 8 categorías. La categoría llega en la URL
 * (?cat=cpu) y de ella depende qué tabla y qué campos se usan.
 * Acciones: listar, formulario (crear/editar), guardar y eliminar.
 */
class ComponenteControlador
{
    private string $cat;        // tabla de la categoría actual (ej. "cpu")
    private array $def;         // su definición en config/categorias.php
    private Componente $modelo; // modelo genérico apuntando a esa tabla

    public function __construct(private PDO $pdo, private array $categorias)
    {
        requerir_admin(); // todo este controlador es solo para administradores

        // La categoría debe existir en la configuración (lista blanca).
        $cat = $_GET['cat'] ?? '';
        if (!is_string($cat) || !isset($categorias[$cat])) {
            http_response_code(404);
            vista('plantillas/error', ['titulo' => 'Categoría no encontrada', 'mensaje' => 'Esa categoría de componentes no existe.']);
            exit;
        }
        $this->cat = $cat;
        $this->def = $categorias[$cat];
        $this->modelo = new Componente($pdo, $cat, $this->def);
    }

    /** Listado de la categoría. */
    public function listar(): void
    {
        vista('componentes/lista', [
            'titulo'  => $this->def['plural'],
            'cat'     => $this->cat,
            'def'     => $this->def,
            'filas'   => $this->modelo->todos(),
            'sockets' => isset($this->def['sockets']) ? $this->modelo->socketsDeTodos() : [],
        ]);
    }

    /** Formulario vacío (crear) o con datos (editar, cuando llega ?id=). */
    public function formulario(): void
    {
        $registro = null;
        $socketsMarcados = [];

        if (isset($_GET['id'])) {
            $registro = $this->buscarOVolver((int)$_GET['id']);
            if (isset($this->def['sockets'])) {
                $socketsMarcados = $this->modelo->sockets((int)$registro['id']);
            }
        }

        $this->mostrarFormulario($registro, $registro ?? [], [], $socketsMarcados);
    }

    /** Recibe el formulario (POST), valida y guarda. */
    public function guardar(): void
    {
        // Si el envío supera post_max_size, PHP descarta TODO el formulario ($_POST llega vacío).
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            flash('danger', 'El archivo enviado es demasiado grande. La imagen puede pesar máximo 2 MB.');
            redirigir(url('componentes', 'listar', ['cat' => $this->cat]));
        }
        csrf_verificar();

        $id = (int)($_POST['id'] ?? 0);
        $registro = $id > 0 ? $this->buscarOVolver($id) : null;

        // 1) Validación en el servidor.
        [$datos, $errores] = Validador::validar($this->def['campos'], $_POST);

        // El nombre no puede repetirse dentro de la categoría (evita registros duplicados).
        if (!isset($errores['nombre']) && $this->modelo->existeNombre((string)$datos['nombre'], $id)) {
            $errores['nombre'] = 'Ya existe ' . $this->def['singular'] . ' con ese nombre.';
        }

        $sockets = [];
        if (isset($this->def['sockets'])) {
            $sockets = Validador::opcionesMultiples($_POST['sockets'] ?? [], $this->def['sockets']);
            if (!$sockets) {
                $errores['sockets'] = 'Marca al menos un socket compatible.';
            }
        }

        // Con errores no se guarda nada: se vuelve a mostrar el formulario con lo que el usuario escribió.
        if ($errores) {
            $this->mostrarFormulario($registro, $_POST, $errores, $sockets);
            return;
        }

        // 2) Guardar en la base de datos.
        try {
            if ($registro) {
                $this->modelo->actualizar($id, $datos);
            } else {
                $id = $this->modelo->crear($datos);
            }
            if (isset($this->def['sockets'])) {
                $this->modelo->guardarSockets($id, $sockets);
            }
        } catch (PDOException $e) {
            $errores['general'] = 'No se pudo guardar en la base de datos. Revisa los datos e inténtalo de nuevo.';
            $this->mostrarFormulario($registro, $_POST, $errores, $sockets);
            return;
        }

        // 3) Imagen (opcional). Va después de guardar porque el nombre del archivo lleva el id.
        $imagenActual = $registro['imagen'] ?? null;
        try {
            if (SubidaImagen::hayArchivo($_FILES['imagen'] ?? null)) {
                $nueva = SubidaImagen::guardar($_FILES['imagen'], $this->cat, $id, $datos['nombre']);
                if ($imagenActual !== null && $imagenActual !== $nueva) {
                    SubidaImagen::borrar($imagenActual); // se borra la foto anterior
                }
                $this->modelo->actualizarImagen($id, $nueva);
            } elseif (!empty($_POST['quitar_imagen']) && $imagenActual !== null) {
                SubidaImagen::borrar($imagenActual);
                $this->modelo->actualizarImagen($id, null);
            }
        } catch (RuntimeException $e) {
            // Los datos sí se guardaron; solo falló la imagen. Se avisa y se vuelve al formulario.
            flash('warning', 'Los datos se guardaron, pero la imagen no: ' . $e->getMessage());
            redirigir(url('componentes', 'formulario', ['cat' => $this->cat, 'id' => $id]));
        }

        flash('success', $registro ? 'Cambios guardados con éxito.' : 'Componente creado con éxito.');
        redirigir(url('componentes', 'listar', ['cat' => $this->cat]));
    }

    /** Elimina un registro. Solo por POST y con el código CSRF. */
    public function eliminar(): void
    {
        csrf_verificar();
        $registro = $this->buscarOVolver((int)($_POST['id'] ?? 0));

        try {
            $this->modelo->eliminar((int)$registro['id']);
            SubidaImagen::borrar($registro['imagen'] ?? null);
            flash('success', "Se eliminó «{$registro['nombre']}».");
        } catch (PDOException $e) {
            // La llave foránea de armados_recomendados impide borrar un componente en uso.
            flash('danger', "No se pudo eliminar «{$registro['nombre']}»: está usado en un armado recomendado. Elimina primero ese armado.");
        }
        redirigir(url('componentes', 'listar', ['cat' => $this->cat]));
    }

    /* ---------- Métodos de apoyo ---------- */

    /** Busca un registro; si no existe, vuelve al listado con un aviso. */
    private function buscarOVolver(int $id): array
    {
        $registro = $id > 0 ? $this->modelo->buscar($id) : null;
        if (!$registro) {
            flash('danger', 'Ese componente no existe o ya fue eliminado.');
            redirigir(url('componentes', 'listar', ['cat' => $this->cat]));
        }
        return $registro;
    }

    private function mostrarFormulario(?array $registro, array $valores, array $errores, array $socketsMarcados): void
    {
        vista('componentes/formulario', [
            'titulo'          => ($registro ? 'Editar ' : 'Agregar ') . $this->def['singular'],
            'cat'             => $this->cat,
            'def'             => $this->def,
            'registro'        => $registro,        // null si se está creando
            'valores'         => $valores,         // lo que se muestra en los campos
            'errores'         => $errores,
            'socketsMarcados' => $socketsMarcados,
        ]);
    }
}
