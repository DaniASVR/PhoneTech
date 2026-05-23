<?php
// modelo que obtiene todos los servicios existentes en la BBDD

class ServicioModel {
    private $conexion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    // metodo para obtener todos los servicios de la base de datos
    public function obtenerTodos() {
        $sql = "SELECT nombre, descripcion, precio, imagen FROM servicios ORDER BY id ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
