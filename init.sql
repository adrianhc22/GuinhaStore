-- ============================================================
-- Proyecto Final: GuinhaStore (tienda de videojuegos PS5)
-- Diseño de la base de datos
-- ============================================================

USE guinahstore;

-- ------------------------------------------------------------
-- PRODUCTOS (juegos de PS5)
-- ------------------------------------------------------------
CREATE TABLE productos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    foto MEDIUMBLOB,
    precio DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock INT(11) NOT NULL DEFAULT 0,
    fabricante VARCHAR(100),
    origen VARCHAR(100),
    trailer VARCHAR(30),
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- USUARIOS
-- ------------------------------------------------------------
CREATE TABLE usuarios (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    fecha_nacimiento DATE,
    num_tarjeta VARCHAR(20),
    direccion_postal VARCHAR(200),
    rol ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente',
    PRIMARY KEY (id),
    UNIQUE KEY uq_correo (correo)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- CARRITO DE COMPRAS
-- ------------------------------------------------------------
CREATE TABLE carrito (
    id INT(11) NOT NULL AUTO_INCREMENT,
    id_usuario INT(11) NOT NULL,
    id_producto INT(11) NOT NULL,
    cantidad INT(11) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    KEY idx_carrito_usuario (id_usuario),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- HISTORIAL DE COMPRAS
-- ------------------------------------------------------------
CREATE TABLE historial (
    id INT(11) NOT NULL AUTO_INCREMENT,
    id_orden INT(11) NOT NULL,
    id_usuario INT(11) NOT NULL,
    id_producto INT(11) NOT NULL,
    cantidad INT(11) NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL DEFAULT 0,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_hist_usuario (id_usuario),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB;
