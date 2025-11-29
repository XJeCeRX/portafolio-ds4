-- =====================================================
-- BASE DE DATOS: CHINOS CAFE
-- Sistema POS Web
-- =====================================================

CREATE DATABASE IF NOT EXISTS chinos_cafe CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE chinos_cafe;

-- =====================================================
-- TABLA: proveedores
-- =====================================================
CREATE TABLE IF NOT EXISTS proveedores (
  id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  contacto VARCHAR(100) NOT NULL,
  telefono VARCHAR(20),
  email VARCHAR(100),
  direccion TEXT,
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLA: inventario
-- =====================================================
CREATE TABLE IF NOT EXISTS inventario (
  id_producto INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT,
  precio_compra DECIMAL(10,2) NOT NULL,
  precio_venta DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  unidad VARCHAR(20) DEFAULT 'unidad',
  categoria VARCHAR(50),
  id_proveedor INT,
  fecha_ingreso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLA: ventas
-- =====================================================
CREATE TABLE IF NOT EXISTS ventas (
  id_venta INT AUTO_INCREMENT PRIMARY KEY,
  numero_factura VARCHAR(50) UNIQUE NOT NULL,
  cliente VARCHAR(100),
  total DECIMAL(10,2) NOT NULL,
  tipo_pago VARCHAR(20) DEFAULT 'efectivo',
  fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  usuario VARCHAR(50),
  estado VARCHAR(20) DEFAULT 'completada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLA: detalle_venta
-- =====================================================
CREATE TABLE IF NOT EXISTS detalle_venta (
  id_detalle INT AUTO_INCREMENT PRIMARY KEY,
  id_venta INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad INT NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (id_venta) REFERENCES ventas(id_venta) ON DELETE CASCADE,
  FOREIGN KEY (id_producto) REFERENCES inventario(id_producto) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLA: contactos
-- =====================================================
CREATE TABLE IF NOT EXISTS contactos (
  id_contacto INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(100) NOT NULL,
  mensaje TEXT NOT NULL,
  fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  estado VARCHAR(20) DEFAULT 'nuevo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- ÍNDICES PARA OPTIMIZACIÓN
-- =====================================================
CREATE INDEX idx_ventas_fecha ON ventas(fecha_venta);
CREATE INDEX idx_ventas_factura ON ventas(numero_factura);
CREATE INDEX idx_inventario_stock ON inventario(stock);
CREATE INDEX idx_inventario_categoria ON inventario(categoria);
CREATE INDEX idx_detalle_venta ON detalle_venta(id_venta);

-- =====================================================
-- DATOS DE EJEMPLO
-- =====================================================

-- Proveedores
INSERT INTO proveedores (nombre, contacto, telefono, email, direccion) VALUES
('Café Premium Co.', 'Juan Pérez', '6000-1234', 'juan@cafepremium.com', 'Calle 1, David'),
('Bebidas ABC', 'María García', '6000-5678', 'maria@bebidasabc.com', 'Av. Principal, David');

-- Inventario
INSERT INTO inventario (nombre, descripcion, precio_compra, precio_venta, stock, unidad, categoria, id_proveedor) VALUES
('Café Expresso', 'Café expresso de alta calidad', 2.50, 4.00, 100, 'taza', 'Bebidas', 1),
('Café Americano', 'Café americano tradicional', 1.80, 3.00, 150, 'taza', 'Bebidas', 1),
('Café Latte', 'Café latte con leche', 2.20, 4.50, 80, 'taza', 'Bebidas', 1),
('Croissant', 'Croissant de mantequilla', 1.00, 2.50, 50, 'unidad', 'Panadería', 2),
('Torta de Chocolate', 'Deliciosa torta de chocolate', 5.00, 8.00, 20, 'porción', 'Pastelería', 2);

SELECT '✅ Base de datos creada exitosamente!' AS mensaje;


