<?php
// tests de integracion que comprueban flujos completos entre modulos

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/UsuarioModel.php';
require_once __DIR__ . '/../src/Models/ServicioModel.php';
require_once __DIR__ . '/../src/Models/CitaModel.php';

function testFlujoRegistroYLogin($db, $emailTest, $passPlano) {
    $usuarioModel = new UsuarioModel($db);
    $passHash = password_hash($passPlano, PASSWORD_BCRYPT);
    $usuarioModel->crear('Integracion Test', $emailTest, $passHash, '600999888', 'cliente');
    $usuario = $usuarioModel->obtenerPorEmail($emailTest);

    if ($usuario && password_verify($passPlano, $usuario['password'])) {
        return [true, $usuario['id']];
    }
    return [false, 'el flujo de registro y verificacion de contraseña fallo'];
}

function testFlujoCitaConUsuario($db, $idUsuario, $emailTest) {
    $citaModel = new CitaModel($db);
    $fechaTest = date('Y-m-d H:i:s', strtotime('+45 days 11:00'));

    $datosCita = [
        'usuario_id' => $idUsuario,
        'nombre_noregistrado' => 'Integracion Test',
        'telefono_noregistrado' => '600999888',
        'email_noregistrado' => $emailTest,
        'dispositivo_modelo' => 'Pixel 8 Test',
        'servicio_id' => 1,
        'fecha_hora' => $fechaTest,
        'comentarios' => 'Cita de integracion'
    ];

    $citaModel->crear($datosCita);
    $idCita = $db->lastInsertId();

    $todasCitas = $citaModel->obtenerTodas();
    $encontrada = false;
    foreach ($todasCitas as $c) {
        if (intval($c['id']) === intval($idCita)) {
            if ($c['usuario_nombre'] === 'Integracion Test') {
                $encontrada = true;
            }
            break;
        }
    }

    if ($encontrada) {
        return [true, $idCita];
    }
    return [false, 'la cita no trae los datos del usuario relacionado'];
}

function testFlujoCitaInvitado($db) {
    $citaModel = new CitaModel($db);
    $fechaInvitado = date('Y-m-d H:i:s', strtotime('+46 days 12:00'));
    $datosInvitado = [
        'usuario_id' => null,
        'nombre_noregistrado' => 'Invitado Integracion',
        'telefono_noregistrado' => '611000111',
        'email_noregistrado' => 'invitado_test@correo.com',
        'dispositivo_modelo' => 'Xiaomi Test',
        'servicio_id' => 2,
        'fecha_hora' => $fechaInvitado,
        'comentarios' => ''
    ];

    $citaModel->crear($datosInvitado);
    $idCitaInvitado = $db->lastInsertId();
    $citaInv = $citaModel->obtenerPorId($idCitaInvitado);

    if ($citaInv && $citaInv['usuario_id'] === null && $citaInv['nombre_noregistrado'] === 'Invitado Integracion') {
        return [true, $idCitaInvitado];
    }
    return [false, 'los datos del invitado no se guardaron bien'];
}

