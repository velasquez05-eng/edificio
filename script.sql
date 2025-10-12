-- ============================================
-- CREACIÓN DE TABLAS EN ORDEN CORRECTO
-- ============================================

-- 1️⃣ Tabla: rol
CREATE TABLE rol (
                     id_rol INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                     rol VARCHAR(150) NOT NULL,
                     descripcion TEXT DEFAULT NULL
);

-- 2️⃣ Tabla: persona
CREATE TABLE persona (
                         id_persona INT AUTO_INCREMENT PRIMARY KEY,
                         nombre VARBINARY(255) NOT NULL,
                         apellido_paterno VARBINARY(255) NOT NULL,
                         apellido_materno VARBINARY(255),
                         ci VARBINARY(255) NOT NULL,
                         telefono VARCHAR(50),
                         email VARCHAR(255),
                         username VARCHAR(50) NOT NULL UNIQUE,
                         password_hash VARCHAR(255) NOT NULL,
                         verificado BOOLEAN DEFAULT FALSE,
                         tiempo_verificacion DATETIME DEFAULT NULL,
                         codigo_recuperacion VARCHAR(6) DEFAULT NULL,
                         tiempo_codigo_recuperacion DATETIME DEFAULT NULL,
                         tiempo_bloqueo DATETIME DEFAULT NULL,
                         fecha_eliminado DATETIME DEFAULT NULL,
                         estado ENUM('activo', 'inactivo') DEFAULT 'activo',
                         id_rol INT,
                         FOREIGN KEY (id_rol) REFERENCES rol(id_rol)
);

-- 3️⃣ Tabla: historial_login
CREATE TABLE historial_login (
                                 id_historial_login INT AUTO_INCREMENT PRIMARY KEY,
                                 id_persona INT NOT NULL,
                                 fecha DATETIME NOT NULL,
                                 estado ENUM('exitoso', 'fallido') NOT NULL,
                                 FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
);

-- 4️⃣ Tabla: area_comun
CREATE TABLE area_comun (
                            id_area INT AUTO_INCREMENT PRIMARY KEY,
                            nombre VARCHAR(100) NOT NULL,
                            descripcion TEXT DEFAULT NULL,
                            capacidad INT DEFAULT NULL,
                            estado ENUM('disponible', 'mantenimiento', 'ocupada') DEFAULT 'disponible'
);

-- 5️⃣ Tabla: departamento
CREATE TABLE departamento (
                              id_departamento INT AUTO_INCREMENT PRIMARY KEY,
                              numero VARCHAR(10) NOT NULL,
                              piso INT NOT NULL,
                              estado ENUM('ocupado', 'disponible', 'mantenimiento') DEFAULT 'disponible'
);

-- 6️⃣ Tabla: reserva_area_comun
CREATE TABLE reserva_area_comun (
                                    id_persona INT NOT NULL,
                                    id_area INT NOT NULL,
                                    fecha_reserva DATE NOT NULL,
                                    hora_inicio TIME NOT NULL,
                                    hora_fin TIME NOT NULL,
                                    estado ENUM('pendiente', 'confirmada', 'cancelada') DEFAULT 'pendiente',
                                    FOREIGN KEY (id_persona) REFERENCES persona(id_persona),
                                    FOREIGN KEY (id_area) REFERENCES area_comun(id_area)
);

-- 7️⃣ Tabla: tiene_departamento
CREATE TABLE tiene_departamento (
                                    id_dep_per INT AUTO_INCREMENT PRIMARY KEY,
                                    id_departamento INT NOT NULL,
                                    id_persona INT NOT NULL,
                                    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
                                    FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento),
                                    FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
);

-- 8️⃣ Tabla: servicio
CREATE TABLE servicio (
                          id_servicio INT AUTO_INCREMENT PRIMARY KEY,
                          nombre ENUM('agua', 'luz', 'gas') NOT NULL UNIQUE,
                          unidad_medida VARCHAR(50) NOT NULL,
                          costo_unitario DECIMAL(10,2) NOT NULL,
                          estado ENUM('activo', 'inactivo') DEFAULT 'activo'
);

-- 9️⃣ Tabla: medidor
CREATE TABLE medidor (
                         id_medidor INT AUTO_INCREMENT PRIMARY KEY,
                         codigo VARCHAR(50) NOT NULL UNIQUE,
                         id_servicio INT NOT NULL,
                         id_departamento INT NOT NULL,
                         fecha_instalacion DATE DEFAULT CURRENT_DATE,
                         estado ENUM('activo', 'mantenimiento', 'baja', 'corte') DEFAULT 'activo',
                         FOREIGN KEY (id_servicio) REFERENCES servicio(id_servicio),
                         FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento)
);

-- 🔟 Tabla: lector_sensor_consumo
CREATE TABLE lector_sensor_consumo (
                                       id_lectura INT AUTO_INCREMENT PRIMARY KEY,
                                       id_medidor INT NOT NULL,
                                       fecha_hora DATETIME NOT NULL,
                                       consumo DECIMAL(10,2) NOT NULL,
                                       FOREIGN KEY (id_medidor) REFERENCES medidor(id_medidor)
);

