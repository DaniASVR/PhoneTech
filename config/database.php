<?php
// config/database.php

class Database {
    // Credenciales de la base de datos por defecto (Docker)
    private $host = "db";
    private $db_name = "phonetech_db";
    private $username = "phonetech";
    private $password = "phonetech_pass";
    private $conn;

    // Metodo para obtener la conexion con la base de datos usando PDO
    public function obtenerConexion() {
        $this->conn = null;

        try {
            // Intentar leer de variables de entorno si estan configuradas, si no usar las de la clase
            $host = getenv('DB_HOST');
            if ($host === false || $host === '') {
                $host = $this->host;
            }

            $db_name = getenv('DB_NAME');
            if ($db_name === false || $db_name === '') {
                $db_name = $this->db_name;
            }

            $username = getenv('DB_USER');
            if ($username === false || $username === '') {
                $username = $this->username;
            }

            $password = getenv('DB_PASSWORD');
            if ($password === false || $password === '') {
                $password = $this->password;
            }

            // Cadena de conexion para MySQL con UTF-8
            $connString = "mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8";
            
            $this->conn = new PDO($connString, $username, $password);
            
            // Configurar PDO para lanzar excepciones cuando ocurra un error
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch (PDOException $exception) {
            // Si la conexion falla, lanzamos una excepcion
            throw new Exception("Error al conectar a la base de datos: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
