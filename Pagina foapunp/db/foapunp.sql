-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS foapunp;
USE foapunp;

-- Tabla del personal (datos laborales)
CREATE TABLE personal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    dni VARCHAR(8) UNIQUE NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    tipo_personal ENUM('Docente', 'Administrativo', 'Servicios') NOT NULL,
    tipo_contrato ENUM('Nombrado', 'Contratado', 'CAS') NOT NULL,
    socio ENUM('SI', 'NO') NOT NULL DEFAULT 'NO',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de usuarios (acceso al sistema)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_id INT UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'socio', 'empleado') NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (personal_id) REFERENCES personal(id)
);

-- Tabla de documentos
CREATE TABLE documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    archivo VARCHAR(255) NOT NULL,
    personal_id INT NULL,
    prestamo_id INT NULL,
    fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personal_id) REFERENCES personal(id),
    FOREIGN KEY (prestamo_id) REFERENCES prestamos(id)
);

-- Tabla de pagos
CREATE TABLE pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prestamo_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_pago DATE NOT NULL,
    metodo_pago VARCHAR(50) NOT NULL,
    referencia VARCHAR(100),
    estado ENUM('Pendiente', 'Completo') DEFAULT 'Completo',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prestamo_id) REFERENCES prestamos(id)
);

-- Tabla de solicitudes de socio
CREATE TABLE solicitudes_socio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_id INT NOT NULL,
    motivo TEXT NOT NULL,
    estado ENUM('Pendiente', 'Aprobado', 'Rechazado') DEFAULT 'Pendiente',
    comentarios TEXT,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_revision DATETIME NULL,
    revisado_por INT NULL,
    FOREIGN KEY (personal_id) REFERENCES personal(id),
    FOREIGN KEY (revisado_por) REFERENCES usuarios(id)
);

-- Tabla de anuncios
CREATE TABLE anuncios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    contenido TEXT NOT NULL,
    archivo VARCHAR(255),
    publico ENUM('Todos', 'Socios', 'Empleados') NOT NULL,
    fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE logs_acceso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    accion VARCHAR(50) NOT NULL COMMENT 'login, logout, password_change',
    detalle TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);


-- Insertar personal inicial
INSERT INTO personal (codigo, dni, apellidos, nombres, tipo_personal, tipo_contrato, socio) 
VALUES 
('DOC001', '87654321', 'GARCIA', 'LUIS', 'Docente', 'Nombrado', 'SI'),
('ADM001', '12345678', 'PEREZ', 'ANA', 'Administrativo', 'Contratado', 'NO');

-- Insertar usuarios (password: Admin123)
INSERT INTO usuarios (personal_id, username, password, rol) 
VALUES 
(1, 'lgarcia', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(2, 'aperez', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'empleado');