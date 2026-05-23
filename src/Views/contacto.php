<?php
$titulo = "Contacto";

require_once __DIR__ . '/components/header.php';

// Variables de control de estado del formulario
$mensajeEnviado = false;
$error = "";

// Procesar el envio del formulario mediante peticion POST de PHP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura y limpieza de datos basica del formulario
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';

    // Validacion sencilla de campos obligatorios
    if (empty($nombre) || empty($email) || empty($mensaje)) {
        $error = "Por favor, rellena todos los campos obligatorios.";
    } else {
        // Placeholder para el envio del formulario
        $mensajeEnviado = true;
    }
}
?>

<div class="container my-5">
    <div class="row">
        <!-- Columna de informacion fisica del taller -->
        <div class="col-md-5 mb-4">
            <div class="p-4 rounded border">
                <h3 class="fw-bold text-dark mb-4">Información de Contacto</h3>
                <p class="text-muted">Si tienes alguna duda sobre nuestras reparaciones o necesitas consultar un presupuesto especial, ponte en contacto con nosotros.</p>
                <hr>
                <p class="mb-2"><strong>Dirección:</strong> Calle Aleatoria, 67, Torrevieja</p>
                <p class="mb-2"><strong>Teléfono:</strong> 600 000 000</p>
                <p class="mb-2"><strong>Correo:</strong> contacto@phonetech.es</p>
                <hr>
            </div>
        </div>

        <!-- Columna del Formulario de Contacto -->
        <div class="col-md-7">
            <div class="p-4 rounded border shadow-sm bg-white">
                <h3 class="fw-bold text-dark mb-4 text-center">¡Contacta con nosotros!</h3>

                <!-- Mensaje de exito al enviar el formulario -->
                <?php if ($mensajeEnviado): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>¡Mensaje enviado con éxito!</strong>
                    </div>
                <?php endif; ?>

                <!-- Mensaje de error si falla la validacion -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- Formulario de contacto con accion a la misma pagina -->
                <form action="" method="POST" class="needs-validation">
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-bold">Nombre Completo *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Escribe tu nombre y apellidos">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Correo Electrónico*</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="ejemplo@correo.com">
                    </div>

                    <div class="mb-3">
                        <label for="telefono" class="form-label fw-bold">Teléfono (Opcional)</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Introduce tu número de teléfono">
                    </div>

                    <div class="mb-3">
                        <label for="mensaje" class="form-label fw-bold">Mensaje / Consulta *</label>
                        <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required placeholder="Escribe tu consulta aquí..."></textarea>
                    </div>

                    <!-- Boton de envio con estetica warning similar al resto de la web -->
                    <button type="submit" class="btn btn-warning w-100 fw-bold text-dark shadow-sm">Enviar Mensaje</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Cargar el pie de pagina comun
require_once __DIR__ . '/components/footer.php';
?>
