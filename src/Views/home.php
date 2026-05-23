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

<section class="services-section py-5">
    <div class="container">
        <!-- Seccion de servicios -->
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark">Nuestros Servicios</h2>
            <p class="text-muted">Las reparaciones más solicitadas en taller con repuestos garantizados y mano de obra profesional.</p>
        </div>

        <div class="row justify-content-center">
            <!-- Tarjeta 1 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm border">
                    <img src="/images/screen_repair.png" class="card-img-top" alt="Cambio de Pantalla iPhone 13" style="height: 180px; object-fit: cover;">
                    <div class="card-body text-center d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title text-dark fw-bold mb-2">Cambio de Pantalla</h5>
                            <p class="card-text text-muted small">
                                Reparación completa del panel táctil y cristal OLED con repuestos de alta calidad y calibración.
                            </p>
                        </div>
                        <div class="mt-3">
                            <h3 class="text-warning fw-bold mb-3">129.99€</h3>
                            <div>
                                <a href="/reservar" class="btn btn-outline-warning w-100 fw-bold text-dark">Reservar Cita</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 2 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm border">
                    <img src="/images/battery_repair.png" class="card-img-top" alt="Cambio de Batería iPhone 13" style="height: 180px; object-fit: cover;">
                    <div class="card-body text-center d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title text-dark fw-bold mb-2">Cambio de Batería</h5>
                            <p class="card-text text-muted small">
                                Sustitución de batería degradada por una nueva.
                            </p>
                        </div>
                        <div class="mt-3">
                            <h3 class="text-warning fw-bold mb-3">59.99€</h3>
                            <div>
                                <a href="/reservar" class="btn btn-outline-warning w-100 fw-bold text-dark">Reservar Cita</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 3-->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm border">
                    <img src="/images/connector_repair.png" class="card-img-top" alt="Conector de Carga iPhone 13" style="height: 180px; object-fit: cover;">
                    <div class="card-body text-center d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title text-dark fw-bold mb-2">Conector de Carga</h5>
                            <p class="card-text text-muted small">
                                Reparación o cambio del puerto de carga Lightning.
                            </p>
                        </div>
                        <div class="mt-3">
                            <h3 class="text-warning fw-bold mb-3">49.99€</h3>
                            <div>
                                <a href="/reservar" class="btn btn-outline-warning w-100 fw-bold text-dark">Reservar Cita</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Carga el pie de pagina comun
require_once __DIR__ . '/components/footer.php';
?>

