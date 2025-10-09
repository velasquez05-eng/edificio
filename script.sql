CREATE TABLE persona (
                         id_persona INT AUTO_INCREMENT PRIMARY KEY,
                         nombre VARBINARY(255) NOT NULL,
                         apellido_paterno VARBINARY(255) NOT NULL,
                         apellido_materno VARBINARY(255),
                         ci VARBINARY(255) NOT NULL,
                         telefono VARCHAR(50),
                         email VARCHAR(255),
                         estado ENUM('activo', 'inactivo') DEFAULT 'activo',
                         id_rol int,
                         FOREIGN KEY (id_rol) REFERENCES rol(id_rol)
);

CREATE TABLE rol (
                     id_rol INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                     rol VARCHAR(150) NOT NULL,
                     descripcion TEXT DEFAULT NULL
);

CREATE TABLE login (
                       id_login INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(50) NOT NULL UNIQUE,
                       password_hash VARCHAR(255) NOT NULL,
                       verificado BOOLEAN DEFAULT FALSE,
                       tiempo_verificacion DATETIME DEFAULT NULL,
                       codigo_recuperacion VARCHAR(6) DEFAULT NULL,
                       tiempo_codigo_recuperacion DATETIME DEFAULT NULL,
                       tiempo_bloqueo DATETIME DEFAULT NULL,
                       estado ENUM('activo', 'inactivo') DEFAULT 'activo',
                       id_persona INT NOT NULL,
                       FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
);

CREATE TABLE historial_login (
                             id_historial_login INT AUTO_INCREMENT PRIMARY KEY,
                             id_login INT NOT NULL,
                             fecha DATETIME NOT NULL,
                             ip VARCHAR(50),
                             estado ENUM('exitoso', 'fallido') NOT NULL,
                             FOREIGN KEY (id_login) REFERENCES login(id_login)
);

CREATE TABLE area_comun (
                            id_area INT AUTO_INCREMENT PRIMARY KEY,
                            nombre VARCHAR(100) NOT NULL,
                            descripcion TEXT DEFAULT NULL,
                            capacidad INT DEFAULT NULL,
                            estado ENUM('disponible', 'mantenimiento', 'ocupada') DEFAULT 'disponible'
);

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

CREATE TABLE departamento (
                              id_departamento INT AUTO_INCREMENT PRIMARY KEY,
                              numero VARCHAR(10) NOT NULL,
                              piso INT NOT NULL,
                              metros_cuadrados DECIMAL(6,2) DEFAULT NULL,
                              estado ENUM('ocupado', 'disponible', 'mantenimiento') DEFAULT 'disponible'
);

CREATE TABLE incidente (
                           id_incidente INT AUTO_INCREMENT PRIMARY KEY,
                           id_departamento INT NOT NULL,
                           id_residente INT NOT NULL,          -- quién reporta el incidente
                           descripcion TEXT NOT NULL,
                           fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                           estado ENUM('pendiente', 'en_proceso', 'resuelto', 'cancelado') DEFAULT 'pendiente',
                           FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento),
                           FOREIGN KEY (id_residente) REFERENCES persona(id_persona)
);

CREATE TABLE incidente_asignado (
                                    id_asignacion INT AUTO_INCREMENT PRIMARY KEY,
                                    id_incidente INT NOT NULL,
                                    id_personal INT NOT NULL,
                                    fecha_asignacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    fecha_atencion DATETIME DEFAULT NULL,
                                    FOREIGN KEY (id_incidente) REFERENCES incidente(id_incidente),
                                    FOREIGN KEY (id_personal) REFERENCES persona(id_persona)
);

CREATE TABLE historial_incidente (
                                     id_historial_incidente INT AUTO_INCREMENT PRIMARY KEY,
                                     id_incidente INT NOT NULL,
                                     id_persona INT NOT NULL,  -- quien realiza la acción (admin o personal)
                                     accion ENUM('asignacion', 'inicio_atencion', 'actualizacion', 'resolucion', 'cancelacion') NOT NULL,
                                     observacion TEXT DEFAULT NULL,
                                     estado_anterior ENUM('pendiente', 'en_proceso', 'resuelto', 'cancelado') DEFAULT NULL,
                                     estado_nuevo ENUM('pendiente', 'en_proceso', 'resuelto', 'cancelado') DEFAULT NULL,
                                     fecha_accion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                     FOREIGN KEY (id_incidente) REFERENCES incidente(id_incidente),
                                     FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
);


CREATE TABLE tiene_departamento (
                                      id_dep_per INT AUTO_INCREMENT PRIMARY KEY,
                                      id_departamento INT NOT NULL,
                                      id_persona INT NOT NULL,
                                      estado ENUM('activo', 'inactivo') DEFAULT 'activo',
                                      FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento),
                                      FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
);


