-- ========================================================
-- TABLAS DE USUARIOS 
-- ========================================================

CREATE TABLE persona (
  id_persona INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  appaterno VARCHAR(100) NOT NULL,
  apmaterno VARCHAR(100) NOT NULL,
  fecha_naci date not null,
  ci VARCHAR(20) NOT NULL UNIQUE,
  telefono VARCHAR(20) DEFAULT NULL,
  email VARCHAR(150) DEFAULT NULL
);

-- PRIMERO crear roles
CREATE TABLE roles (
  id_rol INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre_rol VARCHAR(100) NOT NULL,
  descripcion TEXT DEFAULT NULL
);

CREATE TABLE usuario (
  id_usuario INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  codigo_recuperacion VARCHAR(6) DEFAULT NULL,
  expiracion_codigo DATETIME DEFAULT NULL,
  id_persona INT(11) NOT NULL,
  FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
);

CREATE TABLE personal (
  id_personal INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  cargo VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  fecha_contratacion DATE NOT NULL,
  codigo_recuperacion VARCHAR(6) DEFAULT NULL,
  expiracion_codigo DATETIME DEFAULT NULL,
  id_persona INT(11) NOT NULL,
  id_rol INT(11) NOT NULL,
  FOREIGN KEY (id_persona) REFERENCES persona(id_persona),
  FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);

-- ========================================================
-- TABLAS DE SERVICIOS Y FACTURACIÓN 
-- ========================================================

CREATE TABLE edificio (
  id_edificio INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  direccion VARCHAR(200) NOT NULL
);

CREATE TABLE departamento (
  id_departamento INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  numero VARCHAR(20) NOT NULL,
  piso INT(11) NOT NULL,
  id_edificio INT(11) NOT NULL,
  FOREIGN KEY (id_edificio) REFERENCES edificio(id_edificio)
);

CREATE TABLE area_comun (
  id_area_comun int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre varchar(100) NOT NULL,
  descripcion text DEFAULT NULL,
  capacidad int(11) DEFAULT NULL,
  id_edificio int(11) NOT NULL,
  FOREIGN KEY (id_edificio) REFERENCES edificio(id_edificio)
);

CREATE TABLE reserva (
  id_usuario int(11) NOT NULL,
  id_area_comun int(11) NOT NULL,
  fecha_inicio datetime NOT NULL,
  fecha_fin datetime NOT NULL,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
  FOREIGN KEY (id_area_comun) REFERENCES area_comun(id_area_comun)
);


CREATE TABLE pertenece_dep (
  id_usuario INT(11) NOT NULL,
  id_departamento INT(11) NOT NULL,
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE,
  estado ENUM('activo', 'desactivado') NOT NULL,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
  FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento)
);

CREATE TABLE servicio (
  id_servicio INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre_servicio VARCHAR(100) NOT NULL,
  descripcion TEXT DEFAULT NULL,
  tipo_tarifa ENUM('medidor', 'fijo') NOT NULL,
  costo_unitario DECIMAL(10,4) DEFAULT NULL,
  tarifa_fija DECIMAL(10,2) DEFAULT NULL,
  unidad_medida VARCHAR(20) DEFAULT NULL
);

CREATE TABLE tiene_serv (
  id_departamento INT(11) NOT NULL,
  id_servicio INT(11) NOT NULL,
  fecha_inicio_servicio DATE NOT NULL,
  fecha_fin_servicio DATE NOT NULL,
  FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento),
  FOREIGN KEY (id_servicio) REFERENCES servicio(id_servicio)	
);

CREATE TABLE lectura_sensor (
  id_lectura INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  hora_lectura DATETIME NOT NULL,
  consumo INT(11) NOT NULL,
  id_departamento INT(11) NOT NULL,
  id_servicio INT(11) NOT NULL,
  FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento),
  FOREIGN KEY (id_servicio) REFERENCES servicio(id_servicio)
);


CREATE TABLE historial_consumo (
  id_historial_consumo INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fecha_inicio_lectura DATETIME NOT NULL,
  fecha_fin_lectura DATETIME NOT NULL,
  consumo_total DECIMAL(10,2) DEFAULT NULL,
  id_lectura INT(11) NOT NULL,
  FOREIGN KEY (id_lectura) REFERENCES lectura_sensor(id_lectura)
);

CREATE TABLE factura (
  id_factura INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fecha_emision DATE NOT NULL,
  fecha_vencimiento DATE NOT NULL,
  monto DECIMAL(10,2) NOT NULL,
  estado_pago VARCHAR(50) DEFAULT NULL,
  qr_code_url VARCHAR(255) DEFAULT NULL,
  id_historial_consumo INT(11) NOT NULL,
  FOREIGN KEY (id_historial_consumo) REFERENCES historial_consumo(id_historial_consumo)
);

