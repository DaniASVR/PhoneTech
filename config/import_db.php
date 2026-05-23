<?php
require_once __DIR__ . '/database.php';

try {
    // Instancia la base de datos y obtener la conexion PDO
    $database = new Database();
    $db = $database->obtenerConexion();

    // Lee el contenido del archivo schema.sql
    $sql = file_get_contents(__DIR__ . '/schema.sql');

    // Ejecuta el SQL completo en el servidor de base de datos
    $db->exec($sql);

    echo "Base de datos y tablas creadas con éxito!\n";
} catch (Exception $e) {
    echo "Error al importar la base de datos: " . $e->getMessage() . "\n";
}