function testFlujoCompletoCita($db) {
    $citaModel = new CitaModel($db);
    $fechaCiclo = date('Y-m-d H:i:s', strtotime('+47 days 14:00'));
    $datosCiclo = [
        'usuario_id' => null,
        'nombre_noregistrado' => 'Ciclo Test',
        'telefono_noregistrado' => '622333444',
        'email_noregistrado' => 'ciclo@correo.com',
        'dispositivo_modelo' => 'OnePlus Test',
        'servicio_id' => 3,
        'fecha_hora' => $fechaCiclo,
        'comentarios' => 'test del ciclo de vida'
    ];

    $citaModel->crear($datosCiclo);
    $idCiclo = $db->lastInsertId();
    $cicloOk = true;
    $cicloError = '';

    $cita = $citaModel->obtenerPorId($idCiclo);
    if (!$cita || $cita['estado'] !== 'pendiente') {
        $cicloOk = false;
        $cicloError = 'el estado inicial no es pendiente';
    }

    if ($cicloOk) {
        $citaModel->actualizarEstado($idCiclo, 'completada');
        $cita = $citaModel->obtenerPorId($idCiclo);
        if (!$cita || $cita['estado'] !== 'completada') {
            $cicloOk = false;
            $cicloError = 'no se pudo cambiar a completada';
        }
    }

    if ($cicloOk) {
        $citaModel->actualizarEstado($idCiclo, 'cancelada');
        $cita = $citaModel->obtenerPorId($idCiclo);
        if (!$cita || $cita['estado'] !== 'cancelada') {
            $cicloOk = false;
            $cicloError = 'no se pudo cambiar a cancelada';
        }
    }

    if ($cicloOk) {
        $citaModel->eliminar($idCiclo);
        $cita = $citaModel->obtenerPorId($idCiclo);
        if ($cita !== false) {
            $cicloOk = false;
            $cicloError = 'la cita no se elimino';
        }
    }

    return $cicloOk ? [true, ''] : [false, $cicloError];
}

function testCatalogoServiciosCita($db) {
    $servicioModel = new ServicioModel($db);
    $servicios = $servicioModel->obtenerTodos();
    $servicioPantalla = null;
    foreach ($servicios as $s) {
        if (intval($s['id']) === 1) {
            $servicioPantalla = $s;
            break;
        }
    }

    if ($servicioPantalla && $servicioPantalla['nombre'] === 'Cambio de Pantalla') {
        return [true, ''];
    }
    return [false, 'el servicio con id=1 no coincide con el catalogo'];
}

// lanzar los tests al ejecutar el archivo
$database = new Database();
$db = $database->obtenerConexion();

echo "-- Tests de integracion --\n";

$emailTest = 'integracion_' . time() . '@phonetech.es';
$passPlano = 'clave123';
$resultados = [];

$idUsuario = null;
$idCita = null;
$idCitaInvitado = null;

// Ejecutar test 1
$res1 = testFlujoRegistroYLogin($db, $emailTest, $passPlano);
if ($res1[0]) {
    $idUsuario = $res1[1];
    $resultados[] = ['testFlujoRegistroYLogin', true, ''];
} else {
    $resultados[] = ['testFlujoRegistroYLogin', false, $res1[1]];
}

// Ejecutar test 2
if ($idUsuario !== null) {
    $res2 = testFlujoCitaConUsuario($db, $idUsuario, $emailTest);
    if ($res2[0]) {
        $idCita = $res2[1];
        $resultados[] = ['testFlujoCitaConUsuario', true, ''];
    } else {
        $resultados[] = ['testFlujoCitaConUsuario', false, $res2[1]];
    }
} else {
    $resultados[] = ['testFlujoCitaConUsuario', false, 'no se pudo ejecutar porque fallo el registro del usuario'];
}

// Ejecutar test 3
$res3 = testFlujoCitaInvitado($db);
if ($res3[0]) {
    $idCitaInvitado = $res3[1];
    $resultados[] = ['testFlujoCitaInvitado', true, ''];
} else {
    $resultados[] = ['testFlujoCitaInvitado', false, $res3[1]];
}

// Ejecutar test 4
$res4 = testFlujoCompletoCita($db);
$resultados[] = ['testFlujoCompletoCita', $res4[0], $res4[1]];

// Ejecutar test 5
$res5 = testCatalogoServiciosCita($db);
$resultados[] = ['testCatalogoServiciosCita', $res5[0], $res5[1]];

// Limpieza de datos de prueba
$citaModel = new CitaModel($db);
if ($idCita !== null) {
    $citaModel->eliminar($idCita);
}
if ($idCitaInvitado !== null) {
    $citaModel->eliminar($idCitaInvitado);
}
if ($idUsuario !== null) {
    $sql = "DELETE FROM usuarios WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
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



