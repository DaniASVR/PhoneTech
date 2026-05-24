<?php
// Vista para el inicio de sesion de los usuarios

// si el usuario ya tiene la sesion activa, lo redirigimos a la pagina principal
if (isset($_SESSION['usuario_id'])) {
    header('Location: /');
    exit();
}

$error = "";
$email = "";

// procesar el formulario cuando se envia mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // validacion sencilla en el lado del servidor
    if (empty($email) || empty($password)) {
        $error = "por favor, introduce tu correo y contraseña.";
    } else {
        // inicializar conexion y modelo de usuarios
        $database = new Database();
        $db = $database->obtenerConexion();
        $usuarioModel = new UsuarioModel($db);

        // buscar el usuario por su email
        $usuario = $usuarioModel->obtenerPorEmail($email);

        // si el usuario existe y la contraseña es correcta (verificada con bcrypt)
        if ($usuario && password_verify($password, $usuario['password'])) {
            // guardamos las variables de sesion
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol'];

            // si el usuario selecciono "recordarme", bajamos la cookie por 30 dias
            if (isset($_POST['recordarme'])) {
                setcookie('recordar_usuario', $usuario['id'], time() + (30 * 24 * 60 * 60), "/");
            }

            // si es administrador, lo redirigimos al panel de control
            if ($usuario['rol'] === 'admin') {
                header('Location: /admin');
            } else {
                header('Location: /catalogo');
            }
            exit();
        } else {
            $error = "correo electronico o contraseña incorrectos.";
        }
    }
}

$titulo = "Iniciar Sesión";
require_once __DIR__ . '/components/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow border-0 rounded-4 overflow-hidden">
                <!-- cabecera de la tarjeta con estilo dark -->
                <div class="bg-dark text-white py-4 text-center">
                    <h3 class="fw-bold mb-1">Área Cliente</h3>
                    <p class="mb-0 text-white-50">Introduce tus credenciales para acceder</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <!-- mostrar alerta si hay algun error -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger mb-4" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <!-- seccion de inicio de sesion -->
                    <div id="login-section">
                        <form action="/login" method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Correo Electrónico *</label>
                                <input type="email" class="form-control py-2" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required placeholder="ejemplo@correo.com">
                                <div class="invalid-feedback">Introduce un correo electrónico válido.</div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Contraseña *</label>
                                <input type="password" class="form-control py-2" id="password" name="password" required placeholder="Introduce tu contraseña">
                                <div class="invalid-feedback">Introduce tu contraseña.</div>
                            </div>

                            <!-- recordar usuario y recuperar contrasena en la misma fila -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="recordarme" id="recordarme">
                                    <label class="form-check-label small text-muted" for="recordarme">
                                        Recordarme
                                    </label>
                                </div>
                                <div>
                                    <a href="#" id="btn-show-recuperar" class="text-muted small text-decoration-none">¿Has olvidado tu contraseña?</a>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold text-dark py-2 shadow-sm mb-3">Iniciar Sesión</button>
                            
                            <div class="text-center small">
                                <span class="text-muted">¿No tienes cuenta?</span>
                                <a href="/registro" class="text-dark fw-bold text-decoration-none">Regístrate aquí</a>
                            </div>
                        </form>
                    </div>

                    <!-- seccion de recuperacion de contraseña (oculta hasta que se hace click en "olvido su contraseña?") -->
                    <div id="recuperar-section" class="d-none">
                        <p class="text-muted small mb-4">Introduce tu correo electrónico y te enviaremos las instrucciones para restablecer tu contraseña.</p>
                        <form id="form-recuperar" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="email_recuperar" class="form-label fw-bold">Correo Electrónico *</label>
                                <input type="email" class="form-control py-2" id="email_recuperar" required placeholder="ejemplo@correo.com">
                                <div class="invalid-feedback">Introduce un correo electrónico válido.</div>
                            </div>

                            <button type="submit" class="btn btn-dark btn-lg w-100 fw-bold py-2 shadow-sm mb-3">Enviar Enlace</button>
                            
                            <div class="text-center">
                                <a href="#" id="btn-show-login" class="text-dark fw-bold small text-decoration-none">Volver al Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// carga el pie de pagina comun
require_once __DIR__ . '/components/footer.php';
?>
