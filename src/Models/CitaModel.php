<?php
// modelo para gestionar las citas en la base de datos

class CitaModel {
    private $conexion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    // metodo para registrar una nueva cita en la base de datos
    public function crear($datos) {
        $sql = "INSERT INTO citas (
                    usuario_id, 
                    nombre_noregistrado, 
                    telefono_noregistrado, 
                    email_noregistrado, 
                    dispositivo_modelo, 
                    servicio_id, 
                    fecha_hora, 
                    comentarios,
                    estado
                ) VALUES (
                    :usuario_id, 
                    :nombre_noregistrado, 
                    :telefono_noregistrado, 
                    :email_noregistrado, 
                    :dispositivo_modelo, 
                    :servicio_id, 
                    :fecha_hora, 
                    :comentarios,
                    'pendiente'
                )";

        $stmt = $this->conexion->prepare($sql);

        // asociar usuario_id. Si es null, lo guardamos como NULL en la base de datos
        if ($datos['usuario_id'] === null) {
            $stmt->bindValue(':usuario_id', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':usuario_id', $datos['usuario_id'], PDO::PARAM_INT);
        }
        $stmt->bindParam(':nombre_noregistrado', $datos['nombre_noregistrado']);
        $stmt->bindParam(':telefono_noregistrado', $datos['telefono_noregistrado']);
        $stmt->bindParam(':email_noregistrado', $datos['email_noregistrado']);
        $stmt->bindParam(':dispositivo_modelo', $datos['dispositivo_modelo']);
        $stmt->bindParam(':servicio_id', $datos['servicio_id'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_hora', $datos['fecha_hora']);
        $stmt->bindParam(':comentarios', $datos['comentarios']);

        return $stmt->execute();
    }

    // metodo para comprobar si una fecha y hora de cita está disponible
    public function comprobarDisponibilidad($fechaHora) {
        $sql = "SELECT COUNT(*) FROM citas WHERE fecha_hora = :fecha_hora AND estado != 'cancelada'";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':fecha_hora', $fechaHora);
        $stmt->execute();
        
        // si el conteo es 0, significa que la hora esta libre
        return intval($stmt->fetchColumn()) === 0;
    }

    // metodo para obtener el listado completo de citas con los datos del cliente y servicio correspondientes
    public function obtenerTodas() {
        $sql = "SELECT 
                    c.id, 
                    c.usuario_id,
                    u.nombre AS usuario_nombre,
                    u.email AS usuario_email,
                    u.telefono AS usuario_telefono,
                    c.nombre_noregistrado,
                    c.email_noregistrado,
                    c.telefono_noregistrado,
                    c.dispositivo_modelo,
                    c.comentarios,
                    c.servicio_id,
                    s.nombre AS servicio_nombre,
                    s.precio AS servicio_precio,
                    c.fecha_hora,
                    c.estado,
                    c.created_at
                FROM citas c
                INNER JOIN servicios s ON c.servicio_id = s.id
                LEFT JOIN usuarios u ON c.usuario_id = u.id
                ORDER BY c.fecha_hora DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // metodo para actualizar el estado de una cita
    public function actualizarEstado($id, $estado) {
        $sql = "UPDATE citas SET estado = :estado WHERE id = :id";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // metodo para obtener una cita especifica por su ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM citas WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // metodo para actualizar los datos editables de una cita
    public function actualizar($id, $datos) {
        $sql = "UPDATE citas SET 
                    nombre_noregistrado = :nombre,
                    telefono_noregistrado = :telefono,
                    email_noregistrado = :email,
                    dispositivo_modelo = :dispositivo,
                    servicio_id = :servicio_id,
                    fecha_hora = :fecha_hora,
                    comentarios = :comentarios
                WHERE id = :id";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $datos['nombre_noregistrado']);
        $stmt->bindParam(':telefono', $datos['telefono_noregistrado']);
        $stmt->bindParam(':email', $datos['email_noregistrado']);
        $stmt->bindParam(':dispositivo', $datos['dispositivo_modelo']);
        $stmt->bindParam(':servicio_id', $datos['servicio_id'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_hora', $datos['fecha_hora']);
        $stmt->bindParam(':comentarios', $datos['comentarios']);
        
        return $stmt->execute();
    }

    // metodo para eliminar fisicamente una cita de la base de datos
    public function eliminar($id) {
        $sql = "DELETE FROM citas WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

