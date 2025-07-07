-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS foapunp;
USE foapunp;

-- Tabla de personal (trabajadores)
CREATE TABLE personal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) UNIQUE NOT NULL COMMENT 'Código laboral',
    dni VARCHAR(8) UNIQUE NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    tipo_personal ENUM('Docente', 'Administrativo', 'Servicios') NOT NULL,
    tipo_contrato ENUM('Nombrado', 'Contratado', 'CAS') NOT NULL,
    socio ENUM('SI', 'NO') NOT NULL DEFAULT 'NO',
    fecha_ingreso DATE,
    dependencia VARCHAR(100),
    telefono VARCHAR(15),
    direccion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de usuarios (acceso al sistema)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_id INT UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'socio', 'empleado') NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    ultimo_login DATETIME,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personal_id) REFERENCES personal(id)
);

-- Tabla de ahorros
CREATE TABLE ahorros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    tipo ENUM('Obligatorio', 'Voluntario') NOT NULL,
    fecha_ahorro DATE NOT NULL,
    descripcion VARCHAR(200),
    referencia VARCHAR(50),
    registrado_por INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personal_id) REFERENCES personal(id),
    FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
);

-- Tabla de préstamos
CREATE TABLE prestamos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    cuotas INT NOT NULL,
    tasa_interes DECIMAL(5,2) NOT NULL COMMENT 'Tasa anual',
    cuota_mensual DECIMAL(10,2) NOT NULL,
    motivo TEXT,
    estado ENUM('Pendiente', 'Aprobado', 'Rechazado', 'Cancelado') DEFAULT 'Pendiente',
    aprobado_por INT,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_aprobacion DATETIME,
    fecha_finalizacion DATETIME,
    comentarios TEXT,
    FOREIGN KEY (personal_id) REFERENCES personal(id),
    FOREIGN KEY (aprobado_por) REFERENCES usuarios(id)
);

-- Tabla de pagos de préstamos
CREATE TABLE pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prestamo_id INT NOT NULL,
    numero_cuota INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    capital DECIMAL(10,2) NOT NULL,
    interes DECIMAL(10,2) NOT NULL,
    saldo_restante DECIMAL(10,2) NOT NULL,
    fecha_pago DATE NOT NULL,
    metodo_pago ENUM('Planilla', 'Transferencia', 'Depósito', 'Efectivo') NOT NULL,
    referencia VARCHAR(100),
    estado ENUM('Pendiente', 'Completo', 'Atrasado') DEFAULT 'Completo',
    registrado_por INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prestamo_id) REFERENCES prestamos(id),
    FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
);

-- Tabla de documentos
CREATE TABLE documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    archivo VARCHAR(255) NOT NULL,
    tipo ENUM('Reglamento', 'Contrato', 'Formato', 'Circular', 'Otro') NOT NULL,
    personal_id INT,
    prestamo_id INT,
    publico BOOLEAN DEFAULT FALSE,
    fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    subido_por INT NOT NULL,
    FOREIGN KEY (personal_id) REFERENCES personal(id),
    FOREIGN KEY (prestamo_id) REFERENCES prestamos(id),
    FOREIGN KEY (subido_por) REFERENCES usuarios(id)
);

-- Tabla de solicitudes de socio
CREATE TABLE solicitudes_socio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_id INT NOT NULL,
    motivo TEXT NOT NULL,
    estado ENUM('Pendiente', 'Aprobado', 'Rechazado') DEFAULT 'Pendiente',
    comentarios TEXT,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_revision DATETIME,
    revisado_por INT,
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

-- Tabla de logs de acceso
CREATE TABLE logs_acceso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    accion VARCHAR(50) NOT NULL COMMENT 'login, logout, password_change',
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de parámetros del sistema
CREATE TABLE parametros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) UNIQUE NOT NULL,
    valor TEXT NOT NULL,
    descripcion TEXT,
    editable BOOLEAN DEFAULT TRUE,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    actualizado_por INT,
    FOREIGN KEY (actualizado_por) REFERENCES usuarios(id)
);

-- Insertar parámetros iniciales
INSERT INTO parametros (clave, valor, descripcion, editable) VALUES
('tasa_interes_anual', '5.00', 'Tasa de interés anual para préstamos', TRUE),
('max_cuotas', '12', 'Número máximo de cuotas para préstamos', TRUE),
('porcentaje_max_prestamo', '80', 'Porcentaje máximo del ahorro que se puede prestar', TRUE),
('dias_gracia', '5', 'Días de gracia para pagos', TRUE),
('penalidad_mora', '2.00', 'Porcentaje de penalidad por mora', TRUE);

-- Insertar usuario admin inicial (password: Admin123)
INSERT INTO personal (codigo, dni, apellidos, nombres, tipo_personal, tipo_contrato, socio) 
VALUES ('ADMIN001', '12345678', 'Administrador', 'Sistema', 'Administrativo', 'Nombrado', 'SI');

INSERT INTO usuarios (personal_id, username, password, rol) 
VALUES (1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insertar parámetros iniciales
INSERT INTO parametros (clave, valor, descripcion) VALUES
('tasa_interes_anual', '5.00', 'Tasa de interés anual para préstamos'),
('max_cuotas', '12', 'Número máximo de cuotas para préstamos'),
('porcentaje_max_prestamo', '80', 'Porcentaje máximo del ahorro que se puede prestar');
