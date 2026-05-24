<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php 
        // Comprobar si la pagina ha definido un titulo especifico
        if (isset($titulo)) {
            echo "PhoneTech - " . $titulo;
        } else {
            echo "PhoneTech - Tu Taller de Moviles";
        }
        ?>
    </title>
    
    <!-- Enlace al CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    
    <link href="/css/main.css" rel="stylesheet">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            
            <a class="navbar-brand font-weight-bold" href="/">PhoneTech</a>
            
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Enlaces del menu de navegacion -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/catalogo">Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/reservar">Reservar Cita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contacto">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Área Cliente</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
