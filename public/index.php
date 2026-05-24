<?php
// iniciamos la sesion de PHP para guardar los datos del usuario logueado en todo el sitio
session_start();

// autologin si la sesion esta vacia pero existe la cookie 'recordar_usuario'
if (!isset($_SESSION['usuario_id']) && isset($_COOKIE['recordar_usuario'])) {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../src/Models/UsuarioModel.php';
    
    $database = new Database();
    $db = $database->obtenerConexion();
    $usuarioModel = new UsuarioModel($db);
    $usuario = $usuarioModel->obtenerPorId(intval($_COOKIE['recordar_usuario']));
    
    if ($usuario) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
    }
}

/*El spl_autoload_register es una funcion que se encarga de cargar las clases que no se han incluido manualmente
y asi nos evitamos tener que estar metiendo require o include en cada archivo, ademas nos ahorra lineas de codigo y hace que el codigo sea mas limpio*/


// Habilita la visualizacion de errores para facilitar el desarrollo local
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Carga el archivo de conexion de la base de datos solo una vez
require_once __DIR__ . '/../config/database.php';

// Registra un cargador automatico para no usar require manual en cada archivo
spl_autoload_register(function ($nombreClase) {
    // Buscar el archivo dentro de la carpeta Controllers
    $rutaControlador = __DIR__ . '/../src/Controllers/' . $nombreClase . '.php';
    if (file_exists($rutaControlador)) {
        require_once $rutaControlador;
        return;
    }

    // Busca el archivo dentro de la carpeta Models
    $rutaModelo = __DIR__ . '/../src/Models/' . $nombreClase . '.php';
    if (file_exists($rutaModelo)) {
        require_once $rutaModelo;
        return;
    }
});

// Limpiar la URL de la peticion
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Router principal del proyecto mediante estructura switch
switch ($uri) {
    // Ruta de inicio
    case '/':
    case '/index.php':
        $controller = new HomeController();
        $controller->index();
        break;

    case '/catalogo':
        require_once __DIR__ . '/../src/Views/catalogo.php';
        break;

    case '/reservar':
        require_once __DIR__ . '/../src/Views/reservar.php';
        break;


    case '/contacto':
        require_once __DIR__ . '/../src/Views/contacto.php';
        break;

    case '/login':
        require_once __DIR__ . '/../src/Views/login.php';
        break;

    case '/registro':
        require_once __DIR__ . '/../src/Views/registro.php';
        break;

    case '/admin':
        require_once __DIR__ . '/../src/Views/admin.php';
        break;

    case '/logout':
        // borramos la cookie de recordar sesion del navegador
        setcookie('recordar_usuario', '', time() - 3600, "/");

        // destruimos la sesion actual para cerrar sesion
        session_destroy();
        header('Location: /login');
        exit();
        break;

    // Si la ruta no coincide con ninguna de las anteriores, devolvemos error 404
    default:
        http_response_code(404);
        require_once __DIR__ . '/../src/Views/404.php';
        break;
}
