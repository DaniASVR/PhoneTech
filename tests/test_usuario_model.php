<?php
// tests unitarios para el modelo de usuarios

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/UsuarioModel.php';

function testCrearUsuario($db, $emailTest) {
    $modelo = new UsuarioModel($db);
    $passHash = password_hash('123456', PASSWORD_BCRYPT);
    $resultado = $modelo->crear('Usuario Test', $emailTest, $passHash, '600000001', 'cliente');
    if ($resultado) {
        return [true, ''];
    }
    return [false, 'no se pudo insertar el usuario'];
}

function testObtenerPorEmail($db, $emailTest) {
    $modelo = new UsuarioModel($db);
    $usuario = $modelo->obtenerPorEmail($emailTest);
    if ($usuario && $usuario['email'] === $emailTest) {
        return [true, $usuario['id']];
    }
    return [false, 'no se encontro el usuario por email'];
}

function testObtenerPorId($db, $idCreado) {
    $modelo = new UsuarioModel($db);
    $usuario = $modelo->obtenerPorId($idCreado);
    if ($usuario && $usuario['nombre'] === 'Usuario Test') {
        return [true, ''];
    }
    return [false, 'no se encontro el usuario por ID'];
}

function testEmailNoExistente($db) {
    $modelo = new UsuarioModel($db);
    $noExiste = $modelo->obtenerPorEmail('noexiste_xyz@correo.com');
    if ($noExiste === false) {
        return [true, ''];
    }
    return [false, 'deberia devolver false para un email que no existe'];
}

// lanzar los tests al ejecutar el archivo
$database = new Database();
$db = $database->obtenerConexion();

echo "-- Tests unitarios: UsuarioModel --\n";

$emailTest = 'test_unitario_' . time() . '@phonetech.es';
$resultados = [];
$idCreado = null;

// Ejecutar test 1
$res1 = testCrearUsuario($db, $emailTest);
$resultados[] = ['testCrearUsuario', $res1[0], $res1[1]];

// Ejecutar test 2
$res2 = testObtenerPorEmail($db, $emailTest);
if ($res2[0]) {
    $idCreado = $res2[1];
    $resultados[] = ['testObtenerPorEmail', true, ''];
} else {
    $resultados[] = ['testObtenerPorEmail', false, $res2[1]];
}

// Ejecutar test 3
if ($idCreado !== null) {
    $res3 = testObtenerPorId($db, $idCreado);
    $resultados[] = ['testObtenerPorId', $res3[0], $res3[1]];
} else {
    $resultados[] = ['testObtenerPorId', false, 'no se pudo ejecutar porque fallo el test anterior'];
}

// Ejecutar test 4
$res4 = testEmailNoExistente($db);
$resultados[] = ['testEmailNoExistente', $res4[0], $res4[1]];

// Limpiar el usuario de prueba
if ($idCreado !== null) {
    $sql = "DELETE FROM usuarios WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $idCreado, PDO::PARAM_INT);
    $stmt->execute();
}

$fallos = 0;
foreach ($resultados as $r) {
    if ($r[1]) {
        echo "  OK: " . $r[0] . "\n";
    } else {
        echo "  FALLO: " . $r[0] . " -> " . $r[2] . "\n";
        $fallos++;
    }
}
echo "\nResultado: " . (count($resultados) - $fallos) . "/" . count($resultados) . " tests pasados\n";
exit($fallos > 0 ? 1 : 0);


