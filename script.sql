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
                         intentos_fallidos INT DEFAULT 0,
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
                            costo_reserva decimal(10,2) not null,
                            fecha_inicio_mantenimiento datetime default null,
                            fecha_fin_mantenimiento datetime default null,
                            estado ENUM('disponible', 'mantenimiento', 'no disponible','eliminado') DEFAULT 'disponible'
);

-- 2️⃣ Tabla: comunicado
CREATE TABLE comunicado (
                            id_comunicado INT AUTO_INCREMENT PRIMARY KEY,
                            id_persona INT NOT NULL,
                            titulo VARCHAR(255) NOT NULL,
                            contenido TEXT NOT NULL,
                            fecha_publicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            fecha_expiracion DATE DEFAULT NULL,
                            prioridad ENUM('baja', 'media', 'alta', 'urgente') DEFAULT 'media',
                            estado ENUM('borrador', 'publicado', 'archivado') DEFAULT 'borrador',
                            tipo_audiencia ENUM('Todos', 'Residente', 'Personal') NOT NULL,
                            FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
);

-- 5️⃣ Tabla: departamento
CREATE TABLE departamento (
                              id_departamento INT AUTO_INCREMENT PRIMARY KEY,
                              numero VARCHAR(10) NOT NULL,
                              piso INT NOT NULL,
                              estado ENUM('ocupado', 'disponible') DEFAULT 'disponible'
);

-- 6️⃣ Tabla: reserva_area_comun
CREATE TABLE reserva_area_comun (
                                    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
                                    id_persona INT NOT NULL,
                                    id_area INT NOT NULL,
                                    fecha_reserva DATE NOT NULL,
                                    hora_inicio TIME NOT NULL,
                                    hora_fin TIME NOT NULL,
                                    motivo TEXT DEFAULT NULL,
                                    estado ENUM('pendiente', 'confirmada', 'cancelada') DEFAULT 'pendiente',
                                    FOREIGN KEY (id_persona) REFERENCES persona(id_persona),
                                    FOREIGN KEY (id_area) REFERENCES area_comun(id_area),
                                    UNIQUE (id_area, fecha_reserva, hora_inicio, hora_fin)
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
                         fecha_emision DATE NOT NULL DEFAULT CURRENT_DATE,
                         fecha_vencimiento DATE NOT NULL,
                         monto_total DECIMAL(10,2) NOT NULL,
                         estado ENUM('pendiente', 'pagada', 'vencida') DEFAULT 'pendiente',
                         FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento)
);

