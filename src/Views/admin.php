<?php

// si el usuario no tiene el rol de administrador, no puede ver esta pagina y es redirigido a login
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: /login');
    exit();
}

// instanciamos la conexion a la base de datos
$database = new Database();
$db = $database->obtenerConexion();

// cargamos el modelo de citas
$citaModel = new CitaModel($db);

$mensajeExito = "";
$mensajeError = "";

// procesar las acciones de la cita si se envia por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $citaId = isset($_POST['cita_id']) ? intval($_POST['cita_id']) : 0;
    $accion = isset($_POST['accion']) ? trim($_POST['accion']) : '';

    if ($citaId > 0) {
        if ($accion === 'eliminar') {
            // accion para borrar una cita de la base de datos
            if ($citaModel->eliminar($citaId)) {
                $mensajeExito = "¡La cita #" . $citaId . " ha sido eliminada con éxito!";
            } else {
                $mensajeError = "Ocurrió un error al intentar eliminar la cita.";
            }
        } elseif ($accion === 'actualizar') {
            // accion para guardar los cambios editados de la cita
            $datosActualizar = [
                'nombre_noregistrado' => isset($_POST['nombre']) ? trim($_POST['nombre']) : '',
                'email_noregistrado' => isset($_POST['email']) ? trim($_POST['email']) : '',
                'telefono_noregistrado' => isset($_POST['telefono']) ? trim($_POST['telefono']) : '',
                'dispositivo_modelo' => isset($_POST['dispositivo']) ? trim($_POST['dispositivo']) : '',
                'servicio_id' => isset($_POST['servicio_id']) ? intval($_POST['servicio_id']) : 0,
                'fecha' => isset($_POST['fecha']) ? trim($_POST['fecha']) : '',
                'hora' => isset($_POST['hora']) ? trim($_POST['hora']) : '',
                'comentarios' => isset($_POST['comentarios']) ? trim($_POST['comentarios']) : ''
            ];

            // validar que los campos requeridos esten llenos
            if (empty($datosActualizar['nombre_noregistrado']) || empty($datosActualizar['email_noregistrado']) || empty($datosActualizar['telefono_noregistrado']) || empty($datosActualizar['dispositivo_modelo']) || empty($datosActualizar['servicio_id']) || empty($datosActualizar['fecha']) || empty($datosActualizar['hora'])) {
                $mensajeError = "Por favor, rellena todos los campos obligatorios de la cita.";
            } else {
                // concatenar la fecha y hora seleccionada
                $datosActualizar['fecha_hora'] = $datosActualizar['fecha'] . ' ' . $datosActualizar['hora'] . ':00';
                
                if ($citaModel->actualizar($citaId, $datosActualizar)) {
                    $mensajeExito = "¡La cita #" . $citaId . " ha sido actualizada correctamente!";
                    // redirigimos para limpiar la URL del parametro editar_id y refrescar
                    header('Location: /admin?mensaje_exito=' . urlencode($mensajeExito));
                    exit();
                } else {
                    $mensajeError = "Ha ocurrido un error al intentar actualizar la cita.";
                }
            }
        } else {
            // procesar el cambio de estado de la cita (comportamiento existente)
            $nuevoEstado = isset($_POST['estado']) ? trim($_POST['estado']) : '';
            if ($nuevoEstado === 'pendiente' || $nuevoEstado === 'completada' || $nuevoEstado === 'cancelada') {
                if ($citaModel->actualizarEstado($citaId, $nuevoEstado)) {
                    $mensajeExito = "¡El estado de la cita #" . $citaId . " se ha actualizado correctamente a " . $nuevoEstado . "!";
                } else {
                    $mensajeError = "Ha ocurrido un error al intentar cambiar el estado de la cita.";
                }
            }
        }
    }
}

// comprobar si venimos de una redireccion exitosa tras actualizar
if (isset($_GET['mensaje_exito'])) {
    $mensajeExito = $_GET['mensaje_exito'];
}

// obtener la lista completa de citas
$citas = $citaModel->obtenerTodas();