-- 11️⃣ Tabla: historial_consumo
CREATE TABLE historial_consumo (
                                   id_historial_consumo INT AUTO_INCREMENT PRIMARY KEY,
                                   id_medidor INT NOT NULL,
                                   fecha_inicio DATETIME NOT NULL,
                                   fecha_fin DATETIME NOT NULL,
                                   consumo_total DECIMAL(10,2) NOT NULL,
                                   FOREIGN KEY (id_medidor) REFERENCES medidor(id_medidor)
);

-- 12️⃣ Tabla: factura
CREATE TABLE factura (
                         id_factura INT AUTO_INCREMENT PRIMARY KEY,
                         id_departamento INT NOT NULL,
                         id_servicio INT NOT NULL,
                         id_historial_consumo INT NOT NULL,
                         fecha_emision DATE NOT NULL DEFAULT CURRENT_DATE,
                         fecha_vencimiento DATE NOT NULL,
                         monto_total DECIMAL(10,2) NOT NULL,
                         estado ENUM('pendiente', 'pagada', 'vencida') DEFAULT 'pendiente',
                         FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento),
                         FOREIGN KEY (id_servicio) REFERENCES servicio(id_servicio),
                         FOREIGN KEY (id_historial_consumo) REFERENCES historial_consumo(id_historial_consumo)
);

-- 13️⃣ Tabla: persona_paga_factura
CREATE TABLE persona_paga_factura (
                                      id_factura INT NOT NULL,
                                      id_persona INT NOT NULL,
                                      fecha_pago DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                      monto_pagado DECIMAL(10,2) NOT NULL,
                                      PRIMARY KEY (id_factura, id_persona),
                                      FOREIGN KEY (id_factura) REFERENCES factura(id_factura),
                                      FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
);

-- 14️⃣ Tabla: historial_pago
CREATE TABLE historial_pago (
                                id_historial_pago INT AUTO_INCREMENT PRIMARY KEY,
                                id_factura INT NOT NULL,
                                id_persona INT NOT NULL,
                                monto_pagado DECIMAL(10,2) NOT NULL,
                                fecha_pago DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                observacion TEXT DEFAULT NULL,
                                FOREIGN KEY (id_factura) REFERENCES factura(id_factura),
                                FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
);

-- 15️⃣ Tabla: incidente
CREATE TABLE incidente (
                           id_incidente INT AUTO_INCREMENT PRIMARY KEY,
                           id_departamento INT NOT NULL,
                           id_residente INT NOT NULL,
                           descripcion TEXT NOT NULL,
                           fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                           estado ENUM('pendiente', 'en_proceso', 'resuelto', 'cancelado') DEFAULT 'pendiente',
                           FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento),
                           FOREIGN KEY (id_residente) REFERENCES persona(id_persona)
);

-- 16️⃣ Tabla: incidente_asignado
CREATE TABLE incidente_asignado (
                                    id_asignacion INT AUTO_INCREMENT PRIMARY KEY,
                                    id_incidente INT NOT NULL,
                                    id_personal INT NOT NULL,
                                    fecha_asignacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    fecha_atencion DATETIME DEFAULT NULL,
                                    FOREIGN KEY (id_incidente) REFERENCES incidente(id_incidente),
                                    FOREIGN KEY (id_personal) REFERENCES persona(id_persona)
);

-- 17️⃣ Tabla: historial_incidente
CREATE TABLE historial_incidente (
                                     id_historial_incidente INT AUTO_INCREMENT PRIMARY KEY,
                                     id_incidente INT NOT NULL,
                                     id_persona INT NOT NULL,
                                     accion ENUM('asignacion', 'inicio_atencion', 'actualizacion', 'resolucion', 'cancelacion') NOT NULL,
                                     observacion TEXT DEFAULT NULL,
                                     estado_anterior ENUM('pendiente', 'en_proceso', 'resuelto', 'cancelado') DEFAULT NULL,
                                     estado_nuevo ENUM('pendiente', 'en_proceso', 'resuelto', 'cancelado') DEFAULT NULL,
                                     fecha_accion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                     FOREIGN KEY (id_incidente) REFERENCES incidente(id_incidente),
                                     FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
);

-- 18️⃣ Tabla: alerta_predictiva
CREATE TABLE alerta_predictiva (
                                   id_alerta INT AUTO_INCREMENT PRIMARY KEY,
                                   id_departamento INT NOT NULL,
                                   id_servicio INT NOT NULL,
                                   tipo_alerta ENUM('riesgo_corte', 'corte') NOT NULL,
                                   facturas_vencidas INT NOT NULL DEFAULT 0,
                                   fecha_alerta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                   FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento),
                                   FOREIGN KEY (id_servicio) REFERENCES servicio(id_servicio)
);

-- 19️⃣ Tabla: notificacion_persona
CREATE TABLE notificacion_persona (
                                      id_alerta INT NOT NULL,
                                      id_persona INT NOT NULL,
                                      fecha_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                      estado ENUM('enviado', 'recibido', 'leído') DEFAULT 'enviado',
                                      medio ENUM('email', 'sms', 'app') DEFAULT 'email',
                                      observacion TEXT DEFAULT NULL,
                                      FOREIGN KEY (id_alerta) REFERENCES alerta_predictiva(id_alerta),
                                      FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
);
