<?php
/**
 * GESTIÓN DE ADMINISTRADORES
 *
 * listar / formulario / guardar -> registrar un administrador nuevo (figura 22 del documento).
 * contrasena                    -> restablecer la contraseña propia en dos pasos (figura 21):
 *                                  1) verificar la identidad con la contraseña actual,
 *                                  2) establecer la nueva.
 * correo                        -> cada administrador registra o cambia su correo electrónico
 *                                  (base para el futuro restablecimiento por correo).
 * restablecer                   -> un administrador genera una contraseña temporal para un
 *                                  compañero que olvidó la suya (la plataforma no envía correos).
 */
class AdministradorControlador
{
    /** Minutos que dura la verificación de identidad antes de pedirla otra vez. */
    private const MINUTOS_VERIFICACION = 10;

    private Administrador $modelo;

    public function __construct(private PDO $pdo, private array $categorias)
    {
        requerir_admin();
        $this->modelo = new Administrador($pdo);
    }

    public function listar(): void
    {
        vista('administradores/lista', [
            'titulo'          => 'Administradores',
            'administradores' => $this->modelo->todos(),
            // Contraseña temporal recién generada: se muestra una sola vez y se borra de la sesión.
            'temporal'        => $this->tomarDeSesion('password_temporal'),
        ]);
    }

    public function formulario(): void
    {
        vista('administradores/formulario', ['titulo' => 'Registrar administrador', 'valores' => [], 'errores' => []]);
    }

