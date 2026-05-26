<?php
// modelo para gestionar las operaciones de usuarios en la base de datos

class UsuarioModel {
    private $conexion;

    // constructor que recibe la conexion PDO de la base de datos
    public function __construct($db) {
        $this->conexion = $db;
    }

    // metodo para buscar un usuario a traves de su correo electronico
    public function obtenerPorEmail($email) {
        $sql = "SELECT id, nombre, email, password, telefono, rol FROM usuarios WHERE email = :email LIMIT 1";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        // devuelve el usuario como array asociativo o false si no existe
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // metodo para buscar un usuario por su ID de clave primaria
    public function obtenerPorId($id) {
        $sql = "SELECT id, nombre, email, password, telefono, rol FROM usuarios WHERE id = :id LIMIT 1";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
        // metodo para registrar un nuevo usuario en la base de datos (por defecto rol cliente)
    public function crear($nombre, $email, $password, $telefono, $rol = 'cliente') {
        $sql = "INSERT INTO usuarios (nombre, email, password, telefono, rol) 
                VALUES (:nombre, :email, :password, :telefono, :rol)";
        
        $stmt = $this->conexion->prepare($sql);
        
        // enlazamos los parametros de forma segura para prevenir inyecciones SQL
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':rol', $rol);
        
        return $stmt->execute();
    }
}

