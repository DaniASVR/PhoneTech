<?php
// tests unitarios para el modelo de servicios

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/ServicioModel.php';

function testObtenerTodos($db) {
    $modelo = new ServicioModel($db);
    $servicios = $modelo->obtenerTodos();
    if (is_array($servicios) && count($servicios) >= 3) {
        return [true, $servicios];
    }
    return [false, 'se esperaban al menos 3 servicios'];
}

function testEstructuraServicio($servicios) {
    if (!is_array($servicios)) {
        return [false, 'no se recibieron servicios validos'];
    }
    $camposEsperados = ['id', 'nombre', 'descripcion', 'precio', 'imagen'];
    foreach ($servicios as $servicio) {
        foreach ($camposEsperados as $campo) {
            if (!isset($servicio[$campo])) {
                return [false, 'falta el campo: ' . $campo];
            }
        }
    }
    return [true, ''];
}

function testPreciosCorrectos($servicios) {
    if (!is_array($servicios)) {
        return [false, 'no se recibieron servicios validos'];
    }
    foreach ($servicios as $servicio) {
        if (!is_numeric($servicio['precio']) || floatval($servicio['precio']) <= 0) {
            return [false, 'precio invalido en: ' . $servicio['nombre'] . ' = ' . $servicio['precio']];
        }
    }
    return [true, ''];
}

// lanzar los tests al ejecutar el archivo
$database = new Database();
$db = $database->obtenerConexion();

echo "-- Tests unitarios: ServicioModel --\n";

$resultados = [];
$servicios = null;

// Ejecutar test 1
$res1 = testObtenerTodos($db);
if ($res1[0]) {
    $servicios = $res1[1];
    $resultados[] = ['testObtenerTodos', true, ''];
} else {
    $resultados[] = ['testObtenerTodos', false, $res1[1]];
}

// Ejecutar test 2
if ($servicios !== null) {
    $res2 = testEstructuraServicio($servicios);
    $resultados[] = ['testEstructuraServicio', $res2[0], $res2[1]];
} else {
    $resultados[] = ['testEstructuraServicio', false, 'no se pudo ejecutar porque fallo el test anterior'];
}

// Ejecutar test 3
if ($servicios !== null) {
    $res3 = testPreciosCorrectos($servicios);
    $resultados[] = ['testPreciosCorrectos', $res3[0], $res3[1]];
} else {
    $resultados[] = ['testPreciosCorrectos', false, 'no se pudo ejecutar porque fallo el test anterior'];
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



