<?php
// Vista para el registro de nuevos usuarios clientes

// si el usuario ya tiene la sesion activa, lo redirigimos a la pagina principal
if (isset($_SESSION['usuario_id'])) {
    header('Location: /');
    exit();
}

$error = "";
$nombre = "";
$email = "";
$telefono = "";

// procesar el formulario cuando se envia mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $password_confirm = isset($_POST['password_confirm']) ? trim($_POST['password_confirm']) : '';

    // validacion en el lado del servidor
    if (empty($nombre) || empty($email) || empty($telefono) || empty($password) || empty($password_confirm)) {
        $error = "por favor, completa todos los campos del formulario.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "por favor, introduce un correo electronico valido.";
    } elseif (!preg_match('/^[0-9]{9,15}$/', $telefono)) {
        $error = "el numero de telefono no es valido (debe contener solo digitos, entre 9 y 15).";
    } elseif (strlen($password) < 6) {
        $error = "la contraseña debe tener al menos 6 caracteres.";
    } elseif ($password !== $password_confirm) {
        $error = "las contraseñas introducidas no coinciden.";
    } else {
        // inicializar conexion y modelo de usuarios
        $database = new Database();
        $db = $database->obtenerConexion();
        $usuarioModel = new UsuarioModel($db);

        // verificar si el email ya existe en la base de datos
        $usuarioExistente = $usuarioModel->obtenerPorEmail($email);
        if ($usuarioExistente) {
            $error = "el correo electronico ya esta registrado.";
        } else {
            // encriptamos la contraseña con bcrypt para guardarla de forma segura
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // registrar el nuevo usuario
            if ($usuarioModel->crear($nombre, $email, $hashedPassword, $telefono, 'cliente')) {
                // obtener el usuario recien creado para iniciar sesion automaticamente
                $usuario = $usuarioModel->obtenerPorEmail($email);
                if ($usuario) {
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'];
                    $_SESSION['usuario_rol'] = $usuario['rol'];
                    
                    // redirigimos directamente al catalogo de servicios
                    header('Location: /catalogo');
                    exit();
                }
            } else {
                $error = "no se pudo crear la cuenta. intentalo de nuevo mas tarde.";
            }
        }
    }
}

$titulo = "Registrarse";
require_once __DIR__ . '/components/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow border-0 rounded-4 overflow-hidden">
                <!-- cabecera de la tarjeta con estilo dark -->
                <div class="bg-dark text-white py-4 text-center">
                    <h3 class="fw-bold mb-1">Crear Cuenta</h3>
                    <p class="mb-0 text-white-50">Regístrate para reservar y gestionar tus citas</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <!-- mostrar alerta si hay algun error -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger mb-4" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="/registro" method="POST" id="form-registro" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">Nombre Completo *</label>
                            <input type="text" class="form-control py-2" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required placeholder="Introduce tu nombre y apellidos">
                            <div class="invalid-feedback">Introduce tu nombre completo.</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Correo Electrónico *</label>
                            <input type="email" class="form-control py-2" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required placeholder="ejemplo@correo.com">
                            <div class="invalid-feedback">Introduce un correo electrónico válido.</div>
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label fw-bold">Teléfono de Contacto *</label>
                            <input type="tel" class="form-control py-2" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required placeholder="Ej: 600111222">
                            <div class="invalid-feedback">Introduce un teléfono de contacto válido (9-15 dígitos).</div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Contraseña *</label>
                            <input type="password" class="form-control py-2" id="password" name="password" required placeholder="Mínimo 6 caracteres">
                            <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirm" class="form-label fw-bold">Confirmar Contraseña *</label>
                            <input type="password" class="form-control py-2" id="password_confirm" name="password_confirm" required placeholder="Repite tu contraseña">
                            <div class="invalid-feedback">Por favor, confirma tu contraseña.</div>
                        </div>

                        <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold text-dark py-2 shadow-sm mb-3">Registrarse</button>
                        
                        <div class="text-center small">
                            <span class="text-muted">¿Ya tienes cuenta?</span>
                            <a href="/login" class="text-dark fw-bold text-decoration-none">Inicia sesión aquí</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// carga el pie de pagina comun
require_once __DIR__ . '/components/footer.php';
?>