$titulo = "Panel de Control";
require_once __DIR__ . '/components/header.php';
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h1 class="fw-bold text-dark mb-0">Panel de Administración</h1>
            <p class="text-muted mb-0">Gestión de citas y estado de reparaciones de PhoneTech</p>
        </div>
        <span class="badge bg-dark py-2 px-3 fs-6">Modo: Administrador</span>
    </div>

    <!-- alertas de procesamiento del formulario -->
    <?php if (!empty($mensajeExito)): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <?php echo htmlspecialchars($mensajeExito); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <?php echo htmlspecialchars($mensajeError); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>


    <!-- formulario de edición de citas -->
    <?php 
    if (isset($_GET['editar_id'])): 
        $editarId = intval($_GET['editar_id']);
        $citaEditar = $citaModel->obtenerPorId($editarId);
        
        if ($citaEditar):
            // cargar los servicios para el select
            $servicioModel = new ServicioModel($db);
            $servicios = $servicioModel->obtenerTodos();
            
            // separar fecha y hora
            $fechaCita = date('Y-m-d', strtotime($citaEditar['fecha_hora']));
            $horaCita = date('H:i', strtotime($citaEditar['fecha_hora']));
            
            // cargar datos de contacto de la cita
            $nombreCita = $citaEditar['nombre_noregistrado'] ?? '';
            $emailCita = $citaEditar['email_noregistrado'] ?? '';
            $telefonoCita = $citaEditar['telefono_noregistrado'] ?? '';
            
            // si estuvieran vacios por algun motivo e iniciados con usuario registrado, buscar datos de respaldo
            if (empty($nombreCita) && $citaEditar['usuario_id'] !== null) {
                $usuarioModel = new UsuarioModel($db);
                $usuarioInfo = $usuarioModel->obtenerPorId($citaEditar['usuario_id']);
                if ($usuarioInfo) {
                    $nombreCita = $usuarioInfo['nombre'];
                    $emailCita = $usuarioInfo['email'];
                    $telefonoCita = $usuarioInfo['telefono'];
                }
            }
    ?>
        <!-- formulario de edición -->
        <div class="card shadow border-0 rounded-4 mb-5 border-warning">
            <div class="bg-warning text-dark px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-pencil-square me-2"></i>Editar Cita #<?php echo $editarId; ?>
                </h5>
                <a href="/admin" class="btn-close" aria-label="Close"></a>
            </div>
            <div class="card-body p-4">
                <form action="/admin" method="POST">
                    <input type="hidden" name="cita_id" value="<?php echo $editarId; ?>">
                    <input type="hidden" name="accion" value="actualizar">
                    
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Datos de Contacto</h6>
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label for="edit_nombre" class="form-label fw-bold small">Nombre *</label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" value="<?php echo htmlspecialchars($nombreCita); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label for="edit_telefono" class="form-label fw-bold small">Teléfono *</label>
                            <input type="tel" class="form-control" id="edit_telefono" name="telefono" value="<?php echo htmlspecialchars($telefonoCita); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_email" class="form-label fw-bold small">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="edit_email" name="email" value="<?php echo htmlspecialchars($emailCita); ?>" required>
                        </div>
                    </div>
                    
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2 mt-4">Detalles del Servicio</h6>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="edit_servicio_id" class="form-label fw-bold small">Tipo de Reparación *</label>
                            <select class="form-select" id="edit_servicio_id" name="servicio_id" required>
                                <?php foreach ($servicios as $s): ?>
                                    <option value="<?php echo $s['id']; ?>" <?php echo (intval($citaEditar['servicio_id']) === intval($s['id'])) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($s['nombre']); ?> (<?php echo number_format($s['precio'], 2); ?>€)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_dispositivo" class="form-label fw-bold small">Modelo del Dispositivo *</label>
                            <input type="text" class="form-control" id="edit_dispositivo" name="dispositivo" value="<?php echo htmlspecialchars($citaEditar['dispositivo_modelo']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="edit_fecha" class="form-label fw-bold small">Fecha de la Cita *</label>
                            <input type="date" class="form-control" id="edit_fecha" name="fecha" value="<?php echo $fechaCita; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_hora" class="form-label fw-bold small">Hora de la Cita *</label>
                            <select class="form-select" id="edit_hora" name="hora" required>
                                <?php 
                                for ($h = 10; $h <= 21; $h++): 
                                    $h_formato = sprintf('%02d:00', $h);
                                ?>
                                    <option value="<?php echo $h_formato; ?>" <?php echo ($horaCita === $h_formato) ? 'selected' : ''; ?>>
                                        <?php echo $h_formato; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="edit_comentarios" class="form-label fw-bold small">Comentarios / Notas de la Avería</label>
                        <textarea class="form-control" id="edit_comentarios" name="comentarios" rows="3"><?php echo htmlspecialchars($citaEditar['comentarios'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="/admin" class="btn btn-outline-secondary fw-bold px-4">Cancelar</a>
                        <button type="submit" class="btn btn-warning fw-bold text-dark px-4 shadow-sm">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    <?php 
        endif; 
    endif; 
    ?>

    <!-- listado de citas en tabla -->
    <div class="card shadow border-0 rounded-4 overflow-hidden">
        <div class="bg-dark text-white px-4 py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Citas Registradas</h5>
            <span class="badge bg-warning text-dark fw-bold"><?php echo count($citas); ?> registradas</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" class="ps-4">ID</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Email</th>
                            <th scope="col">Dispositivo</th>
                            <th scope="col">Notas</th>
                            <th scope="col">Servicio</th>
                            <th scope="col">Fecha y Hora</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="pe-4 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($citas)): ?>
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <h5 class="mb-1">No hay citas registradas en la base de datos</h5>
                                    <p class="small mb-0">Las reservas que hagan los clientes aparecerán listadas aquí.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($citas as $c): 
                                // comprobar si es usuario registrado o invitado para obtener sus datos de contacto
                                if ($c['usuario_id'] !== null) {
                                    $nombreCliente = $c['usuario_nombre'];
                                    $telefonoCliente = $c['usuario_telefono'];
                                    $emailCliente = $c['usuario_email'];
                                } else {
                                    $nombreCliente = $c['nombre_noregistrado'];
                                    $telefonoCliente = $c['telefono_noregistrado'];
                                    $emailCliente = $c['email_noregistrado'];
                                }
                            ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#<?php echo $c['id']; ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($nombreCliente); ?></div>
                                        <?php if ($c['usuario_id'] !== null): ?>
                                            <span class="badge bg-info text-dark small-badge" style="font-size: 0.7rem;">Registrado</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary small-badge" style="font-size: 0.7rem;">Invitado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($telefonoCliente ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($emailCliente ?? ''); ?></td>
                                    <td class="fw-medium"><?php echo htmlspecialchars($c['dispositivo_modelo'] ?? ''); ?></td>
                                    <td class="small text-muted text-break" style="max-width: 180px;"><?php echo htmlspecialchars($c['comentarios'] ?? ''); ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($c['servicio_nombre']); ?></div>
                                        <div class="small text-muted"><?php echo number_format($c['servicio_precio'], 2); ?>€</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo date('d/m/Y', strtotime($c['fecha_hora'])); ?></div>
                                        <div class="small text-muted"><?php echo date('H:i \h', strtotime($c['fecha_hora'])); ?></div>
                                    </td>
                                    <td>
                                        <?php 
                                        $badgeClass = "bg-warning text-dark";
                                        if ($c['estado'] === 'completada') {
                                            $badgeClass = "bg-success text-white";
                                        } elseif ($c['estado'] === 'cancelada') {
                                            $badgeClass = "bg-danger text-white";
                                        }
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?> px-3 py-2 text-capitalize">
                                            <?php echo htmlspecialchars($c['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end align-items-center gap-1">
                                            <!-- boton para editar la cita -->
                                            <a href="/admin?editar_id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Editar datos de la cita">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <!-- formulario para eliminar la cita con confirmacion js -->
                                            <form action="/admin" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar permanentemente la cita #<?php echo $c['id']; ?>?');">
                                                <input type="hidden" name="cita_id" value="<?php echo $c['id']; ?>">
                                                <input type="hidden" name="accion" value="eliminar">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Eliminar cita">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>

                                            <span class="text-muted me-2">|</span>

                                            <!-- botones para cambiar el estado de la cita -->
                                            <div class="d-inline-flex gap-1">
                                                <?php if ($c['estado'] !== 'pendiente'): ?>
                                                    <form action="/admin" method="POST" class="d-inline m-0">
                                                        <input type="hidden" name="cita_id" value="<?php echo $c['id']; ?>">
                                                        <input type="hidden" name="estado" value="pendiente">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Marcar como Pendiente">
                                                            Pendiente
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <?php if ($c['estado'] !== 'completada'): ?>
                                                    <form action="/admin" method="POST" class="d-inline m-0">
                                                        <input type="hidden" name="cita_id" value="<?php echo $c['id']; ?>">
                                                        <input type="hidden" name="estado" value="completada">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Marcar como Completada">
                                                            Completar
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <?php if ($c['estado'] !== 'cancelada'): ?>
                                                    <form action="/admin" method="POST" class="d-inline m-0">
                                                        <input type="hidden" name="cita_id" value="<?php echo $c['id']; ?>">
                                                        <input type="hidden" name="estado" value="cancelada">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Cancelar Cita">
                                                            Cancelar
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// carga el pie de pagina comun
require_once __DIR__ . '/components/footer.php';
?>
