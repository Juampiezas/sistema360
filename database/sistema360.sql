CREATE DATABASE sistema360;

USE sistema360;

-- TABLA USUARIOS

CREATE TABLE usuarios(
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL,
    clave VARCHAR(255) NOT NULL,
    rol VARCHAR(20)
);

-- TABLA CLIENTES

CREATE TABLE clientes(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    telefono VARCHAR(20),
    direccion TEXT
);

-- TABLA PROVEEDORES

CREATE TABLE proveedores(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    empresa VARCHAR(100),
    telefono VARCHAR(20)
);

-- TABLA PRODUCTOS

CREATE TABLE productos(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    precio DECIMAL(10,2),
    stock INT,
    categoria VARCHAR(50)
);

-- TABLA VENTAS

CREATE TABLE ventas(
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2),

    FOREIGN KEY(cliente_id)
    REFERENCES clientes(id)
);

-- DETALLE VENTAS

CREATE TABLE detalle_ventas(
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT,
    producto_id INT,
    cantidad INT,
    subtotal DECIMAL(10,2),

    FOREIGN KEY(venta_id)
    REFERENCES ventas(id),

    FOREIGN KEY(producto_id)
    REFERENCES productos(id)
);

-- USUARIO ADMIN

INSERT INTO usuarios(usuario, clave, rol)
VALUES(
    'admin',
    MD5('1234'),
    'Administrador'
);