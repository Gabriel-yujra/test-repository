-- ============================================
-- SISTEMA DE GESTIÓN DE INVENTARIOS
-- Material de Construcción
-- Compatible con MySQL / MariaDB
-- ============================================

CREATE DATABASE IF NOT EXISTS inventario_material_construccion;
USE inventario_material_construccion;

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------
-- 1. TABLAS DE SEGURIDAD Y CATÁLOGOS
-- ---------------------------------------------------------

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) UNIQUE,
  descripcion TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
  contrasenia_hash VARCHAR(255) NOT NULL,
  nombre_completo VARCHAR(100),
  rol_id INT NOT NULL,
  estado ENUM('activo','inactivo') DEFAULT 'activo',
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50),
  descripcion TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE unidades (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(20),
  abreviacion VARCHAR(5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 2. GESTIÓN DE MATERIALES
-- ---------------------------------------------------------

CREATE TABLE materiales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(20) UNIQUE,
  nombre VARCHAR(100) NOT NULL,
  marca VARCHAR(50),
  categoria_id INT,
  unidad_base_id INT,
  es_perecedero TINYINT(1) DEFAULT 0,
  alerta_stock_minimo INT,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id),
  FOREIGN KEY (unidad_base_id) REFERENCES unidades(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE conversiones_unidades (
  id INT AUTO_INCREMENT PRIMARY KEY,
  material_id INT NOT NULL,
  unidad_origen_id INT NOT NULL,
  unidad_destino_id INT NOT NULL,
  factor DECIMAL(10,4) NOT NULL,
  FOREIGN KEY (material_id) REFERENCES materiales(id),
  FOREIGN KEY (unidad_origen_id) REFERENCES unidades(id),
  FOREIGN KEY (unidad_destino_id) REFERENCES unidades(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 3. INFRAESTRUCTURA
-- ---------------------------------------------------------

CREATE TABLE almacenes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  tipo ENUM('central','obra'),
  direccion VARCHAR(200),
  zona ENUM('sur','centro','norte','el_alto','laderas'),
  usuario_responsable_id INT,
  FOREIGN KEY (usuario_responsable_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE proyectos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(20) UNIQUE,
  nombre VARCHAR(100),
  direccion VARCHAR(200),
  residente_usuario_id INT,
  estado ENUM('activo','pausado','finalizado'),
  presupuesto_limite DECIMAL(15,2),
  almacen_id INT,
  FOREIGN KEY (residente_usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (almacen_id) REFERENCES almacenes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 4. INVENTARIO Y LOTES
-- ---------------------------------------------------------

CREATE TABLE inventario_stock (
  id INT AUTO_INCREMENT PRIMARY KEY,
  almacen_id INT NOT NULL,
  material_id INT NOT NULL,
  cantidad DECIMAL(12,2) DEFAULT 0,
  costo_promedio DECIMAL(12,2) DEFAULT 0,
  ultima_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_inventario (almacen_id, material_id),
  FOREIGN KEY (almacen_id) REFERENCES almacenes(id),
  FOREIGN KEY (material_id) REFERENCES materiales(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE lotes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  material_id INT NOT NULL,
  codigo_lote VARCHAR(50),
  fecha_vencimiento DATE,
  cantidad_restante DECIMAL(12,2),
  FOREIGN KEY (material_id) REFERENCES materiales(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 5. MOVIMIENTOS
-- ---------------------------------------------------------

CREATE TABLE movimientos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  almacen_id INT NOT NULL,
  proyecto_id INT NULL,
  usuario_id INT NOT NULL,
  tipo ENUM('compra','transferencia_entrada','transferencia_salida','consumo_obra','ajuste_positivo','ajuste_negativo','devolucion'),
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  documento_referencia VARCHAR(50),
  comentarios TEXT,
  FOREIGN KEY (almacen_id) REFERENCES almacenes(id),
  FOREIGN KEY (proyecto_id) REFERENCES proyectos(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE detalle_movimientos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  movimiento_id INT NOT NULL,
  material_id INT NOT NULL,
  cantidad DECIMAL(12,2) NOT NULL,
  costo_unitario DECIMAL(12,4),
  costo_total DECIMAL(15,2),
  lote_id INT NULL,
  FOREIGN KEY (movimiento_id) REFERENCES movimientos(id),
  FOREIGN KEY (material_id) REFERENCES materiales(id),
  FOREIGN KEY (lote_id) REFERENCES lotes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 6. TRANSFERENCIAS
-- ---------------------------------------------------------

CREATE TABLE transferencias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  almacen_origen_id INT NOT NULL,
  almacen_destino_id INT NOT NULL,
  estado ENUM('pendiente','en_transito','recibido','rechazado'),
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_recepcion TIMESTAMP NULL,
  descripcion TEXT,
  FOREIGN KEY (almacen_origen_id) REFERENCES almacenes(id),
  FOREIGN KEY (almacen_destino_id) REFERENCES almacenes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE transferencia_detalle (
  id INT AUTO_INCREMENT PRIMARY KEY,
  transferencia_id INT NOT NULL,
  material_id INT NOT NULL,
  cantidad DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (transferencia_id) REFERENCES transferencias(id),
  FOREIGN KEY (material_id) REFERENCES materiales(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 7. PROVEEDORES Y COMPRAS
-- ---------------------------------------------------------

CREATE TABLE proveedores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  razon_social VARCHAR(150),
  nit VARCHAR(20),
  telefono VARCHAR(20),
  es_formal TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ordenes_compra (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(20) UNIQUE,
  proveedor_id INT NOT NULL,
  estado ENUM('borrador','aprobada','recepcionada','cancelada'),
  monto_total DECIMAL(15,2),
  usuario_aprobacion_id INT,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
  FOREIGN KEY (usuario_aprobacion_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE detalle_ordenes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  orden_compra_id INT NOT NULL,
  material_id INT NOT NULL,
  cantidad_solicitada DECIMAL(12,2),
  precio_pactado DECIMAL(12,2),
  FOREIGN KEY (orden_compra_id) REFERENCES ordenes_compra(id),
  FOREIGN KEY (material_id) REFERENCES materiales(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE facturas_compra (
  id INT AUTO_INCREMENT PRIMARY KEY,
  orden_compra_id INT NOT NULL,
  proveedor_id INT NOT NULL,
  numero_factura VARCHAR(50),
  codigo_autorizacion VARCHAR(100),
  cuf VARCHAR(255),
  codigo_control VARCHAR(50),
  fecha_factura DATE,
  monto_total DECIMAL(15,2),
  credito_fiscal DECIMAL(15,2),
  pdf_url VARCHAR(255),
  aplica_retencion TINYINT(1) DEFAULT 0,
  FOREIGN KEY (orden_compra_id) REFERENCES ordenes_compra(id),
  FOREIGN KEY (proveedor_id) REFERENCES proveedores(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- 8. AUDITORÍA
-- ---------------------------------------------------------

CREATE TABLE registro_auditoria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NULL,
  tabla VARCHAR(100),
  operacion VARCHAR(20),
  fecha DATE,
  hora TIME,
  cambios_json JSON,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------
-- DATOS INICIALES
-- ---------------------------------------------------------

-- Roles
INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Acceso total al sistema'),
('Almacenero', 'Registro de entradas y salidas'),
('Residente', 'Solo consulta y solicitud');

-- Unidades de medida
INSERT INTO unidades (nombre, abreviacion) VALUES
('Pieza', 'PZA'),
('Kilogramo', 'KG'),
('Metro Cúbico', 'M3'),
('Bolsa', 'BOLSA'),
('Metro Lineal', 'ML');

-- Categorías de ejemplo
INSERT INTO categorias (nombre, descripcion) VALUES
('Obra Gruesa', 'Materiales para obra gruesa'),
('Cementos', 'Cementos y mezclas'),
('Herramientas', 'Herramientas de construcción'),
('Eléctricas', 'Herramientas eléctricas'),
('Pinturas', 'Pinturas y acabados'),
('Aceros', 'Aceros y estructuras metálicas');

-- Usuario administrador (contraseña: admin123)
INSERT INTO usuarios (nombre_usuario, contrasenia_hash, nombre_completo, rol_id) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador del Sistema', 1);

-- Almacén central
INSERT INTO almacenes (nombre, tipo, zona) VALUES
('Almacén Central', 'central', 'centro');

