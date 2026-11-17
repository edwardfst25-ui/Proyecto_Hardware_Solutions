<?php
/** Inicio y cierre de sesión del administrador. */
class AuthControlador
{
    public function __construct(private PDO $pdo, private array $categorias)
    {
    }

    /** GET: muestra el formulario. POST: comprueba usuario y contraseña. */
    public function login(): void
    {
        // Si ya inició sesión, va directo al panel.
        if (isset($_SESSION['admin_id'])) {
            redirigir(url('panel'));
        }

        $error = '';
        $usuario = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verificar();
            $usuario  = trim((string)($_POST['usuario'] ?? ''));
            $password = (string)($_POST['password'] ?? '');

            if ($usuario === '' || $password === '') {
                $error = 'Por favor completa usuario y contraseña.';
            } else {
                $admin = (new Administrador($this->pdo))->verificar($usuario, $password);
                if ($admin) {
                    // Se cambia el identificador de la sesión al entrar (evita el robo de sesión).
                    session_regenerate_id(true);
                    $_SESSION['admin_id']      = $admin['id'];
                    $_SESSION['admin_usuario'] = $admin['usuario'];
                    $_SESSION['admin_nombre']  = $admin['nombre_completo'];
                    redirigir(url('panel'));
                }
                // Mismo mensaje para usuario o contraseña incorrectos: no se revela cuál falló.
                $error = 'Usuario o contraseña incorrectos.';
            }
        }

        vista('auth/login', ['titulo' => 'Iniciar sesión', 'error' => $error, 'usuario' => $usuario], 'publico');
    }

    /** Cierra la sesión (solo por POST, para que un enlace externo no pueda cerrarla). */
    public function logout(): void
    {
        csrf_verificar();
        $_SESSION = [];
        session_destroy();
        redirigir(url('auth', 'login'));
    }
}