CREATE TABLE pago (
  id_usuario INT(11) NOT NULL,
  id_factura INT(11) NOT NULL,
  fecha_pago DATE NOT NULL,
  metodo_pago VARCHAR(150) NOT NULL,
  referencia_bancaria VARCHAR(100) DEFAULT NULL,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
  FOREIGN KEY (id_factura) REFERENCES factura(id_factura)
);

CREATE TABLE historial_pagos (
  id_historial_pago INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_factura INT(11) NOT NULL,
  fecha_emision DATE NOT NULL,
  fecha_vencimiento DATE NOT NULL,
  monto_total DECIMAL(10,2) NOT NULL,
  fecha_pago DATE NOT NULL,
  metodo_pago VARCHAR(150) NOT NULL,
  FOREIGN KEY (id_factura) REFERENCES factura(id_factura)
);

CREATE TABLE alerta_predictiva (
  id_alerta INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_historial_pago INT(11) NOT NULL,
  id_factura INT(11) NOT NULL,
  id_usuario INT(11) NOT NULL,
  contador_meses_atrasados INT(11) NOT NULL,
  nivel_riesgo ENUM('riesgo', 'corte') NOT NULL,
  fecha_creacion DATETIME NOT NULL,
  FOREIGN KEY (id_historial_pago) REFERENCES historial_pagos(id_historial_pago),
  FOREIGN KEY (id_factura) REFERENCES factura(id_factura),
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

CREATE TABLE notificacion (
  id_notificacion int(11) not null PRIMARY KEY,
  titulo VARCHAR(150) not null,
  mensaje TEXT not null,
  id_alerta int(11) not null,
  FOREIGN KEY (id_alerta) REFERENCES alerta_predictiva(id_alerta)
);

CREATE TABLE tiene_noti(
  id_usuario int(11) not null,
  id_notificacion int(11) not null,
  fecha_hora_notificacion date not null,
  estado ENUM('recibido', 'leido') NOT NULL,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);
-- ========================================================
-- TABLAS DE INCIDENTES (CORREGIDAS)
-- ========================================================

CREATE TABLE incidente (
  id_incidente INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  titulo_incidente VARCHAR(100) NOT NULL,
  descripcion TEXT DEFAULT NULL,
  fecha_creacion DATETIME NOT NULL,
  estado VARCHAR(50) DEFAULT NULL,
  id_departamento INT(11) NOT NULL,
  FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento)
);

CREATE TABLE atiende_incidente (
  id_incidente INT(11) NOT NULL,
  id_personal INT(11) NOT NULL,
  fecha_atendido DATETIME NOT NULL,
  acciones_tomadas TEXT DEFAULT NULL,
  FOREIGN KEY (id_incidente) REFERENCES incidente(id_incidente),
  FOREIGN KEY (id_personal) REFERENCES personal(id_personal)
);

CREATE TABLE historial_incidentes (
  id_historial_incidente INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fecha_creacion DATETIME NOT NULL,
  fecha_atendido DATETIME NOT NULL,
  acciones_tomadas TEXT DEFAULT NULL,
  id_incidente INT(11) NOT NULL,
  id_personal INT(11) NOT NULL,
  FOREIGN KEY (id_incidente) REFERENCES incidente(id_incidente),
  FOREIGN KEY (id_personal) REFERENCES personal(id_personal)
);



-- ========================================================
-- CREAR ADMINISTRADOR POR DEFECTO (CORREGIDO)
-- ========================================================

-- 1. Crear persona
INSERT INTO persona (nombre, appaterno, apmaterno, fecha_naci, ci, telefono, email)
VALUES ('Maximo', 'Decimo', 'Meridio', '1980-01-01', '99999999', '000000000', 'admin@gmail.com');

-- 2. Crear rol administrador primero
INSERT INTO roles (nombre_rol, descripcion) 
VALUES ('Administrador', 'Acceso total al sistema');


-- 3. Crear personal administrador Admin11@
INSERT INTO personal (cargo, username, password_hash, fecha_contratacion, id_persona, id_rol)
VALUES (
  'Administrador',
  'admin',
  '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu',
  CURDATE(),
  (SELECT id_persona FROM persona WHERE ci = '99999999'),
  (SELECT id_rol FROM roles WHERE nombre_rol = 'Administrador')
);


-- ========================================================
-- CREAR EDIFICIO MARQUÉS
-- ========================================================

INSERT INTO edificio (nombre, direccion)
VALUES ('Bilbao', 'Av. Principal 123, Zona Centro');


-- ========================================================
-- CREAR FUNCIONES
-- ========================================================