-- 14️⃣ Tabla: conceptos
CREATE TABLE conceptos (
                           id_concepto INT AUTO_INCREMENT PRIMARY KEY,
                           id_persona INT NOT NULL,
                           id_factura INT NULL,
                           concepto ENUM('agua', 'luz', 'gas', 'mantenimiento', 'reserva_area', 'incidente', 'multa', 'otros') NOT NULL,
                           monto DECIMAL(10,2) NOT NULL,
                           id_origen INT NULL,
                           tipo_origen ENUM('reserva', 'consumo', 'incidente', 'mantenimiento', 'multa', 'otros') NULL,
                           cantidad INT DEFAULT 1,
                           descripcion TEXT NOT NULL,
                           fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
                           estado ENUM('pendiente', 'facturado', 'cancelado') DEFAULT 'pendiente',
                           FOREIGN KEY (id_factura) REFERENCES factura(id_factura),
                           FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
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


-- ============================================
-- TRIGGER PARA CALCULAR COSTO DE RESERVA
-- ============================================

DELIMITER //

CREATE TRIGGER after_reserva_area_comun_insert
    AFTER INSERT ON reserva_area_comun
    FOR EACH ROW
BEGIN
    DECLARE v_costo_hora DECIMAL(10,2);
    DECLARE v_horas_reserva DECIMAL(10,2);
    DECLARE v_costo_total DECIMAL(10,2);
    DECLARE v_id_persona_reserva INT;

    -- Obtener el costo por hora del área común
    SELECT costo_reserva INTO v_costo_hora
    FROM area_comun
    WHERE id_area = NEW.id_area;

    -- Calcular la cantidad de horas de la reserva
    SET v_horas_reserva = TIMESTAMPDIFF(HOUR, CONCAT(NEW.fecha_reserva, ' ', NEW.hora_inicio), CONCAT(NEW.fecha_reserva, ' ', NEW.hora_fin));

    -- Si la diferencia es negativa (reserva que pasa de un día a otro), ajustar
    IF v_horas_reserva < 0 THEN
        SET v_horas_reserva = 24 + v_horas_reserva;
    END IF;

    -- Calcular el costo total
    SET v_costo_total = v_costo_hora * v_horas_reserva;

    -- Obtener el ID de la persona que realizó la reserva
    SET v_id_persona_reserva = NEW.id_persona;

    -- Insertar el concepto en la tabla conceptos
    INSERT INTO conceptos (
        id_persona,
        id_factura,  -- NULL por ahora, se asignará cuando se genere la factura
        concepto,
        monto,
        id_origen,
        tipo_origen,
        cantidad,
        descripcion,
        estado
    ) VALUES (
                 v_id_persona_reserva,
                 NULL,
                 'reserva_area',
                 v_costo_total,
                 NEW.id_reserva,  -- ID de la reserva como origen
                 'reserva',
                 v_horas_reserva,
                 CONCAT('Reserva de área común: ',
                        (SELECT nombre FROM area_comun WHERE id_area = NEW.id_area),
                        ' - Fecha: ', NEW.fecha_reserva,
                        ' - Horario: ', NEW.hora_inicio, ' a ', NEW.hora_fin,
                        ' - ', v_horas_reserva, ' hora(s)'),
                 'pendiente'
             );

END//

DELIMITER ;

-- ============================================
-- TRIGGER ADICIONAL PARA ACTUALIZAR CONCEPTO SI SE CANCELA LA RESERVA
-- ============================================

DELIMITER //

CREATE TRIGGER after_reserva_area_comun_update
    AFTER UPDATE ON reserva_area_comun
    FOR EACH ROW
BEGIN
    -- Si el estado cambia a 'cancelada', cancelar el concepto asociado
    IF NEW.estado = 'cancelada' AND OLD.estado != 'cancelada' THEN
        UPDATE conceptos
        SET estado = 'cancelado'
        WHERE tipo_origen = 'reserva'
          AND id_origen = NEW.id_reserva  -- Buscamos por id_reserva
          AND estado = 'pendiente';
    END IF;

    -- Si el estado cambia de 'cancelada' a otro estado, reactivar el concepto
    IF OLD.estado = 'cancelada' AND NEW.estado != 'cancelada' THEN
        UPDATE conceptos
        SET estado = 'pendiente'
        WHERE tipo_origen = 'reserva'
          AND id_origen = NEW.id_reserva  -- Buscamos por id_reserva
          AND estado = 'cancelado';
    END IF;
END//

DELIMITER ;

-- ============================================
-- TRIGGER PARA ACTUALIZACIONES DE HORA
-- ============================================
DELIMITER //

CREATE TRIGGER after_reserva_area_comun_update_horario
    AFTER UPDATE ON reserva_area_comun
    FOR EACH ROW
BEGIN
    DECLARE v_costo_hora DECIMAL(10,2);
    DECLARE v_horas_reserva DECIMAL(10,2);
    DECLARE v_costo_total DECIMAL(10,2);
    DECLARE v_nombre_area VARCHAR(100);

    -- Solo procesar si cambió el horario, fecha o área (ignorar cambios de estado)
    IF (NEW.hora_inicio != OLD.hora_inicio OR
        NEW.hora_fin != OLD.hora_fin OR
        NEW.fecha_reserva != OLD.fecha_reserva OR
        NEW.id_area != OLD.id_area) THEN

        -- Obtener el costo del área común
        SELECT costo_reserva, nombre INTO v_costo_hora, v_nombre_area
        FROM area_comun
        WHERE id_area = NEW.id_area;

        -- Calcular nuevas horas de reserva
        SET v_horas_reserva = TIMESTAMPDIFF(HOUR, CONCAT(NEW.fecha_reserva, ' ', NEW.hora_inicio), CONCAT(NEW.fecha_reserva, ' ', NEW.hora_fin));

        -- Si la diferencia es negativa (reserva que pasa de un día a otro), ajustar
        IF v_horas_reserva < 0 THEN
            SET v_horas_reserva = 24 + v_horas_reserva;
        END IF;

        -- Calcular nuevo costo total
        SET v_costo_total = v_costo_hora * v_horas_reserva;

        -- Actualizar el concepto existente (si existe)
        UPDATE conceptos
        SET monto = v_costo_total,
            cantidad = v_horas_reserva,
            descripcion = CONCAT('Reserva de ', v_nombre_area,
                                 ' - Fecha: ', NEW.fecha_reserva,
                                 ' - Horario: ', NEW.hora_inicio, ' a ', NEW.hora_fin,
                                 ' - ', v_horas_reserva, ' hora(s)',
                                 ' - Motivo: ', NEW.motivo)
        WHERE tipo_origen = 'reserva'
          AND id_origen = NEW.id_reserva
          AND id_persona = NEW.id_persona;

    END IF;
END//

DELIMITER ;

-- procedimiento para generar facturas por mes

DELIMITER //

CREATE PROCEDURE generar_facturas_todos_departamentos(
    IN p_periodo_facturado DATE
)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_id_departamento INT;
    DECLARE v_id_factura INT;
    DECLARE v_fecha_emision DATE;
    DECLARE v_fecha_vencimiento DATE;
    DECLARE v_mes_inicio DATE;
    DECLARE v_mes_fin DATE;
    DECLARE v_conceptos INT;
    DECLARE v_monto_total DECIMAL(10,2);

    DECLARE cur_departamentos CURSOR FOR
        SELECT id_departamento FROM departamento WHERE estado = 'ocupado';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    SET v_mes_inicio = p_periodo_facturado;
    SET v_mes_fin = LAST_DAY(p_periodo_facturado);
    SET v_fecha_emision = DATE_ADD(v_mes_fin, INTERVAL 1 DAY);
    SET v_fecha_vencimiento = DATE_ADD(v_mes_fin, INTERVAL 10 DAY);

    CREATE TEMPORARY TABLE IF NOT EXISTS resultados_facturacion (
                                                                    id_departamento INT,
                                                                    numero_departamento VARCHAR(10),
                                                                    id_factura INT,
                                                                    periodo_facturado VARCHAR(7),
                                                                    fecha_emision DATE,
                                                                    fecha_vencimiento DATE,
                                                                    monto_total DECIMAL(10,2),
                                                                    conceptos_incluidos INT,
                                                                    mensaje VARCHAR(255)
    );

    OPEN cur_departamentos;

    read_loop: LOOP
        FETCH cur_departamentos INTO v_id_departamento;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- 🔍 Primero: verificar si hay conceptos pendientes en el rango del mes
        SELECT COUNT(*) INTO v_conceptos
        FROM conceptos c
        WHERE c.id_factura IS NULL
          AND c.estado = 'pendiente'
          AND DATE(c.fecha_creacion) BETWEEN v_mes_inicio AND v_mes_fin
          AND c.id_persona IN (
            SELECT id_persona
            FROM tiene_departamento
            WHERE id_departamento = v_id_departamento
              AND estado = 'activo'
        );

        IF v_conceptos > 0 THEN
            -- ✅ Solo si hay conceptos, crear factura
            INSERT INTO factura (id_departamento, fecha_emision, fecha_vencimiento, monto_total, estado)
            VALUES (v_id_departamento, v_fecha_emision, v_fecha_vencimiento, 0, 'pendiente');
            SET v_id_factura = LAST_INSERT_ID();

            -- Vincular conceptos
            UPDATE conceptos c
            SET c.id_factura = v_id_factura, c.estado = 'facturado'
            WHERE c.id_factura IS NULL
              AND c.estado = 'pendiente'
              AND DATE(c.fecha_creacion) BETWEEN v_mes_inicio AND v_mes_fin
              AND c.id_persona IN (
                SELECT id_persona
                FROM tiene_departamento
                WHERE id_departamento = v_id_departamento
                  AND estado = 'activo'
            );

            -- Calcular total
            SELECT COALESCE(SUM(monto),0) INTO v_monto_total
            FROM conceptos
            WHERE id_factura = v_id_factura;

            UPDATE factura
            SET monto_total = v_monto_total
            WHERE id_factura = v_id_factura;

            INSERT INTO resultados_facturacion
            SELECT
                d.id_departamento,
                d.numero,
                v_id_factura,
                DATE_FORMAT(p_periodo_facturado, '%Y-%m'),
                v_fecha_emision,
                v_fecha_vencimiento,
                v_monto_total,
                v_conceptos,
                CONCAT('Factura generada con ', v_conceptos, ' conceptos')
            FROM departamento d
            WHERE d.id_departamento = v_id_departamento;

        ELSE
            -- ❌ No hay conceptos → no se crea factura
            INSERT INTO resultados_facturacion
            SELECT
                d.id_departamento,
                d.numero,
                NULL,
                DATE_FORMAT(p_periodo_facturado, '%Y-%m'),
                v_fecha_emision,
                v_fecha_vencimiento,
                0,
                0,
                'No hay conceptos pendientes para este mes'
            FROM departamento d
            WHERE d.id_departamento = v_id_departamento;
        END IF;

    END LOOP;

    CLOSE cur_departamentos;

    -- Mostrar resumen
    SELECT
        'RESUMEN FACTURACIÓN' as titulo,
        periodo_facturado as mes,
        COUNT(*) as total_departamentos,
        COUNT(CASE WHEN id_factura IS NOT NULL THEN 1 END) as facturas_generadas,
        COUNT(CASE WHEN id_factura IS NULL THEN 1 END) as sin_conceptos,
        SUM(monto_total) as total_recaudado,
        SUM(conceptos_incluidos) as total_conceptos
    FROM resultados_facturacion;

    -- Mostrar detalles
    SELECT
        numero_departamento as departamento,
        id_factura,
        monto_total,
        conceptos_incluidos,
        mensaje
    FROM resultados_facturacion
    ORDER BY numero_departamento;

    DROP TEMPORARY TABLE resultados_facturacion;
END//

DELIMITER ;


-- trigger para cuando se realiza la facturacion

DELIMITER //

CREATE TRIGGER after_concepto_facturado_actualiza_reserva
    AFTER UPDATE ON conceptos
    FOR EACH ROW
BEGIN
    -- Solo actúa cuando el concepto pasa a 'facturado' y proviene de una reserva
    IF OLD.estado = 'pendiente'
        AND NEW.estado = 'facturado'
        AND NEW.tipo_origen = 'reserva'
        AND NEW.id_origen IS NOT NULL THEN

        -- Actualiza la reserva asociada
        UPDATE reserva_area_comun
        SET estado = 'confirmada'
        WHERE id_reserva = NEW.id_origen
          AND estado = 'pendiente';  -- solo si aún no está confirmada ni cancelada
    END IF;
END//

DELIMITER ;