-- Base de datos para Turismo Tiwanaku
-- Sistema de gestión de tours y reservas

CREATE DATABASE IF NOT EXISTS tiwanaku_turismo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE tiwanaku_turismo;

-- Tabla de Tours
CREATE TABLE IF NOT EXISTS tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    duracion INT NOT NULL COMMENT 'Duración en horas',
    cupos_disponibles INT NOT NULL DEFAULT 0,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_categoria (categoria),
    INDEX idx_precio (precio),
    INDEX idx_fecha_creacion (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') DEFAULT 'usuario',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME DEFAULT NULL,
    activo TINYINT(1) DEFAULT 1,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Reservas
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tour_id INT NOT NULL,
    cliente_nombre VARCHAR(255) NOT NULL,
    cliente_email VARCHAR(255) NOT NULL,
    cliente_telefono VARCHAR(50) NOT NULL,
    cantidad_personas INT NOT NULL,
    fecha_tour DATE NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'cancelada') DEFAULT 'pendiente',
    fecha_reserva DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    INDEX idx_tour_id (tour_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha_tour (fecha_tour),
    INDEX idx_fecha_reserva (fecha_reserva)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de ejemplo - Usuarios
-- Contraseña para ambos: admin123
INSERT INTO users (username, email, password, nombre_completo, rol) VALUES
('admin', 'admin@tiwanaku.com', '$2y$10$.SKADkpFbs34ApWZ6JCD2Oidw3OD/somnizNXf7LMsl9nMR5BqHlK', 'Administrador del Sistema', 'admin'),
('usuario1', 'usuario1@tiwanaku.com', '$2y$10$.SKADkpFbs34ApWZ6JCD2Oidw3OD/somnizNXf7LMsl9nMR5BqHlK', 'Usuario de Prueba', 'usuario');

-- Datos de ejemplo - Tours
INSERT INTO tours (nombre, descripcion, categoria, precio, duracion, cupos_disponibles) VALUES
('Tour Completo Tiwanaku', 'Visita guiada completa a todos los sitios arqueológicos de Tiwanaku, incluyendo la Puerta del Sol, el Templo de Kalasasaya, y el Museo de Cerámica. Incluye guía especializado y transporte.', 'Arqueológico', 150.00, 4, 20),
('Templo de Kalasasaya', 'Recorrido detallado por el Templo de Kalasasaya, uno de los monumentos más importantes de Tiwanaku. Incluye explicación histórica y cultural.', 'Histórico', 80.00, 2, 15),
('Puerta del Sol', 'Visita al emblemático monumento de la Puerta del Sol, símbolo de la cultura Tiwanaku. Incluye guía y material informativo.', 'Cultural', 60.00, 1, 25),
('Museo de Tiwanaku', 'Recorrido por el Museo de Tiwanaku con exposición de cerámica, textiles y artefactos arqueológicos. Ideal para conocer la cultura en profundidad.', 'Cultural', 50.00, 2, 30),
('Tour Arqueológico Completo', 'Tour completo de día entero visitando todos los sitios arqueológicos, museos y centros culturales de Tiwanaku. Incluye almuerzo típico.', 'Arqueológico', 200.00, 8, 10),
('Rituales Ancestrales', 'Experiencia única participando en rituales ancestrales aymaras en Tiwanaku. Incluye ceremonia y guía espiritual.', 'Religioso', 120.00, 3, 12),
('Amanecer en Tiwanaku', 'Tour especial para ver el amanecer en Tiwanaku, momento mágico del solsticio. Incluye desayuno y guía especializado.', 'Cultural', 100.00, 3, 15),
('Arte y Artesanía Tiwanaku', 'Visita a talleres de artesanos locales que mantienen las técnicas ancestrales. Incluye demostración y posibilidad de compra.', 'Cultural', 70.00, 2, 20),
('Tour Fotográfico', 'Tour especializado para fotógrafos con acceso a áreas restringidas y mejores horarios de luz. Guía fotográfico incluido.', 'Aventura', 180.00, 5, 8),
('Tiwanaku Nocturno', 'Visita nocturna a Tiwanaku con iluminación especial y experiencias sensoriales. Incluye cena temática.', 'Cultural', 160.00, 4, 12);

-- Algunas reservas de ejemplo
INSERT INTO reservations (tour_id, cliente_nombre, cliente_email, cliente_telefono, cantidad_personas, fecha_tour, estado) VALUES
(1, 'Juan Pérez', 'juan.perez@email.com', '591-12345678', 2, DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'confirmada'),
(2, 'María González', 'maria.gonzalez@email.com', '591-87654321', 1, DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'pendiente'),
(3, 'Carlos López', 'carlos.lopez@email.com', '591-11223344', 4, DATE_ADD(CURDATE(), INTERVAL 15 DAY), 'confirmada'),
(5, 'Ana Martínez', 'ana.martinez@email.com', '591-55667788', 2, DATE_ADD(CURDATE(), INTERVAL 20 DAY), 'pendiente');

-- Vista para estadísticas
CREATE OR REPLACE VIEW vista_estadisticas AS
SELECT 
    COUNT(DISTINCT t.id) as total_tours,
    COUNT(DISTINCT r.id) as total_reservas,
    SUM(CASE WHEN r.estado = 'confirmada' THEN 1 ELSE 0 END) as reservas_confirmadas,
    SUM(CASE WHEN r.estado = 'pendiente' THEN 1 ELSE 0 END) as reservas_pendientes,
    SUM(CASE WHEN r.estado = 'cancelada' THEN 1 ELSE 0 END) as reservas_canceladas,
    AVG(t.precio) as precio_promedio_tours,
    SUM(r.cantidad_personas) as total_personas_reservadas
FROM tours t
LEFT JOIN reservations r ON t.id = r.tour_id;

-- Procedimiento almacenado para actualizar cupos
DELIMITER //
CREATE PROCEDURE actualizar_cupos_tour(
    IN p_tour_id INT,
    IN p_cantidad INT,
    IN p_operacion VARCHAR(10) -- 'reservar' o 'liberar'
)
BEGIN
    IF p_operacion = 'reservar' THEN
        UPDATE tours 
        SET cupos_disponibles = cupos_disponibles - p_cantidad 
        WHERE id = p_tour_id AND cupos_disponibles >= p_cantidad;
    ELSEIF p_operacion = 'liberar' THEN
        UPDATE tours 
        SET cupos_disponibles = cupos_disponibles + p_cantidad 
        WHERE id = p_tour_id;
    END IF;
END //
DELIMITER ;

