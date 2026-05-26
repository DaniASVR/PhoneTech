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

// procesar la actualizacion del estado de una cita si se envia por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $citaId = isset($_POST['cita_id']) ? intval($_POST['cita_id']) : 0;
    $nuevoEstado = isset($_POST['estado']) ? trim($_POST['estado']) : '';

    // validar que el estado sea uno de los permitidos en la base de datos
    if ($citaId > 0 && ($nuevoEstado === 'pendiente' || $nuevoEstado === 'completada' || $nuevoEstado === 'cancelada')) {
        if ($citaModel->actualizarEstado($citaId, $nuevoEstado)) {
            $mensajeExito = "¡El estado de la cita #" . $citaId . " se ha actualizado correctamente a " . $nuevoEstado . "!";
        } else {
            $mensajeError = "Ha ocurrido un error al intentar cambiar el estado de la cita.";
        }
    }
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
                                        <div class="btn-group" role="group">
                                            <?php if ($c['estado'] !== 'pendiente'): ?>
                                                <form action="/admin" method="POST" class="d-inline">
                                                    <input type="hidden" name="cita_id" value="<?php echo $c['id']; ?>">
                                                    <input type="hidden" name="estado" value="pendiente">
                                                    <button type="submit" class="btn btn-sm btn-outline-warning text-dark fw-bold me-1" title="Marcar como Pendiente">
                                                        Cita Pendiente
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <?php if ($c['estado'] !== 'completada'): ?>
                                                <form action="/admin" method="POST" class="d-inline">
                                                    <input type="hidden" name="cita_id" value="<?php echo $c['id']; ?>">
                                                    <input type="hidden" name="estado" value="completada">
                                                    <button type="submit" class="btn btn-sm btn-success fw-bold text-white me-1" title="Marcar como Completada">
                                                        Completar cita
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <?php if ($c['estado'] !== 'cancelada'): ?>
                                                <form action="/admin" method="POST" class="d-inline">
                                                    <input type="hidden" name="cita_id" value="<?php echo $c['id']; ?>">
                                                    <input type="hidden" name="estado" value="cancelada">
                                                    <button type="submit" class="btn btn-sm btn-danger fw-bold text-white" title="Cancelar Cita">
                                                        Cancelar cita
                                                    </button>
                                                </form>
                                            <?php endif; ?>
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
