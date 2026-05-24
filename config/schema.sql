-- Crear la tabla de servicios
CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    imagen VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear la tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    rol ENUM('cliente', 'admin') DEFAULT 'cliente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear la tabla de citas
CREATE TABLE IF NOT EXISTS citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    nombre_noregistrado VARCHAR(100) NULL,
    telefono_noregistrado VARCHAR(20) NULL,
    email_noregistrado VARCHAR(150) NULL,
    dispositivo_modelo VARCHAR(100) NOT NULL,
    servicio_id INT NOT NULL,
    fecha_hora DATETIME NOT NULL,
    direccion_facturacion TEXT NULL,
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar servicios por defecto
INSERT INTO servicios (id, nombre, descripcion, precio, imagen) VALUES 
(1, 'Cambio de Pantalla', 'Reparación completa del panel táctil y cristal.', 129.99, '/images/screen_repair.png'),
(2, 'Cambio de Batería', 'Sustitución de batería degradada por una nueva.', 59.99, '/images/battery_repair.png'),
(3, 'Conector de Carga', 'Reparación o cambio del puerto de carga Lightning.', 49.99, '/images/connector_repair.png')
ON DUPLICATE KEY UPDATE 
nombre = VALUES(nombre),
descripcion = VALUES(descripcion), 
precio = VALUES(precio), 
imagen = VALUES(imagen);
