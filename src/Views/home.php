<?php
// Definir el titulo de la pagina de inicio
$titulo = "Inicio";

// Carga la cabecera comun
require_once __DIR__ . '/components/header.php';
?>

<!-- Sección Hero -->
<section class="hero-section py-5 bg-light border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12 text-center text-lg-start mb-4 mb-lg-0">
                <h1 class="display-4 fw-bold text-dark mb-3">
                    Reparamos tu móvil <span class="text-warning">al instante</span>
                </h1>
                <p class="lead text-muted mb-4">
                    En PhoneTech somos especialistas en la reparación de smartphones y tablets. Trabajamos con repuestos de alta calidad y te ofrecemos garantía en cada reparación para tu total tranquilidad. ¡Pide tu cita hoy mismo!
                </p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start justify-content-center">
                    <a href="/reservar" class="btn btn-warning btn-lg px-4 me-md-2 fw-bold text-dark shadow-sm">
                        Reserva tu Cita
                    </a>
                    <a href="/servicios" class="btn btn-outline-dark btn-lg px-4 shadow-sm">
                        Ver Precios
                    </a>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 text-center">
                <img src="/images/hero_repair.png" alt="Reparación de teléfonos PhoneTech" class="img-fluid rounded-4 shadow-lg" style="max-height: 380px; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

<?php
// Carga el pie de pagina comun
require_once __DIR__ . '/components/footer.php';
?>