CREATE TABLE servicio (
                          id_servicio INT AUTO_INCREMENT PRIMARY KEY,
                          nombre ENUM('agua', 'luz', 'gas') NOT NULL UNIQUE,
                          unidad_medida VARCHAR(50) NOT NULL,
                          costo_unitario DECIMAL(10,2) NOT NULL,
                          estado ENUM('activo', 'inactivo') DEFAULT 'activo'
);

CREATE TABLE medidor (
                         id_medidor INT AUTO_INCREMENT PRIMARY KEY,
                         codigo VARCHAR(50) NOT NULL UNIQUE,          -- Código o número del medidor
                         id_servicio INT NOT NULL,
                         id_departamento INT NOT NULL,
                         fecha_instalacion DATE DEFAULT CURRENT_DATE,
                         estado ENUM('activo', 'mantenimiento', 'baja','corte') DEFAULT 'activo',
                         FOREIGN KEY (id_servicio) REFERENCES servicio(id_servicio),
                         FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento)
);

CREATE TABLE lector_sensor_consumo (
                                       id_lectura INT AUTO_INCREMENT PRIMARY KEY,
                                       id_medidor INT NOT NULL,
                                       fecha_hora DATETIME NOT NULL,       -- fecha y hora exacta de la lectura
                                       consumo DECIMAL(10,2) NOT NULL,    -- consumo registrado en esa hora
                                       FOREIGN KEY (id_medidor) REFERENCES medidor(id_medidor)
);

CREATE TABLE historial_consumo (
                                   id_historial_consumo INT AUTO_INCREMENT PRIMARY KEY,
                                   id_medidor INT NOT NULL,
                                   fecha_inicio DATETIME NOT NULL,      -- inicio del periodo de consumo
                                   fecha_fin DATETIME NOT NULL,         -- fin del periodo de consumo
                                   consumo_total DECIMAL(10,2) NOT NULL, -- suma de todas las lecturas del periodo
                                   FOREIGN KEY (id_medidor) REFERENCES medidor(id_medidor)
);


CREATE TABLE factura (
                         id_factura INT AUTO_INCREMENT PRIMARY KEY,
                         id_departamento INT NOT NULL,                   -- Departamento al que pertenece la factura
                         id_servicio INT NOT NULL,                       -- Servicio correspondiente (agua, luz, gas)
                         id_historial_consumo INT NOT NULL,              -- Historial de consumo que respalda la factura
                         fecha_emision DATE NOT NULL DEFAULT CURRENT_DATE,
                         fecha_vencimiento DATE NOT NULL,               -- Fecha límite de pago
                         monto_total DECIMAL(10,2) NOT NULL,            -- Cálculo: consumo_total * costo_unitario
                         estado ENUM('pendiente', 'pagada', 'vencida') DEFAULT 'pendiente',
                         FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento),
                         FOREIGN KEY (id_servicio) REFERENCES servicio(id_servicio),
                         FOREIGN KEY (id_historial_consumo) REFERENCES historial_consumo(id_historial_consumo)
);

CREATE TABLE persona_paga_factura (
                                      id_factura INT NOT NULL,
                                      id_persona INT NOT NULL,
                                      fecha_pago DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                      monto_pagado DECIMAL(10,2) NOT NULL,
                                      PRIMARY KEY (id_factura, id_persona),
                                      FOREIGN KEY (id_factura) REFERENCES factura(id_factura),
                                      FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
);

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

CREATE TABLE alerta_predictiva (
                                   id_alerta INT AUTO_INCREMENT PRIMARY KEY,
                                   id_departamento INT NOT NULL,                 -- Departamento al que pertenece la alerta
                                   id_servicio INT NOT NULL,                     -- Servicio correspondiente (agua, luz, gas)
                                   tipo_alerta ENUM('riesgo_corte', 'corte') NOT NULL,  -- Predicción o corte efectivo
                                   facturas_vencidas INT NOT NULL DEFAULT 0,     -- Número de facturas vencidas que provocan la alerta
                                   fecha_alerta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                   FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento),
                                   FOREIGN KEY (id_servicio) REFERENCES servicio(id_servicio)
);

CREATE TABLE notificacion_persona (
                                      id_alerta INT NOT NULL,                          -- Alerta predictiva que se envía
                                      id_persona INT NOT NULL,                         -- Persona que recibe la notificación
                                      fecha_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                      estado ENUM('enviado', 'recibido', 'leído') DEFAULT 'enviado',
                                      medio ENUM('email', 'sms', 'app') DEFAULT 'email',
                                      observacion TEXT DEFAULT NULL,
                                      FOREIGN KEY (id_alerta) REFERENCES alerta_predictiva(id_alerta),
                                      FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
);
