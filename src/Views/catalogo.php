<?php
// definir el titulo de la pagina
$titulo = "Catálogo";

// carga la cabecera comun
require_once __DIR__ . '/components/header.php';

// instancia la conexion de base de datos
$database = new Database();
$db = $database->obtenerConexion();

// instancia el modelo de servicios
$servicioModel = new ServicioModel($db);

// obtiene la lista de servicios desde MySQL
$reparaciones = $servicioModel->obtenerTodos();
?>

<!-- seccion Hero del Catalogo -->
<section class="catalog-hero py-5 bg-light border-bottom mb-5">
    <div class="container">
        <div class="row align-items-center">
            <!-- imagen colocada a la izquierda -->
            <div class="col-lg-5 col-md-12 text-center mb-4 mb-lg-0">
                <img src="/images/hero_repair.png" alt="Catálogo PhoneTech" class="img-fluid rounded-4 shadow-lg" style="max-height: 280px; object-fit: cover;">
            </div>
            <!-- titulo y buscador a la derecha -->
            <div class="col-lg-7 col-md-12 text-center text-lg-start ps-lg-5">
                <h1 class="display-4 fw-bold text-dark mb-3">Catálogo de Reparaciones</h1>
                <p class="lead text-muted mb-4">
                    Busca el servicio que necesitas para tu teléfono. Ofrecemos precios cerrados con mano de obra y piezas de alta calidad.
                </p>
                <!-- buscador interactivo -->
                <div class="d-flex max-width-form mx-auto mx-lg-0">
                    <input type="text" id="buscar-input" class="form-control py-2 shadow-sm" placeholder="¿Qué necesitas reparar? (Ej. Pantalla, Batería...)">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- listado de servicios del Catalogo -->
<section class="catalog-list pb-5">
    <div class="container">
        <div class="row justify-content-center">
            <!-- iterar sobre el listado completo de reparaciones -->
            <?php foreach ($reparaciones as $r): ?>
                <div class="col-lg-4 col-md-6 mb-4 servicio-tarjeta">
                    <div class="card h-100 shadow-sm border">
                        
                        
                        <?php 
                        // comprobar si la ruta de la imagen esta vacia o no existe, y usar la por defecto en ese caso
                        $rutaImagen = (!empty($r['imagen']) && file_exists(__DIR__ . '/../../public' . $r['imagen'])) ? $r['imagen'] : '/images/default_service.png';
                        ?>


                        <img src="<?php echo htmlspecialchars($rutaImagen); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($r['nombre']); ?>" style="height: 180px; object-fit: cover;">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="card-title text-dark fw-bold mb-2"><?php echo htmlspecialchars($r['nombre']); ?></h5>
                                <p class="card-text text-muted small"><?php echo htmlspecialchars($r['descripcion']); ?></p>
                            </div>
                            <div class="mt-3">
                                <h3 class="text-warning fw-bold mb-3"><?php echo number_format($r['precio'], 2); ?>€</h3>
                                <div>
                                    <a href="/reservar?servicio_id=<?php echo $r['id']; ?>" class="btn btn-outline-warning w-100 fw-bold text-dark">Reservar Cita</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- mensaje sin resultados -->
            <div id="sin-resultados" class="col-12 text-center py-5" style="display: none;">
                <h4 class="fw-bold text-dark">No se encontraron resultados</h4>
                <p class="text-muted">No se han encontrado reparaciones.</p>
            </div>
        </div>
    </div>
</section>

<?php
// cargar el footer comun que contiene el js
require_once __DIR__ . '/components/footer.php';
?>
