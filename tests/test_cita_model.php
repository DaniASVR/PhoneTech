<?php
// tests unitarios para el modelo de citas

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/CitaModel.php';

function testCrearCita($db, $fechaPrueba) {
    $modelo = new CitaModel($db);
    $datosCita = [
        'usuario_id' => null,
        'nombre_noregistrado' => 'Cliente Test',
        'telefono_noregistrado' => '611222333',
        'email_noregistrado' => 'test_cita@correo.com',
        'dispositivo_modelo' => 'iPhone 14 Test',
        'servicio_id' => 1,
        'fecha_hora' => $fechaPrueba,
        'comentarios' => 'Cita de prueba unitaria'
    ];

    $resultado = $modelo->crear($datosCita);
    if ($resultado) {
        $idCitaCreada = $db->lastInsertId();
        return [true, $idCitaCreada];
    }
    return [false, 'no se pudo insertar la cita'];
}

function testObtenerPorId($db, $idCitaCreada) {
    $modelo = new CitaModel($db);
    $cita = $modelo->obtenerPorId($idCitaCreada);
    if ($cita && $cita['dispositivo_modelo'] === 'iPhone 14 Test') {
        return [true, ''];
    }
    return [false, 'la cita no se encontro o los datos no coinciden'];
}

function testDisponibilidadOcupada($db, $fechaPrueba) {
    $modelo = new CitaModel($db);
    $disponible = $modelo->comprobarDisponibilidad($fechaPrueba);
    if ($disponible === false) {
        return [true, ''];
    }
    return [false, 'la hora deberia estar ocupada pero dice que esta libre'];
}

function testDisponibilidadLibre($db) {
    $modelo = new CitaModel($db);
    $fechaLibre = date('Y-m-d H:i:s', strtotime('+60 days 15:00'));
    $disponible = $modelo->comprobarDisponibilidad($fechaLibre);
    if ($disponible === true) {
        return [true, ''];
    }
    return [false, 'la hora deberia estar libre'];
}

function testActualizarEstado($db, $idCitaCreada) {
    $modelo = new CitaModel($db);
    $modelo->actualizarEstado($idCitaCreada, 'completada');
    $cita = $modelo->obtenerPorId($idCitaCreada);
    if ($cita && $cita['estado'] === 'completada') {
        return [true, ''];
    }
    return [false, 'el estado no se actualizo correctamente'];
}

function testActualizar($db, $idCitaCreada, $fechaPrueba) {
    $modelo = new CitaModel($db);
    $datosNuevos = [
        'nombre_noregistrado' => 'Cliente Modificado',
        'telefono_noregistrado' => '699888777',
        'email_noregistrado' => 'modificado@correo.com',
        'dispositivo_modelo' => 'Samsung S24 Test',
        'servicio_id' => 2,
        'fecha_hora' => $fechaPrueba,
        'comentarios' => 'Datos modificados en test'
    ];
    $modelo->actualizar($idCitaCreada, $datosNuevos);
    $cita = $modelo->obtenerPorId($idCitaCreada);
    if ($cita && $cita['dispositivo_modelo'] === 'Samsung S24 Test') {
        return [true, ''];
    }
    return [false, 'los datos no se actualizaron'];
}

function testObtenerTodas($db) {
    $modelo = new CitaModel($db);
    $todas = $modelo->obtenerTodas();
    if (is_array($todas) && count($todas) > 0) {
        return [true, ''];
    }
    return [false, 'no se obtuvieron citas'];
}

function testEliminar($db, $idCitaCreada) {
    $modelo = new CitaModel($db);
    $modelo->eliminar($idCitaCreada);
    $cita = $modelo->obtenerPorId($idCitaCreada);
    if ($cita === false) {
        return [true, ''];
    }
    return [false, 'la cita no se elimino correctamente'];
}

// lanzar los tests al ejecutar el archivo
$database = new Database();
$db = $database->obtenerConexion();

echo "-- Tests unitarios: CitaModel --\n";

$fechaPrueba = date('Y-m-d H:i:s', strtotime('+30 days 10:00'));
$resultados = [];
$idCitaCreada = null;

// Ejecutar test 1
$res1 = testCrearCita($db, $fechaPrueba);
if ($res1[0]) {
    $idCitaCreada = $res1[1];
    $resultados[] = ['testCrearCita', true, ''];
} else {
    $resultados[] = ['testCrearCita', false, $res1[1]];
}

// Ejecutar test 2
if ($idCitaCreada !== null) {
    $res2 = testObtenerPorId($db, $idCitaCreada);
    $resultados[] = ['testObtenerPorId', $res2[0], $res2[1]];
} else {
    $resultados[] = ['testObtenerPorId', false, 'no se pudo ejecutar porque fallo el test anterior'];
}

// Ejecutar test 3
$res3 = testDisponibilidadOcupada($db, $fechaPrueba);
$resultados[] = ['testDisponibilidadOcupada', $res3[0], $res3[1]];

// Ejecutar test 4
$res4 = testDisponibilidadLibre($db);
$resultados[] = ['testDisponibilidadLibre', $res4[0], $res4[1]];

// Ejecutar test 5
if ($idCitaCreada !== null) {
    $res5 = testActualizarEstado($db, $idCitaCreada);
    $resultados[] = ['testActualizarEstado', $res5[0], $res5[1]];
} else {
    $resultados[] = ['testActualizarEstado', false, 'no se pudo ejecutar porque fallo el test anterior'];
}

// Ejecutar test 6
if ($idCitaCreada !== null) {
    $res6 = testActualizar($db, $idCitaCreada, $fechaPrueba);
    $resultados[] = ['testActualizar', $res6[0], $res6[1]];
} else {
    $resultados[] = ['testActualizar', false, 'no se pudo ejecutar porque fallo el test anterior'];
}

// Ejecutar test 7
$res7 = testObtenerTodas($db);
$resultados[] = ['testObtenerTodas', $res7[0], $res7[1]];

// Ejecutar test 8
if ($idCitaCreada !== null) {
    $res8 = testEliminar($db, $idCitaCreada);
    $resultados[] = ['testEliminar', $res8[0], $res8[1]];
} else {
    $resultados[] = ['testEliminar', false, 'no se pudo ejecutar porque fallo el test anterior'];
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