    /** Valida y registra un administrador nuevo. */
    public function guardar(): void
    {
        csrf_verificar();
        $usuario   = trim((string)($_POST['usuario'] ?? ''));
        $nombre    = trim((string)($_POST['nombre_completo'] ?? ''));
        $correo    = mb_strtolower(trim((string)($_POST['correo'] ?? '')));
        $password  = (string)($_POST['password'] ?? '');
        $confirmar = (string)($_POST['password_confirmar'] ?? '');
        $errores = [];

        if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $usuario)) {
            $errores['usuario'] = 'El usuario debe tener entre 3 y 50 caracteres: letras, números, punto, guion o guion bajo.';
        } elseif ($this->modelo->existeUsuario($usuario)) {
            $errores['usuario'] = 'Ya existe un administrador con ese usuario.';
        }
        if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
            $errores['nombre_completo'] = 'Escribe el nombre completo (entre 3 y 100 caracteres).';
        }
        if ($mensaje = $this->validarCorreo($correo, 0)) {
            $errores['correo'] = $mensaje;
        }
        $errores += $this->validarPasswordNueva($password, $confirmar);

        if ($errores) {
            vista('administradores/formulario', [
                'titulo'  => 'Registrar administrador',
                'valores' => ['usuario' => $usuario, 'nombre_completo' => $nombre, 'correo' => $correo], // la contraseña nunca se devuelve al formulario
                'errores' => $errores,
            ]);
            return;
        }

        $this->modelo->crear($usuario, $nombre, $password, $correo);
        flash('success', "Administrador «{$nombre}» registrado con éxito.");
        redirigir(url('administradores', 'listar'));
    }

    /** Restablecer la contraseña propia: paso 1 (verificar identidad) y paso 2 (nueva contraseña). */
    public function contrasena(): void
    {
        $id = (int)$_SESSION['admin_id'];
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verificar();
            $paso = (string)($_POST['paso'] ?? '');

            if ($paso === 'verificar') {
                // Paso 1: la identidad se comprueba con la contraseña actual.
                $admin = $this->modelo->buscar($id);
                if ($admin && password_verify((string)($_POST['password_actual'] ?? ''), $admin['password_hash'])) {
                    $_SESSION['identidad_verificada'] = time();
                } else {
                    $errores['password_actual'] = 'La información no coincide con los datos registrados. Vuelve a intentarlo.';
                }
            } elseif ($paso === 'cambiar' && $this->identidadVerificada()) {
                // Paso 2: nueva contraseña.
                $nueva = (string)($_POST['password'] ?? '');
                $errores = $this->validarPasswordNueva($nueva, (string)($_POST['password_confirmar'] ?? ''));
                if (!$errores) {
                    $this->modelo->cambiarPassword($id, $nueva);
                    unset($_SESSION['identidad_verificada']);
                    session_regenerate_id(true);
                    flash('success', 'Tu contraseña se cambió con éxito.');
                    redirigir(url('panel'));
                }
            }
        }

        vista('administradores/contrasena', [
            'titulo'     => 'Mi cuenta',
            'correo'     => (string)($this->modelo->buscar($id)['correo'] ?? ''),
            'verificada' => $this->identidadVerificada(),
            'errores'    => $errores,
        ]);
    }

    /** Registra o cambia el correo propio (solo por POST). */
    public function correo(): void
    {
        csrf_verificar();
        $id = (int)$_SESSION['admin_id'];
        $correo = mb_strtolower(trim((string)($_POST['correo'] ?? '')));

        if ($mensaje = $this->validarCorreo($correo, $id)) {
            flash('danger', $mensaje);
        } else {
            $this->modelo->actualizarCorreo($id, $correo);
            flash('success', 'Tu correo se guardó con éxito.');
        }
        redirigir(url('administradores', 'contrasena'));
    }

    /** Genera una contraseña temporal para otro administrador (solo por POST). */
    public function restablecer(): void
    {
        csrf_verificar();
        $admin = $this->modelo->buscar((int)($_POST['id'] ?? 0));

        if (!$admin) {
            flash('danger', 'Ese administrador no existe.');
        } elseif ((int)$admin['id'] === (int)$_SESSION['admin_id']) {
            flash('warning', 'Para cambiar tu propia contraseña usa la opción «Restablecer mi contraseña».');
        } else {
            // 12 caracteres aleatorios generados con un generador seguro.
            $temporal = substr(strtr(base64_encode(random_bytes(12)), '+/', 'xy'), 0, 12);
            $this->modelo->cambiarPassword((int)$admin['id'], $temporal);
            $_SESSION['password_temporal'] = [
                'nombre'   => $admin['nombre_completo'] ?: $admin['usuario'],
                'usuario'  => $admin['usuario'],
                'password' => $temporal,
            ];
        }
        redirigir(url('administradores', 'listar'));
    }

    /* ---------- Métodos de apoyo ---------- */

    /** Reglas de una contraseña nueva. Devuelve los errores encontrados (vacío si está bien). */
    private function validarPasswordNueva(string $password, string $confirmar): array
    {
        $errores = [];
        if (strlen($password) < Administrador::PASSWORD_MIN) {
            $errores['password'] = 'La contraseña debe tener al menos ' . Administrador::PASSWORD_MIN . ' caracteres.';
        } elseif (strlen($password) > 72) {
            $errores['password'] = 'La contraseña no puede tener más de 72 caracteres.';
        } elseif ($password !== $confirmar) {
            $errores['password_confirmar'] = 'Las dos contraseñas no coinciden.';
        }
        return $errores;
    }

    /** Valida un correo: formato, longitud y que no lo use otro administrador. Devuelve el error o null. */
    private function validarCorreo(string $correo, int $exceptoId): ?string
    {
        if ($correo === '') {
            return 'El correo electrónico es obligatorio.';
        }
        if (mb_strlen($correo) > 120 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return 'Escribe un correo electrónico válido (máximo 120 caracteres), por ejemplo nombre@dominio.com.';
        }
        if ($this->modelo->existeCorreo($correo, $exceptoId)) {
            return 'Ese correo ya lo usa otro administrador.';
        }
        return null;
    }

    /** ¿Se verificó la identidad hace menos de MINUTOS_VERIFICACION? */
    private function identidadVerificada(): bool
    {
        return isset($_SESSION['identidad_verificada'])
            && time() - $_SESSION['identidad_verificada'] < self::MINUTOS_VERIFICACION * 60;
    }

    /** Lee un dato de la sesión y lo borra (para mostrarlo una sola vez). */
    private function tomarDeSesion(string $clave)
    {
        $valor = $_SESSION[$clave] ?? null;
        unset($_SESSION[$clave]);
        return $valor;
    }
}
