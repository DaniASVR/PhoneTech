<?php
$titulo = "Reservar Cita";
require_once __DIR__ . '/components/header.php';

// instanciar la conexion de base de datos
$database = new Database();
$db = $database->obtenerConexion();

// instanciar los modelos
$servicioModel = new ServicioModel($db);
$citaModel = new CitaModel($db);

// obtener todos los servicios para el selector
$servicios = $servicioModel->obtenerTodos();

// variables para controlar el estado de la reserva
$reservaCreada = false;
$error = "";
$datos = [
    'nombre_noregistrado' => '',
    'email_noregistrado' => '',
    'telefono_noregistrado' => '',
    'dispositivo_modelo' => '',
    'servicio_id' => '',
    'fecha_hora' => '',
    'fecha' => '',
    'hora' => ''
];

// comprobar si nos viene un servicio_id por la URL desde catalogo
if (isset($_GET['servicio_id'])) {
    $datos['servicio_id'] = intval($_GET['servicio_id']);
}

// procesar el envio del formulario mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos['nombre_noregistrado'] = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $datos['email_noregistrado'] = isset($_POST['email']) ? trim($_POST['email']) : '';
    $datos['telefono_noregistrado'] = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $datos['dispositivo_modelo'] = isset($_POST['dispositivo']) ? trim($_POST['dispositivo']) : '';
    $datos['servicio_id'] = isset($_POST['servicio_id']) ? intval($_POST['servicio_id']) : '';
    
    // leemos la fecha y hora por separado y las unimos
    $datos['fecha'] = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
    $datos['hora'] = isset($_POST['hora']) ? trim($_POST['hora']) : '';
    $datos['fecha_hora'] = ($datos['fecha'] !== '' && $datos['hora'] !== '') ? $datos['fecha'] . ' ' . $datos['hora'] . ':00' : '';
    
    $datos['usuario_id'] = null; // de momento no hay login implementado

    // validacion sencilla en el lado del servidor
    if (empty($datos['nombre_noregistrado']) || empty($datos['email_noregistrado']) || empty($datos['telefono_noregistrado']) || empty($datos['dispositivo_modelo']) || empty($datos['servicio_id']) || empty($datos['fecha']) || empty($datos['hora'])) {
        $error = "por favor, completa todos los campos del formulario.";
    } elseif (!filter_var($datos['email_noregistrado'], FILTER_VALIDATE_EMAIL)) {
        $error = "el correo electronico introducido no es valido.";
    } elseif (strtotime($datos['fecha_hora']) < time()) {
        $error = "la fecha y hora de la cita deben ser en el futuro.";
    } else {
        // obtener hora y minutos de la fecha seleccionada para validar restricciones
        $timestamp = strtotime($datos['fecha_hora']);
        $hora = intval(date('H', $timestamp));
        $minutos = intval(date('i', $timestamp));

        if ($minutos !== 0) {
            $error = "las citas solo pueden reservarse de hora en hora (ej: 10:00, 11:00).";
        } elseif ($hora < 10 || $hora > 21) {
            $error = "el horario de atencion es de 10:00 AM a 9:00 PM (21:00).";
        } elseif (!$citaModel->comprobarDisponibilidad($datos['fecha_hora'])) {
            $error = "lo sentimos, la hora seleccionada ya esta ocupada por otro cliente.";
        } else {
            // guardar la cita en la base de datos
            if ($citaModel->crear($datos)) {
                $reservaCreada = true;
            } else {
                $error = "no se pudo registrar la cita. intentalo de nuevo mas tarde.";
            }
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-4 overflow-hidden">
                <!-- cabecera de la tarjeta con estilo warning -->
                <div class="bg-warning text-dark py-4 text-center">
                    <h2 class="fw-bold mb-1">Reserva tu Cita</h2>
                    <p class="mb-0 text-dark-50">Elige la fecha y hora para la reparacion de tu dispositivo</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if ($reservaCreada): ?>
                        <!-- mensaje de exito cuando la cita se guarda -->
                        <div class="text-center py-4">
                            <h3 class="fw-bold text-dark mb-3">Cita Reservada con éxito</h3>
                            <p class="text-muted mb-4">
                                Hemos registrado tu solicitud de cita para reparar tu <strong><?php echo htmlspecialchars($datos['dispositivo_modelo']); ?></strong>.
                                Te esperamos el día <strong><?php echo date('d/m/Y a las H:i', strtotime($datos['fecha_hora'])); ?></strong>
                            </p>
                            <div class="border rounded p-3 mb-4 bg-light text-start">
                                <h6 class="fw-bold text-dark mb-2">Detalles del contacto:</h6>
                                <p class="mb-1"><strong>Nombre:</strong> <?php echo htmlspecialchars($datos['nombre_noregistrado']); ?></p>
                                <p class="mb-1"><strong>Teléfono:</strong> <?php echo htmlspecialchars($datos['telefono_noregistrado']); ?></p>
                                <p class="mb-0"><strong>Correo:</strong> <?php echo htmlspecialchars($datos['email_noregistrado']); ?></p>
                            </div>
                            <a href="/catalogo" class="btn btn-warning fw-bold text-dark px-4 py-2 shadow-sm">Volver al Catálogo</a>
                        </div>
                    <?php else: ?>
                        <!-- mensaje de error si falla la validacion o el guardado -->
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger mb-4" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <!-- formulario de reserva -->
                        <form action="" method="POST" id="form-reserva" class="needs-validation" novalidate>
                            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Datos de Contacto</h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="nombre" class="form-label fw-bold">Nombre Completo *</label>
                                    <input type="text" class="form-control py-2" id="nombre" name="nombre" value="<?php echo htmlspecialchars($datos['nombre_noregistrado']); ?>" required placeholder="Introduce tu nombre">
                                </div>
                                <div class="col-md-6">
                                    <label for="telefono" class="form-label fw-bold">Teléfono de Contacto *</label>
                                    <input type="tel" class="form-control py-2" id="telefono" name="telefono" value="<?php echo htmlspecialchars($datos['telefono_noregistrado']); ?>" required placeholder="Ej: 600000000">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold">Correo Electrónico *</label>
                                <input type="email" class="form-control py-2" id="email" name="email" value="<?php echo htmlspecialchars($datos['email_noregistrado']); ?>" required placeholder="ejemplo@correo.com">
                            </div>

                            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Detalles del Servicio</h5>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="servicio_id" class="form-label fw-bold">Tipo de Reparación *</label>
                                    <select class="form-select py-2" id="servicio_id" name="servicio_id" required>
                                        <option value="">-- Selecciona un servicio --</option>
                                        <?php foreach ($servicios as $s): ?>
                                            <option value="<?php echo $s['id']; ?>" <?php echo ($datos['servicio_id'] === intval($s['id'])) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($s['nombre']); ?> (<?php echo number_format($s['precio'], 2); ?>€)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="dispositivo" class="form-label fw-bold">Modelo del Dispositivo *</label>
                                    <input type="text" class="form-control py-2" id="dispositivo" name="dispositivo" value="<?php echo htmlspecialchars($datos['dispositivo_modelo']); ?>" required placeholder="Ej: iPhone 13 Pro, Galaxy S22">
                                </div>
                            </div>

                             <div class="row mb-4">
                                 <div class="col-md-6 mb-3 mb-md-0">
                                     <label for="fecha" class="form-label fw-bold">Fecha de la Cita *</label>
                                     <input type="date" class="form-control py-2" id="fecha" name="fecha" value="<?php echo htmlspecialchars($datos['fecha']); ?>" required>
                                 </div>
                                 <div class="col-md-6">
                                     <label for="hora" class="form-label fw-bold">Hora de la Cita *</label>
                                     <select class="form-select py-2" id="hora" name="hora" required>
                                         <option value="">-- Selecciona hora --</option>
                                         <?php 
                                         for ($h = 10; $h <= 21; $h++): 
                                             $h_formato = sprintf('%02d:00', $h);
                                         ?>
                                             <option value="<?php echo $h_formato; ?>" <?php echo ($datos['hora'] === $h_formato) ? 'selected' : ''; ?>>
                                                 <?php echo $h_formato; ?>
                                             </option>
                                         <?php endfor; ?>
                                     </select>
                                 </div>
                             </div>

                            <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold text-dark py-3 shadow-sm">Confirmar Reserva de Cita</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// carga el pie de pagina comun
require_once __DIR__ . '/components/footer.php';
?>
