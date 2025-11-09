-- 1. Tabla de USUARIOS
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabla de SUBASTAS
CREATE TABLE subastas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    precio_inicial DECIMAL(10,2) NOT NULL,
    precio_actual DECIMAL(10,2) NOT NULL,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_fin DATETIME NOT NULL,
    estado ENUM('activa', 'finalizada') DEFAULT 'activa',
    imagen_principal VARCHAR(255),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- 3. Tabla de OFERTAS
CREATE TABLE ofertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subasta_id INT NOT NULL,
    usuario_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subasta_id) REFERENCES subastas(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);