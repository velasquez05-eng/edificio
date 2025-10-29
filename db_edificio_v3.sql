-- phpMyAdmin SQL Dump
-- version 5.2.2deb1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 28-10-2025 a las 20:40:07
-- Versión del servidor: 11.8.3-MariaDB-0+deb13u1 from Debian
-- Versión de PHP: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_edificio_v3`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `generar_facturas_todos_departamentos` (IN `p_periodo_facturado` DATE)   BEGIN
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
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alerta_predictiva`
--

CREATE TABLE `alerta_predictiva` (
  `id_alerta` int(11) NOT NULL,
  `id_departamento` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `tipo_alerta` enum('riesgo_corte','corte') NOT NULL,
  `facturas_vencidas` int(11) NOT NULL DEFAULT 0,
  `fecha_alerta` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `area_comun`
--

CREATE TABLE `area_comun` (
  `id_area` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `capacidad` int(11) DEFAULT NULL,
  `costo_reserva` decimal(10,2) NOT NULL,
  `fecha_inicio_mantenimiento` datetime DEFAULT NULL,
  `fecha_fin_mantenimiento` datetime DEFAULT NULL,
  `estado` enum('disponible','mantenimiento','no disponible','eliminado') DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `area_comun`
--

INSERT INTO `area_comun` (`id_area`, `nombre`, `descripcion`, `capacidad`, `costo_reserva`, `fecha_inicio_mantenimiento`, `fecha_fin_mantenimiento`, `estado`) VALUES
(1, 'Sala de Eventos', 'Área para eventos sociales', 50, 100.00, NULL, NULL, 'disponible'),
(2, 'Piscina', 'Piscina climatizada', 20, 50.00, NULL, NULL, 'disponible'),
(3, 'Gimnasio', 'Gimnasio equipado', 15, 30.00, NULL, NULL, 'disponible');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos_fijos`
--

CREATE TABLE `cargos_fijos` (
  `id_cargo` int(11) NOT NULL,
  `nombre_cargo` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `cargos_fijos`
--

INSERT INTO `cargos_fijos` (`id_cargo`, `nombre_cargo`, `monto`, `descripcion`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Jardineria', 20.00, 'mantenimineto de jardin', 'activo', '2025-10-28 18:58:10', '2025-10-28 18:58:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunicado`
--

CREATE TABLE `comunicado` (
  `id_comunicado` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `contenido` text NOT NULL,
  `fecha_publicacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_expiracion` date DEFAULT NULL,
  `prioridad` enum('baja','media','alta','urgente') DEFAULT 'media',
  `estado` enum('borrador','publicado','archivado') DEFAULT 'borrador',
  `tipo_audiencia` enum('Todos','Residente','Personal') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conceptos`
--

CREATE TABLE `conceptos` (
  `id_concepto` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `id_factura` int(11) DEFAULT NULL,
  `concepto` enum('agua','luz','gas','mantenimiento','reserva_area','incidente','multa','otros') NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `id_origen` int(11) DEFAULT NULL,
  `tipo_origen` enum('reserva','consumo','incidente','mantenimiento','multa','otros') DEFAULT NULL,
  `cantidad` int(11) DEFAULT 1,
  `descripcion` text NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `estado` enum('pendiente','facturado','cancelado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `conceptos`
--

INSERT INTO `conceptos` (`id_concepto`, `id_persona`, `id_factura`, `concepto`, `monto`, `id_origen`, `tipo_origen`, `cantidad`, `descripcion`, `fecha_creacion`, `estado`) VALUES
(1, 2, 1, 'agua', 48.66, 1, 'consumo', 1, 'Consumo de AGUA - Periodo: 01/10/2025 al 31/10/2025 - Total: 24.33 m³ x Bs. 2.00 = Bs. 48.66', '2025-10-28 18:56:36', 'facturado'),
(2, 2, 1, 'luz', 57.60, 2, 'consumo', 1, 'Consumo de LUZ - Periodo: 01/10/2025 al 31/10/2025 - Total: 28.80 kWh x Bs. 2.00 = Bs. 57.60', '2025-10-28 18:56:36', 'facturado'),
(3, 2, 1, 'gas', 20.70, 3, 'consumo', 1, 'Consumo de GAS - Periodo: 01/10/2025 al 31/10/2025 - Total: 0.92 m³ x Bs. 22.50 = Bs. 20.70', '2025-10-28 18:56:36', 'facturado'),
(4, 5, 2, 'agua', 46.66, 4, 'consumo', 1, 'Consumo de AGUA - Periodo: 01/10/2025 al 31/10/2025 - Total: 23.33 m³ x Bs. 2.00 = Bs. 46.66', '2025-10-28 18:56:36', 'facturado'),
(5, 5, 2, 'luz', 71.12, 5, 'consumo', 1, 'Consumo de LUZ - Periodo: 01/10/2025 al 31/10/2025 - Total: 35.56 kWh x Bs. 2.00 = Bs. 71.12', '2025-10-28 18:56:36', 'facturado'),
(6, 5, 2, 'gas', 21.38, 6, 'consumo', 1, 'Consumo de GAS - Periodo: 01/10/2025 al 31/10/2025 - Total: 0.95 m³ x Bs. 22.50 = Bs. 21.38', '2025-10-28 18:56:36', 'facturado'),
(7, 6, 3, 'agua', 44.38, 7, 'consumo', 1, 'Consumo de AGUA - Periodo: 01/10/2025 al 31/10/2025 - Total: 22.19 m³ x Bs. 2.00 = Bs. 44.38', '2025-10-28 18:56:36', 'facturado'),
(8, 6, 3, 'luz', 60.88, 8, 'consumo', 1, 'Consumo de LUZ - Periodo: 01/10/2025 al 31/10/2025 - Total: 30.44 kWh x Bs. 2.00 = Bs. 60.88', '2025-10-28 18:56:36', 'facturado'),
(9, 6, 3, 'gas', 23.40, 9, 'consumo', 1, 'Consumo de GAS - Periodo: 01/10/2025 al 31/10/2025 - Total: 1.04 m³ x Bs. 22.50 = Bs. 23.40', '2025-10-28 18:56:36', 'facturado'),
(10, 7, 4, 'agua', 52.94, 10, 'consumo', 1, 'Consumo de AGUA - Periodo: 01/10/2025 al 31/10/2025 - Total: 26.47 m³ x Bs. 2.00 = Bs. 52.94', '2025-10-28 18:56:36', 'facturado'),
(11, 7, 4, 'luz', 72.10, 11, 'consumo', 1, 'Consumo de LUZ - Periodo: 01/10/2025 al 31/10/2025 - Total: 36.05 kWh x Bs. 2.00 = Bs. 72.10', '2025-10-28 18:56:36', 'facturado'),
(12, 7, 4, 'gas', 21.83, 12, 'consumo', 1, 'Consumo de GAS - Periodo: 01/10/2025 al 31/10/2025 - Total: 0.97 m³ x Bs. 22.50 = Bs. 21.83', '2025-10-28 18:56:36', 'facturado'),
(13, 2, 1, 'mantenimiento', 20.00, 1, 'mantenimiento', 1, 'Jardineria - October 2025', '2025-10-28 18:58:31', 'facturado'),
(14, 5, 2, 'mantenimiento', 20.00, 1, 'mantenimiento', 1, 'Jardineria - October 2025', '2025-10-28 18:58:31', 'facturado'),
(15, 6, 3, 'mantenimiento', 20.00, 1, 'mantenimiento', 1, 'Jardineria - October 2025', '2025-10-28 18:58:31', 'facturado'),
(16, 7, 4, 'mantenimiento', 20.00, 1, 'mantenimiento', 1, 'Jardineria - October 2025', '2025-10-28 18:58:31', 'facturado');

--
-- Disparadores `conceptos`
--
DELIMITER $$
CREATE TRIGGER `after_concepto_facturado_actualiza_reserva` AFTER UPDATE ON `conceptos` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE `departamento` (
  `id_departamento` int(11) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `piso` int(11) NOT NULL,
  `estado` enum('ocupado','disponible') DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `departamento`
--

INSERT INTO `departamento` (`id_departamento`, `numero`, `piso`, `estado`) VALUES
(1, '101', 1, 'ocupado'),
(2, '102', 1, 'ocupado'),
(3, '201', 2, 'ocupado'),
(4, '202', 2, 'ocupado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `id_factura` int(11) NOT NULL,
  `id_departamento` int(11) NOT NULL,
  `fecha_emision` date NOT NULL DEFAULT curdate(),
  `fecha_vencimiento` date NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','pagada','vencida') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`id_factura`, `id_departamento`, `fecha_emision`, `fecha_vencimiento`, `monto_total`, `estado`) VALUES
(1, 1, '2025-11-01', '2025-11-10', 146.96, 'pagada'),
(2, 2, '2025-11-01', '2025-11-10', 159.16, 'pagada'),
(3, 3, '2025-11-01', '2025-11-10', 148.66, 'pagada'),
(4, 4, '2025-11-01', '2025-11-10', 166.87, 'pagada');

--
-- Disparadores `factura`
--
DELIMITER $$
CREATE TRIGGER `after_factura_pagada_qr` AFTER UPDATE ON `factura` FOR EACH ROW BEGIN
    DECLARE v_fecha_vencimiento DATE;
    DECLARE v_puntualidad VARCHAR(20);
    DECLARE v_dias_retraso INT;
    DECLARE v_id_persona INT;
    DECLARE v_existe_pago_normal INT;

    -- Solo ejecutar si el estado cambió a 'pagada'
    IF NEW.estado = 'pagada' AND OLD.estado != 'pagada' THEN

        -- Verificar si ya existe un pago normal registrado en historial_pago
        -- Usar fecha_pago en lugar de created_at
        SELECT COUNT(*) INTO v_existe_pago_normal
        FROM historial_pago 
        WHERE id_factura = NEW.id_factura 
        AND fecha_pago >= DATE_SUB(NOW(), INTERVAL 5 MINUTE);

        -- Solo proceder si NO hay pagos normales recientes (es un pago QR directo)
        IF v_existe_pago_normal = 0 THEN

            -- Obtener fecha de vencimiento
            SET v_fecha_vencimiento = NEW.fecha_vencimiento;

            -- Obtener ID de persona del departamento
            SELECT td.id_persona INTO v_id_persona
            FROM tiene_departamento td
            WHERE td.id_departamento = NEW.id_departamento
              AND td.estado = 'activo'
            LIMIT 1;

            -- Si no encontramos persona, usar un valor por defecto
            IF v_id_persona IS NULL THEN
                SET v_id_persona = 0;
            END IF;

            -- Calcular días de retraso (usando fecha actual como fecha de pago)
            SET v_dias_retraso = DATEDIFF(CURDATE(), v_fecha_vencimiento);

            -- Determinar puntualidad
            IF CURDATE() <= v_fecha_vencimiento THEN
                SET v_puntualidad = 'a tiempo';
            ELSE
                SET v_puntualidad = 'atrasado';
            END IF;

            -- Registrar en historial como pago QR
            INSERT INTO historial_pago (id_factura, id_persona, monto_pagado, observacion)
            VALUES (
                       NEW.id_factura,
                       v_id_persona,
                       NEW.monto_total,
                       CONCAT(
                               'PAGO QR ', v_puntualidad,
                               ' | Factura #', NEW.id_factura,
                               ' | Monto: Bs', NEW.monto_total,
                               ' | Fecha Venc: ', DATE_FORMAT(v_fecha_vencimiento, '%d/%m/%Y'),
                               ' | Fecha Pago: ', DATE_FORMAT(CURDATE(), '%d/%m/%Y'),
                               CASE WHEN v_dias_retraso > 0 THEN CONCAT(' | Retraso: ', v_dias_retraso, ' días') ELSE '' END,
                               ' | Método: QR'
                       )
                   );

        END IF;
        
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_consumo`
--

CREATE TABLE `historial_consumo` (
  `id_historial_consumo` int(11) NOT NULL,
  `id_medidor` int(11) NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `consumo_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `historial_consumo`
--

INSERT INTO `historial_consumo` (`id_historial_consumo`, `id_medidor`, `fecha_inicio`, `fecha_fin`, `consumo_total`) VALUES
(1, 1, '2025-10-01 00:00:00', '2025-10-31 23:59:59', 24.33),
(2, 2, '2025-10-01 00:00:00', '2025-10-31 23:59:59', 28.80),
(3, 3, '2025-10-01 00:00:00', '2025-10-31 23:59:59', 0.92),
(4, 4, '2025-10-01 00:00:00', '2025-10-31 23:59:59', 23.33),
(5, 5, '2025-10-01 00:00:00', '2025-10-31 23:59:59', 35.56),
(6, 6, '2025-10-01 00:00:00', '2025-10-31 23:59:59', 0.95),
(7, 7, '2025-10-01 00:00:00', '2025-10-31 23:59:59', 22.19),
(8, 8, '2025-10-01 00:00:00', '2025-10-31 23:59:59', 30.44),
(9, 9, '2025-10-01 00:00:00', '2025-10-31 23:59:59', 1.04),
(10, 10, '2025-10-01 00:00:00', '2025-10-31 23:59:59', 26.47),
(11, 11, '2025-10-01 00:00:00', '2025-10-31 23:59:59', 36.05),
(12, 12, '2025-10-01 00:00:00', '2025-10-31 23:59:59', 0.97);

--
-- Disparadores `historial_consumo`
--
DELIMITER $$
CREATE TRIGGER `after_historial_consumo_insert` AFTER INSERT ON `historial_consumo` FOR EACH ROW BEGIN
    DECLARE v_id_departamento INT;
    DECLARE v_id_persona INT;
    DECLARE v_servicio_nombre VARCHAR(50);
    DECLARE v_unidad_medida VARCHAR(50);
    DECLARE v_costo_unitario DECIMAL(10,2);
    DECLARE v_monto_total DECIMAL(10,2);
    DECLARE v_descripcion TEXT;

    -- Obtener información del departamento y servicio
    SELECT
        m.id_departamento,
        s.nombre,
        s.unidad_medida,
        s.costo_unitario,
        (NEW.consumo_total * s.costo_unitario) as monto_calculado
    INTO
        v_id_departamento,
        v_servicio_nombre,
        v_unidad_medida,
        v_costo_unitario,
        v_monto_total
    FROM medidor m
             JOIN servicio s ON m.id_servicio = s.id_servicio
    WHERE m.id_medidor = NEW.id_medidor;

    -- Obtener el ID de la persona principal del departamento
    SELECT td.id_persona INTO v_id_persona
    FROM tiene_departamento td
    WHERE td.id_departamento = v_id_departamento
      AND td.estado = 'activo'
    LIMIT 1;

    -- Si no encuentra persona activa, buscar cualquier persona del departamento
    IF v_id_persona IS NULL THEN
        SELECT td.id_persona INTO v_id_persona
        FROM tiene_departamento td
        WHERE td.id_departamento = v_id_departamento
        LIMIT 1;
    END IF;

    -- Construir la descripción
    SET v_descripcion = CONCAT(
            'Consumo de ',
            UPPER(v_servicio_nombre),
            ' - Periodo: ',
            DATE_FORMAT(NEW.fecha_inicio, '%d/%m/%Y'),
            ' al ',
            DATE_FORMAT(NEW.fecha_fin, '%d/%m/%Y'),
            ' - Total: ',
            NEW.consumo_total,
            ' ',
            v_unidad_medida,
            ' x Bs. ',
            v_costo_unitario,
            ' = Bs. ',
            v_monto_total
                        );

    -- Insertar el concepto en la tabla conceptos
    INSERT INTO conceptos (
        id_persona,
        id_factura,  -- Se dejará NULL hasta que se cree la factura
        concepto,
        monto,
        id_origen,
        tipo_origen,
        cantidad,
        descripcion,
        fecha_creacion,
        estado
    ) VALUES (
                 v_id_persona,
                 NULL,  -- id_factura se asignará cuando se genere la factura
                 v_servicio_nombre,  -- agua, luz, gas
                 v_monto_total,
                 NEW.id_historial_consumo,  -- Referencia al registro de historial
                 'consumo',  -- Tipo de origen
                 1,
                 v_descripcion,
                 NOW(),
                 'pendiente'  -- Estado hasta que se facture
             );

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_incidente`
--

CREATE TABLE `historial_incidente` (
  `id_historial_incidente` int(11) NOT NULL,
  `id_incidente` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `accion` enum('creacion','asignacion','inicio_atencion','actualizacion','resolucion','cancelacion','reasignacion') NOT NULL,
  `observacion` text DEFAULT NULL,
  `estado_anterior` enum('pendiente','en_proceso','resuelto','cancelado') DEFAULT NULL,
  `estado_nuevo` enum('pendiente','en_proceso','resuelto','cancelado') DEFAULT NULL,
  `fecha_accion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_login`
--

CREATE TABLE `historial_login` (
  `id_historial_login` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` enum('exitoso','fallido') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `historial_login`
--

INSERT INTO `historial_login` (`id_historial_login`, `id_persona`, `fecha`, `estado`) VALUES
(1, 1, '2025-10-28 18:55:43', 'exitoso'),
(2, 2, '2025-10-28 19:11:15', 'exitoso'),
(3, 2, '2025-10-28 19:12:35', 'exitoso'),
(4, 1, '2025-10-28 19:36:31', 'exitoso'),
(5, 2, '2025-10-28 19:44:44', 'exitoso'),
(6, 1, '2025-10-28 19:47:01', 'exitoso'),
(7, 2, '2025-10-28 19:53:50', 'exitoso'),
(8, 1, '2025-10-28 20:08:32', 'exitoso'),
(9, 1, '2025-10-28 20:17:40', 'exitoso'),
(10, 2, '2025-10-28 20:24:40', 'exitoso'),
(11, 1, '2025-10-28 20:37:01', 'exitoso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_pago`
--

CREATE TABLE `historial_pago` (
  `id_historial_pago` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `monto_pagado` decimal(10,2) NOT NULL,
  `fecha_pago` datetime NOT NULL DEFAULT current_timestamp(),
  `observacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `historial_pago`
--

INSERT INTO `historial_pago` (`id_historial_pago`, `id_factura`, `id_persona`, `monto_pagado`, `fecha_pago`, `observacion`) VALUES
(1, 4, 7, 166.87, '2025-10-28 18:59:26', 'PAGO QR a tiempo | Factura #4 | Monto: $166.87 | Fecha Venc: 10/11/2025 | Fecha Pago: 28/10/2025 | Método: QR'),
(2, 3, 6, 148.66, '2025-10-28 19:23:41', 'PAGO a tiempo | Factura #3 | Monto: $148.66 | Fecha Venc: 10/11/2025 | Fecha Pago: 28/10/2025'),
(5, 2, 5, 159.16, '2025-10-28 19:31:11', 'PAGO a tiempo | Factura #2 | Monto: $159.16 | Fecha Venc: 10/11/2025 | Fecha Pago: 28/10/2025'),
(6, 1, 2, 146.96, '2026-01-22 19:31:51', 'PAGO QR a tiempo | Factura #1 | Monto: Bs146.96 | Fecha Venc: 10/11/2025 | Fecha Pago: 28/10/2025 | Método: QR');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidente`
--

CREATE TABLE `incidente` (
  `id_incidente` int(11) NOT NULL,
  `id_departamento` int(11) NOT NULL,
  `id_residente` int(11) NOT NULL,
  `id_creador` int(11) NOT NULL,
  `tipo` enum('interno','externo') DEFAULT 'interno',
  `descripcion` text NOT NULL,
  `descripcion_detallada` text DEFAULT NULL,
  `costo_externo` decimal(10,2) DEFAULT 0.00,
  `id_area` int(11) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','en_proceso','resuelto','cancelado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Disparadores `incidente`
--
DELIMITER $$
CREATE TRIGGER `after_incidente_insert` AFTER INSERT ON `incidente` FOR EACH ROW BEGIN
    INSERT INTO historial_incidente (
        id_incidente,
        id_persona,
        accion,
        observacion,
        estado_anterior,
        estado_nuevo
    ) VALUES (
                 NEW.id_incidente,
                 NEW.id_creador,
                 'creacion',
                 CONCAT('Incidente creado: ', NEW.descripcion),
                 NULL,
                 NEW.estado
             );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_incidente_update` AFTER UPDATE ON `incidente` FOR EACH ROW BEGIN
    DECLARE persona_accion INT;
    DECLARE concepto_descripcion TEXT;

    -- Solo procesar si el estado cambió y es resolución o cancelación
    IF OLD.estado != NEW.estado AND NEW.estado IN ('resuelto', 'cancelado') THEN

        -- Verificar si hay una variable de sesión definida
        IF @usuario_actual IS NOT NULL THEN
            SET persona_accion = @usuario_actual;
        ELSE
            -- Si no hay variable de sesión, usar lógica por defecto
            IF NEW.estado = 'cancelado' THEN
                SET persona_accion = 1; -- Administrador por defecto
            ELSE
                SELECT ia.id_personal INTO persona_accion
                FROM incidente_asignado ia
                WHERE ia.id_incidente = NEW.id_incidente
                ORDER BY ia.id_asignacion DESC
                LIMIT 1;

                IF persona_accion IS NULL THEN
                    SET persona_accion = NEW.id_residente;
                END IF;
            END IF;
        END IF;

        INSERT INTO historial_incidente (
            id_incidente,
            id_persona,
            accion,
            observacion,
            estado_anterior,
            estado_nuevo
        ) VALUES (
                     NEW.id_incidente,
                     persona_accion,
                     IF(NEW.estado = 'resuelto', 'resolucion', 'cancelacion'),
                     CONCAT('Incidente ', NEW.estado, ': ', NEW.descripcion),
                     OLD.estado,
                     NEW.estado
                 );

        -- Limpiar variable de sesión después de usarla
        SET @usuario_actual = NULL;

        -- ???? GENERAR CONCEPTO PARA CUALQUIER INCIDENTE CON COSTO > 0 (INTERNO O EXTERNO)
        IF NEW.estado = 'resuelto' AND NEW.costo_externo > 0 THEN

            -- Determinar la descripción según el tipo de incidente
            IF NEW.tipo = 'externo' THEN
                SET concepto_descripcion = CONCAT('Costo por reparación externa: ', NEW.descripcion);
            ELSE
                SET concepto_descripcion = CONCAT('Costo por materiales/reparación interna: ', NEW.descripcion);
            END IF;

            INSERT INTO conceptos (
                id_persona,
                concepto,
                monto,
                id_origen,
                tipo_origen,
                descripcion,
                estado
            ) VALUES (
                         NEW.id_residente,
                         'incidente',
                         NEW.costo_externo,
                         NEW.id_incidente,
                         'incidente',
                         concepto_descripcion,
                         'pendiente'
                     );
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidente_asignado`
--

CREATE TABLE `incidente_asignado` (
  `id_asignacion` int(11) NOT NULL,
  `id_incidente` int(11) NOT NULL,
  `id_personal` int(11) NOT NULL,
  `fecha_asignacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_atencion` datetime DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `requiere_reasignacion` tinyint(1) DEFAULT 0,
  `comentario_reasignacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Disparadores `incidente_asignado`
--
DELIMITER $$
CREATE TRIGGER `after_incidente_asignado_insert` AFTER INSERT ON `incidente_asignado` FOR EACH ROW BEGIN
    INSERT INTO historial_incidente (
        id_incidente,
        id_persona,
        accion,
        observacion,
        estado_anterior,
        estado_nuevo
    ) VALUES (
                 NEW.id_incidente,
                 NEW.id_personal,
                 'asignacion',
                 CONCAT('Asignado para: ', COALESCE(NEW.observaciones, 'Sin observaciones')),
                 'pendiente',
                 'en_proceso'
             );

    UPDATE incidente SET estado = 'en_proceso' WHERE id_incidente = NEW.id_incidente;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_incidente_asignado_update` AFTER UPDATE ON `incidente_asignado` FOR EACH ROW BEGIN
    -- Inicio de atención
    IF OLD.fecha_atencion IS NULL AND NEW.fecha_atencion IS NOT NULL THEN
        INSERT INTO historial_incidente (
            id_incidente,
            id_persona,
            accion,
            observacion,
            estado_anterior,
            estado_nuevo
        ) VALUES (
                     NEW.id_incidente,
                     NEW.id_personal,
                     'inicio_atencion',
                     CONCAT('Atención iniciada: ', COALESCE(NEW.observaciones, 'Inicio de trabajo')),
                     'en_proceso',
                     'en_proceso'
                 );

        -- Reasignación de personal
    ELSEIF OLD.id_personal != NEW.id_personal THEN
        INSERT INTO historial_incidente (
            id_incidente,
            id_persona,
            accion,
            observacion,
            estado_anterior,
            estado_nuevo
        ) VALUES (
                     NEW.id_incidente,
                     NEW.id_personal,
                     'reasignacion',
                     CONCAT('Reasignado: ', COALESCE(NEW.observaciones, 'Reasignación completada')),
                     'en_proceso',
                     'en_proceso'
                 );

        -- Actualizaciones de progreso
    ELSEIF OLD.fecha_atencion IS NOT NULL AND NEW.observaciones != OLD.observaciones THEN
        INSERT INTO historial_incidente (
            id_incidente,
            id_persona,
            accion,
            observacion,
            estado_anterior,
            estado_nuevo
        ) VALUES (
                     NEW.id_incidente,
                     NEW.id_personal,
                     'actualizacion',
                     CONCAT('Progreso: ', COALESCE(NEW.observaciones, 'Actualización de trabajo')),
                     'en_proceso',
                     'en_proceso'
                 );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lector_sensor_consumo`
--

CREATE TABLE `lector_sensor_consumo` (
  `id_lectura` int(11) NOT NULL,
  `id_medidor` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `consumo` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `lector_sensor_consumo`
--

INSERT INTO `lector_sensor_consumo` (`id_lectura`, `id_medidor`, `fecha_hora`, `consumo`) VALUES
(1, 1, '2025-10-01 23:59:00', 0.72),
(2, 2, '2025-10-01 23:59:00', 0.74),
(3, 3, '2025-10-01 23:59:00', 0.05),
(4, 1, '2025-10-02 23:59:00', 0.14),
(5, 2, '2025-10-02 23:59:00', 0.17),
(6, 3, '2025-10-02 23:59:00', 0.01),
(7, 1, '2025-10-03 23:59:00', 1.12),
(8, 2, '2025-10-03 23:59:00', 1.46),
(9, 3, '2025-10-03 23:59:00', 0.03),
(10, 1, '2025-10-04 23:59:00', 0.94),
(11, 2, '2025-10-04 23:59:00', 1.38),
(12, 3, '2025-10-04 23:59:00', 0.04),
(13, 1, '2025-10-05 23:59:00', 0.18),
(14, 2, '2025-10-05 23:59:00', 0.56),
(15, 3, '2025-10-05 23:59:00', 0.03),
(16, 1, '2025-10-06 23:59:00', 1.02),
(17, 2, '2025-10-06 23:59:00', 0.10),
(18, 3, '2025-10-06 23:59:00', 0.04),
(19, 1, '2025-10-07 23:59:00', 1.42),
(20, 2, '2025-10-07 23:59:00', 0.88),
(21, 3, '2025-10-07 23:59:00', 0.05),
(22, 1, '2025-10-08 23:59:00', 1.37),
(23, 2, '2025-10-08 23:59:00', 1.58),
(24, 3, '2025-10-08 23:59:00', 0.03),
(25, 1, '2025-10-09 23:59:00', 0.80),
(26, 2, '2025-10-09 23:59:00', 0.53),
(27, 3, '2025-10-09 23:59:00', 0.01),
(28, 1, '2025-10-10 23:59:00', 0.35),
(29, 2, '2025-10-10 23:59:00', 0.34),
(30, 3, '2025-10-10 23:59:00', 0.01),
(31, 1, '2025-10-11 23:59:00', 0.13),
(32, 2, '2025-10-11 23:59:00', 0.63),
(33, 3, '2025-10-11 23:59:00', 0.01),
(34, 1, '2025-10-12 23:59:00', 1.30),
(35, 2, '2025-10-12 23:59:00', 0.61),
(36, 3, '2025-10-12 23:59:00', 0.04),
(37, 1, '2025-10-13 23:59:00', 1.33),
(38, 2, '2025-10-13 23:59:00', 0.98),
(39, 3, '2025-10-13 23:59:00', 0.01),
(40, 1, '2025-10-14 23:59:00', 0.62),
(41, 2, '2025-10-14 23:59:00', 0.87),
(42, 3, '2025-10-14 23:59:00', 0.04),
(43, 1, '2025-10-15 23:59:00', 0.39),
(44, 2, '2025-10-15 23:59:00', 1.24),
(45, 3, '2025-10-15 23:59:00', 0.04),
(46, 1, '2025-10-16 23:59:00', 1.27),
(47, 2, '2025-10-16 23:59:00', 0.92),
(48, 3, '2025-10-16 23:59:00', 0.05),
(49, 1, '2025-10-17 23:59:00', 1.45),
(50, 2, '2025-10-17 23:59:00', 1.24),
(51, 3, '2025-10-17 23:59:00', 0.02),
(52, 1, '2025-10-18 23:59:00', 1.05),
(53, 2, '2025-10-18 23:59:00', 1.63),
(54, 3, '2025-10-18 23:59:00', 0.05),
(55, 1, '2025-10-19 23:59:00', 0.76),
(56, 2, '2025-10-19 23:59:00', 0.79),
(57, 3, '2025-10-19 23:59:00', 0.03),
(58, 1, '2025-10-20 23:59:00', 0.22),
(59, 2, '2025-10-20 23:59:00', 0.84),
(60, 3, '2025-10-20 23:59:00', 0.03),
(61, 1, '2025-10-21 23:59:00', 0.55),
(62, 2, '2025-10-21 23:59:00', 0.38),
(63, 3, '2025-10-21 23:59:00', 0.02),
(64, 1, '2025-10-22 23:59:00', 1.43),
(65, 2, '2025-10-22 23:59:00', 1.42),
(66, 3, '2025-10-22 23:59:00', 0.01),
(67, 1, '2025-10-23 23:59:00', 0.46),
(68, 2, '2025-10-23 23:59:00', 1.15),
(69, 3, '2025-10-23 23:59:00', 0.04),
(70, 1, '2025-10-24 23:59:00', 1.21),
(71, 2, '2025-10-24 23:59:00', 1.52),
(72, 3, '2025-10-24 23:59:00', 0.03),
(73, 1, '2025-10-25 23:59:00', 0.18),
(74, 2, '2025-10-25 23:59:00', 0.49),
(75, 3, '2025-10-25 23:59:00', 0.04),
(76, 1, '2025-10-26 23:59:00', 1.14),
(77, 2, '2025-10-26 23:59:00', 1.06),
(78, 3, '2025-10-26 23:59:00', 0.02),
(79, 1, '2025-10-27 23:59:00', 0.46),
(80, 2, '2025-10-27 23:59:00', 1.36),
(81, 3, '2025-10-27 23:59:00', 0.02),
(82, 1, '2025-10-28 23:59:00', 0.86),
(83, 2, '2025-10-28 23:59:00', 1.61),
(84, 3, '2025-10-28 23:59:00', 0.01),
(85, 1, '2025-10-29 23:59:00', 0.31),
(86, 2, '2025-10-29 23:59:00', 1.49),
(87, 3, '2025-10-29 23:59:00', 0.05),
(88, 1, '2025-10-30 23:59:00', 0.72),
(89, 2, '2025-10-30 23:59:00', 0.22),
(90, 3, '2025-10-30 23:59:00', 0.03),
(91, 1, '2025-10-31 23:59:00', 0.43),
(92, 2, '2025-10-31 23:59:00', 0.61),
(93, 3, '2025-10-31 23:59:00', 0.03),
(94, 4, '2025-10-01 23:59:00', 0.90),
(95, 5, '2025-10-01 23:59:00', 1.95),
(96, 6, '2025-10-01 23:59:00', 0.05),
(97, 4, '2025-10-02 23:59:00', 1.01),
(98, 5, '2025-10-02 23:59:00', 1.17),
(99, 6, '2025-10-02 23:59:00', 0.04),
(100, 4, '2025-10-03 23:59:00', 0.43),
(101, 5, '2025-10-03 23:59:00', 0.24),
(102, 6, '2025-10-03 23:59:00', 0.01),
(103, 4, '2025-10-04 23:59:00', 0.22),
(104, 5, '2025-10-04 23:59:00', 0.60),
(105, 6, '2025-10-04 23:59:00', 0.01),
(106, 4, '2025-10-05 23:59:00', 1.04),
(107, 5, '2025-10-05 23:59:00', 1.26),
(108, 6, '2025-10-05 23:59:00', 0.01),
(109, 4, '2025-10-06 23:59:00', 0.44),
(110, 5, '2025-10-06 23:59:00', 0.27),
(111, 6, '2025-10-06 23:59:00', 0.01),
(112, 4, '2025-10-07 23:59:00', 1.14),
(113, 5, '2025-10-07 23:59:00', 1.70),
(114, 6, '2025-10-07 23:59:00', 0.02),
(115, 4, '2025-10-08 23:59:00', 0.70),
(116, 5, '2025-10-08 23:59:00', 1.37),
(117, 6, '2025-10-08 23:59:00', 0.05),
(118, 4, '2025-10-09 23:59:00', 0.71),
(119, 5, '2025-10-09 23:59:00', 1.43),
(120, 6, '2025-10-09 23:59:00', 0.01),
(121, 4, '2025-10-10 23:59:00', 1.07),
(122, 5, '2025-10-10 23:59:00', 0.27),
(123, 6, '2025-10-10 23:59:00', 0.02),
(124, 4, '2025-10-11 23:59:00', 0.92),
(125, 5, '2025-10-11 23:59:00', 1.44),
(126, 6, '2025-10-11 23:59:00', 0.03),
(127, 4, '2025-10-12 23:59:00', 0.15),
(128, 5, '2025-10-12 23:59:00', 1.92),
(129, 6, '2025-10-12 23:59:00', 0.05),
(130, 4, '2025-10-13 23:59:00', 0.93),
(131, 5, '2025-10-13 23:59:00', 1.36),
(132, 6, '2025-10-13 23:59:00', 0.05),
(133, 4, '2025-10-14 23:59:00', 1.25),
(134, 5, '2025-10-14 23:59:00', 1.34),
(135, 6, '2025-10-14 23:59:00', 0.03),
(136, 4, '2025-10-15 23:59:00', 1.35),
(137, 5, '2025-10-15 23:59:00', 0.38),
(138, 6, '2025-10-15 23:59:00', 0.01),
(139, 4, '2025-10-16 23:59:00', 1.11),
(140, 5, '2025-10-16 23:59:00', 0.70),
(141, 6, '2025-10-16 23:59:00', 0.05),
(142, 4, '2025-10-17 23:59:00', 0.75),
(143, 5, '2025-10-17 23:59:00', 1.03),
(144, 6, '2025-10-17 23:59:00', 0.05),
(145, 4, '2025-10-18 23:59:00', 0.34),
(146, 5, '2025-10-18 23:59:00', 1.27),
(147, 6, '2025-10-18 23:59:00', 0.04),
(148, 4, '2025-10-19 23:59:00', 0.30),
(149, 5, '2025-10-19 23:59:00', 1.71),
(150, 6, '2025-10-19 23:59:00', 0.02),
(151, 4, '2025-10-20 23:59:00', 0.33),
(152, 5, '2025-10-20 23:59:00', 1.42),
(153, 6, '2025-10-20 23:59:00', 0.03),
(154, 4, '2025-10-21 23:59:00', 0.51),
(155, 5, '2025-10-21 23:59:00', 1.70),
(156, 6, '2025-10-21 23:59:00', 0.05),
(157, 4, '2025-10-22 23:59:00', 0.95),
(158, 5, '2025-10-22 23:59:00', 1.91),
(159, 6, '2025-10-22 23:59:00', 0.03),
(160, 4, '2025-10-23 23:59:00', 1.17),
(161, 5, '2025-10-23 23:59:00', 0.15),
(162, 6, '2025-10-23 23:59:00', 0.04),
(163, 4, '2025-10-24 23:59:00', 1.39),
(164, 5, '2025-10-24 23:59:00', 0.91),
(165, 6, '2025-10-24 23:59:00', 0.04),
(166, 4, '2025-10-25 23:59:00', 0.14),
(167, 5, '2025-10-25 23:59:00', 1.58),
(168, 6, '2025-10-25 23:59:00', 0.04),
(169, 4, '2025-10-26 23:59:00', 0.75),
(170, 5, '2025-10-26 23:59:00', 1.61),
(171, 6, '2025-10-26 23:59:00', 0.02),
(172, 4, '2025-10-27 23:59:00', 0.94),
(173, 5, '2025-10-27 23:59:00', 1.56),
(174, 6, '2025-10-27 23:59:00', 0.02),
(175, 4, '2025-10-28 23:59:00', 0.67),
(176, 5, '2025-10-28 23:59:00', 0.12),
(177, 6, '2025-10-28 23:59:00', 0.01),
(178, 4, '2025-10-29 23:59:00', 0.19),
(179, 5, '2025-10-29 23:59:00', 0.12),
(180, 6, '2025-10-29 23:59:00', 0.04),
(181, 4, '2025-10-30 23:59:00', 0.51),
(182, 5, '2025-10-30 23:59:00', 1.82),
(183, 6, '2025-10-30 23:59:00', 0.05),
(184, 4, '2025-10-31 23:59:00', 1.02),
(185, 5, '2025-10-31 23:59:00', 1.25),
(186, 6, '2025-10-31 23:59:00', 0.02),
(187, 7, '2025-10-01 23:59:00', 0.22),
(188, 8, '2025-10-01 23:59:00', 0.75),
(189, 9, '2025-10-01 23:59:00', 0.04),
(190, 7, '2025-10-02 23:59:00', 0.54),
(191, 8, '2025-10-02 23:59:00', 0.79),
(192, 9, '2025-10-02 23:59:00', 0.05),
(193, 7, '2025-10-03 23:59:00', 0.32),
(194, 8, '2025-10-03 23:59:00', 1.92),
(195, 9, '2025-10-03 23:59:00', 0.04),
(196, 7, '2025-10-04 23:59:00', 0.54),
(197, 8, '2025-10-04 23:59:00', 1.21),
(198, 9, '2025-10-04 23:59:00', 0.01),
(199, 7, '2025-10-05 23:59:00', 0.24),
(200, 8, '2025-10-05 23:59:00', 1.57),
(201, 9, '2025-10-05 23:59:00', 0.03),
(202, 7, '2025-10-06 23:59:00', 1.13),
(203, 8, '2025-10-06 23:59:00', 1.91),
(204, 9, '2025-10-06 23:59:00', 0.05),
(205, 7, '2025-10-07 23:59:00', 0.90),
(206, 8, '2025-10-07 23:59:00', 1.13),
(207, 9, '2025-10-07 23:59:00', 0.05),
(208, 7, '2025-10-08 23:59:00', 0.34),
(209, 8, '2025-10-08 23:59:00', 0.48),
(210, 9, '2025-10-08 23:59:00', 0.01),
(211, 7, '2025-10-09 23:59:00', 1.26),
(212, 8, '2025-10-09 23:59:00', 0.28),
(213, 9, '2025-10-09 23:59:00', 0.04),
(214, 7, '2025-10-10 23:59:00', 0.76),
(215, 8, '2025-10-10 23:59:00', 0.32),
(216, 9, '2025-10-10 23:59:00', 0.02),
(217, 7, '2025-10-11 23:59:00', 0.41),
(218, 8, '2025-10-11 23:59:00', 1.41),
(219, 9, '2025-10-11 23:59:00', 0.04),
(220, 7, '2025-10-12 23:59:00', 0.59),
(221, 8, '2025-10-12 23:59:00', 0.78),
(222, 9, '2025-10-12 23:59:00', 0.05),
(223, 7, '2025-10-13 23:59:00', 1.19),
(224, 8, '2025-10-13 23:59:00', 0.49),
(225, 9, '2025-10-13 23:59:00', 0.05),
(226, 7, '2025-10-14 23:59:00', 0.95),
(227, 8, '2025-10-14 23:59:00', 0.63),
(228, 9, '2025-10-14 23:59:00', 0.05),
(229, 7, '2025-10-15 23:59:00', 0.22),
(230, 8, '2025-10-15 23:59:00', 0.29),
(231, 9, '2025-10-15 23:59:00', 0.01),
(232, 7, '2025-10-16 23:59:00', 0.70),
(233, 8, '2025-10-16 23:59:00', 0.48),
(234, 9, '2025-10-16 23:59:00', 0.03),
(235, 7, '2025-10-17 23:59:00', 0.87),
(236, 8, '2025-10-17 23:59:00', 0.21),
(237, 9, '2025-10-17 23:59:00', 0.05),
(238, 7, '2025-10-18 23:59:00', 0.38),
(239, 8, '2025-10-18 23:59:00', 1.21),
(240, 9, '2025-10-18 23:59:00', 0.01),
(241, 7, '2025-10-19 23:59:00', 0.58),
(242, 8, '2025-10-19 23:59:00', 0.69),
(243, 9, '2025-10-19 23:59:00', 0.04),
(244, 7, '2025-10-20 23:59:00', 1.42),
(245, 8, '2025-10-20 23:59:00', 1.88),
(246, 9, '2025-10-20 23:59:00', 0.01),
(247, 7, '2025-10-21 23:59:00', 1.46),
(248, 8, '2025-10-21 23:59:00', 1.49),
(249, 9, '2025-10-21 23:59:00', 0.05),
(250, 7, '2025-10-22 23:59:00', 0.76),
(251, 8, '2025-10-22 23:59:00', 0.99),
(252, 9, '2025-10-22 23:59:00', 0.02),
(253, 7, '2025-10-23 23:59:00', 1.07),
(254, 8, '2025-10-23 23:59:00', 0.24),
(255, 9, '2025-10-23 23:59:00', 0.01),
(256, 7, '2025-10-24 23:59:00', 1.29),
(257, 8, '2025-10-24 23:59:00', 1.85),
(258, 9, '2025-10-24 23:59:00', 0.01),
(259, 7, '2025-10-25 23:59:00', 1.40),
(260, 8, '2025-10-25 23:59:00', 0.10),
(261, 9, '2025-10-25 23:59:00', 0.02),
(262, 7, '2025-10-26 23:59:00', 0.77),
(263, 8, '2025-10-26 23:59:00', 1.42),
(264, 9, '2025-10-26 23:59:00', 0.03),
(265, 7, '2025-10-27 23:59:00', 0.23),
(266, 8, '2025-10-27 23:59:00', 0.59),
(267, 9, '2025-10-27 23:59:00', 0.05),
(268, 7, '2025-10-28 23:59:00', 0.10),
(269, 8, '2025-10-28 23:59:00', 1.26),
(270, 9, '2025-10-28 23:59:00', 0.05),
(271, 7, '2025-10-29 23:59:00', 0.27),
(272, 8, '2025-10-29 23:59:00', 1.99),
(273, 9, '2025-10-29 23:59:00', 0.04),
(274, 7, '2025-10-30 23:59:00', 0.76),
(275, 8, '2025-10-30 23:59:00', 1.74),
(276, 9, '2025-10-30 23:59:00', 0.03),
(277, 7, '2025-10-31 23:59:00', 0.52),
(278, 8, '2025-10-31 23:59:00', 0.34),
(279, 9, '2025-10-31 23:59:00', 0.05),
(280, 10, '2025-10-01 23:59:00', 0.77),
(281, 11, '2025-10-01 23:59:00', 1.05),
(282, 12, '2025-10-01 23:59:00', 0.05),
(283, 10, '2025-10-02 23:59:00', 1.45),
(284, 11, '2025-10-02 23:59:00', 1.16),
(285, 12, '2025-10-02 23:59:00', 0.05),
(286, 10, '2025-10-03 23:59:00', 1.38),
(287, 11, '2025-10-03 23:59:00', 1.60),
(288, 12, '2025-10-03 23:59:00', 0.01),
(289, 10, '2025-10-04 23:59:00', 1.20),
(290, 11, '2025-10-04 23:59:00', 1.56),
(291, 12, '2025-10-04 23:59:00', 0.05),
(292, 10, '2025-10-05 23:59:00', 1.30),
(293, 11, '2025-10-05 23:59:00', 1.62),
(294, 12, '2025-10-05 23:59:00', 0.05),
(295, 10, '2025-10-06 23:59:00', 0.76),
(296, 11, '2025-10-06 23:59:00', 1.25),
(297, 12, '2025-10-06 23:59:00', 0.01),
(298, 10, '2025-10-07 23:59:00', 1.03),
(299, 11, '2025-10-07 23:59:00', 0.53),
(300, 12, '2025-10-07 23:59:00', 0.03),
(301, 10, '2025-10-08 23:59:00', 1.47),
(302, 11, '2025-10-08 23:59:00', 0.87),
(303, 12, '2025-10-08 23:59:00', 0.01),
(304, 10, '2025-10-09 23:59:00', 0.86),
(305, 11, '2025-10-09 23:59:00', 0.98),
(306, 12, '2025-10-09 23:59:00', 0.02),
(307, 10, '2025-10-10 23:59:00', 0.74),
(308, 11, '2025-10-10 23:59:00', 1.80),
(309, 12, '2025-10-10 23:59:00', 0.04),
(310, 10, '2025-10-11 23:59:00', 0.92),
(311, 11, '2025-10-11 23:59:00', 0.11),
(312, 12, '2025-10-11 23:59:00', 0.01),
(313, 10, '2025-10-12 23:59:00', 1.13),
(314, 11, '2025-10-12 23:59:00', 1.07),
(315, 12, '2025-10-12 23:59:00', 0.05),
(316, 10, '2025-10-13 23:59:00', 0.76),
(317, 11, '2025-10-13 23:59:00', 1.11),
(318, 12, '2025-10-13 23:59:00', 0.01),
(319, 10, '2025-10-14 23:59:00', 1.28),
(320, 11, '2025-10-14 23:59:00', 0.20),
(321, 12, '2025-10-14 23:59:00', 0.05),
(322, 10, '2025-10-15 23:59:00', 0.47),
(323, 11, '2025-10-15 23:59:00', 1.74),
(324, 12, '2025-10-15 23:59:00', 0.02),
(325, 10, '2025-10-16 23:59:00', 0.72),
(326, 11, '2025-10-16 23:59:00', 0.16),
(327, 12, '2025-10-16 23:59:00', 0.05),
(328, 10, '2025-10-17 23:59:00', 0.30),
(329, 11, '2025-10-17 23:59:00', 1.63),
(330, 12, '2025-10-17 23:59:00', 0.01),
(331, 10, '2025-10-18 23:59:00', 0.74),
(332, 11, '2025-10-18 23:59:00', 1.62),
(333, 12, '2025-10-18 23:59:00', 0.04),
(334, 10, '2025-10-19 23:59:00', 0.38),
(335, 11, '2025-10-19 23:59:00', 0.46),
(336, 12, '2025-10-19 23:59:00', 0.05),
(337, 10, '2025-10-20 23:59:00', 1.24),
(338, 11, '2025-10-20 23:59:00', 0.76),
(339, 12, '2025-10-20 23:59:00', 0.02),
(340, 10, '2025-10-21 23:59:00', 0.58),
(341, 11, '2025-10-21 23:59:00', 1.67),
(342, 12, '2025-10-21 23:59:00', 0.01),
(343, 10, '2025-10-22 23:59:00', 0.53),
(344, 11, '2025-10-22 23:59:00', 1.96),
(345, 12, '2025-10-22 23:59:00', 0.03),
(346, 10, '2025-10-23 23:59:00', 1.17),
(347, 11, '2025-10-23 23:59:00', 1.61),
(348, 12, '2025-10-23 23:59:00', 0.01),
(349, 10, '2025-10-24 23:59:00', 1.16),
(350, 11, '2025-10-24 23:59:00', 1.29),
(351, 12, '2025-10-24 23:59:00', 0.04),
(352, 10, '2025-10-25 23:59:00', 0.79),
(353, 11, '2025-10-25 23:59:00', 0.26),
(354, 12, '2025-10-25 23:59:00', 0.04),
(355, 10, '2025-10-26 23:59:00', 1.32),
(356, 11, '2025-10-26 23:59:00', 1.57),
(357, 12, '2025-10-26 23:59:00', 0.03),
(358, 10, '2025-10-27 23:59:00', 0.39),
(359, 11, '2025-10-27 23:59:00', 1.95),
(360, 12, '2025-10-27 23:59:00', 0.05),
(361, 10, '2025-10-28 23:59:00', 0.97),
(362, 11, '2025-10-28 23:59:00', 1.32),
(363, 12, '2025-10-28 23:59:00', 0.03),
(364, 10, '2025-10-29 23:59:00', 0.42),
(365, 11, '2025-10-29 23:59:00', 1.93),
(366, 12, '2025-10-29 23:59:00', 0.03),
(367, 10, '2025-10-30 23:59:00', 0.13),
(368, 11, '2025-10-30 23:59:00', 0.37),
(369, 12, '2025-10-30 23:59:00', 0.02),
(370, 10, '2025-10-31 23:59:00', 0.11),
(371, 11, '2025-10-31 23:59:00', 0.84),
(372, 12, '2025-10-31 23:59:00', 0.05);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medidor`
--

CREATE TABLE `medidor` (
  `id_medidor` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `id_departamento` int(11) NOT NULL,
  `fecha_instalacion` date DEFAULT curdate(),
  `estado` enum('activo','mantenimiento','baja','corte') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `medidor`
--

INSERT INTO `medidor` (`id_medidor`, `codigo`, `id_servicio`, `id_departamento`, `fecha_instalacion`, `estado`) VALUES
(1, 'AGUA-101-001', 1, 1, '2024-01-15', 'activo'),
(2, 'LUZ-101-001', 2, 1, '2024-01-15', 'activo'),
(3, 'GAS-101-001', 3, 1, '2024-01-15', 'activo'),
(4, 'AGUA-102-001', 1, 2, '2024-01-15', 'activo'),
(5, 'LUZ-102-001', 2, 2, '2024-01-15', 'activo'),
(6, 'GAS-102-001', 3, 2, '2024-01-15', 'activo'),
(7, 'AGUA-201-001', 1, 3, '2024-01-15', 'activo'),
(8, 'LUZ-201-001', 2, 3, '2024-01-15', 'activo'),
(9, 'GAS-201-001', 3, 3, '2024-01-15', 'activo'),
(10, 'AGUA-202-001', 1, 4, '2024-01-15', 'activo'),
(11, 'LUZ-202-001', 2, 4, '2024-01-15', 'activo'),
(12, 'GAS-202-001', 3, 4, '2024-01-15', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion_persona`
--

CREATE TABLE `notificacion_persona` (
  `id_alerta` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `fecha_envio` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('enviado','recibido','leído') DEFAULT 'enviado',
  `medio` enum('email','sms','app') DEFAULT 'email',
  `observacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `id_persona` int(11) NOT NULL,
  `nombre` varbinary(255) NOT NULL,
  `apellido_paterno` varbinary(255) NOT NULL,
  `apellido_materno` varbinary(255) DEFAULT NULL,
  `ci` varbinary(255) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `verificado` tinyint(1) DEFAULT 0,
  `tiempo_verificacion` datetime DEFAULT NULL,
  `codigo_recuperacion` varchar(6) DEFAULT NULL,
  `tiempo_codigo_recuperacion` datetime DEFAULT NULL,
  `intentos_fallidos` int(11) DEFAULT 0,
  `tiempo_bloqueo` datetime DEFAULT NULL,
  `fecha_eliminado` datetime DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `id_rol` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`id_persona`, `nombre`, `apellido_paterno`, `apellido_materno`, `ci`, `telefono`, `email`, `username`, `password_hash`, `verificado`, `tiempo_verificacion`, `codigo_recuperacion`, `tiempo_codigo_recuperacion`, `intentos_fallidos`, `tiempo_bloqueo`, `fecha_eliminado`, `estado`, `id_rol`) VALUES
(1, 0x4a45592b4551656742756b664565696d497a616f5a416f4a557a507444315a67754338555a37476f526c513d, 0x4a55544676386276656d4b6c3531573577375661326c37634b716a724f547758396e5043707245726f74383d, 0x6267646a6b4d4841396c6c38797073704e59734e7174616d714f4936486a37554343312b3046477a49586f3d, 0x6a4256465830577452625a396b775a35392b7636794757776b35546e4c344e4f716e59464e4b734a7055383d, '77777777', 'admin@sistema.com', 'admin', '$2y$12$hCu7kbLTRIPDzm81i8QAxOGLz98HxC8q2NL738tUTNO49iHktpCNS', 1, '2025-10-31 18:55:09', NULL, NULL, 0, NULL, NULL, 'activo', 1),
(2, 0x43553737392f547052754b444b66766f582f44554e786278677962457873646143454d5377617837566d303d, 0x5975355367775a3062416b554749756a6c6a315155744e2f50563838726f4450454f636a616e39316163343d, 0x2f3177574434794761576261715a2f4765643769615a6833647057697a375247423061765657394b6b41493d, 0x42516162724b64636754716c2b62754e52425279706d55367451426147652f634d6766392b777743334a733d, '77777771', 'maria@residente.com', 'maria', '$2y$12$TQRoiD0ZcRESkRLcFE3iqubepbA0e7KXTkh/lPxD.awTFBtBy.6Ze', 1, '2025-10-31 18:55:09', NULL, NULL, 0, NULL, NULL, 'activo', 2),
(3, 0x7678776e746f34317134784961476d2f5a4e2f7170632f615879705955414b547132427a414d2f387446513d, 0x4b556b387735706a516f5467416672624c723338713141566242454c746e6e4c7477775a714371575966633d, 0x7257314f727579654f6175725833374a4352596368796f536c4d54705642766a723350694f58656e6c30493d, 0x4344583232446f4935424267337465793361597a73423768636471583474736c5055536c51574346516a4d3d, '77777772', 'juan@tecnico.com', 'juan', '$2y$12$hsL8XDRikRgbWlBM8ftNuum8jYsOeB.gU52oaPdhB8FjAHoXTz7aO', 1, '2025-10-31 18:55:10', NULL, NULL, 0, NULL, NULL, 'activo', 3),
(4, 0x696450717544497a4f486447503975727a695433646b6968326778636a7a47546b4e484d544a4755542b633d, 0x7957775065532b59546176387976694f7a52655379584442556b447037417271314266754e6f4e566b76673d, 0x5437792b384736784f5a69507266526367643343736d2f67547635414f4f75797a77766d3553384b426f513d, 0x5437337631795449522f6656426f6b612f796d66377a7a47427748462b5a463362335072546833555146733d, '77777773', 'pedro@mantenimiento.com', 'pedro', '$2y$12$fsEpyKNQWK8gvTiTjLuPe.jlnYSDOw3bxjOjVbpSFu58N7koJDiCG', 1, '2025-10-31 18:55:10', NULL, NULL, 0, NULL, NULL, 'activo', 4),
(5, 0x5a4333675a37796e75464c5768414c6f79356e52434575485a435a64417835304c556943736636393376673d, 0x6d2b4e7474517667642f597541475957536f614771565a4d4b34497747563875644f566739503539774b673d, 0x7a3245355a4b7277706e416472746c4c7661626b794961314b56344344433357724f7237733370433032383d, 0x557473676148724e34335a396f4b7537554756776f4a747a442b512b6358676a73784a706b724b795a53453d, '77777774', 'carlos@residente.com', 'carlos', '$2y$12$HrTUFYf6kHU7OR0to8fQaum9kx6.NrvXu2lIdOb.CNQkPXX9QuVtq', 1, '2025-10-31 18:55:10', NULL, NULL, 0, NULL, NULL, 'activo', 2),
(6, 0x3932574c78437a776a6c57567979794c6a4f735832664459545a66415457704256417a455968424d4751733d, 0x78656962354f4e625262514f45373867774933314b374a5a4373364f38647867463259524d764655484a553d, 0x2b2b7a57786f434f5444315559397974305774394a565538766c454553307830694961335077494b776d593d, 0x6f51416e6479757a686a4858787a2f485a41667953695748506e66514339705153784259364b41597645633d, '77777775', 'ana@residente.com', 'ana', '$2y$12$yXa9tayVHOo4Q9zVlu3pguepcg5o34DXX2kPg1FXJw/Ogr.ts1KCO', 1, '2025-10-31 18:55:11', NULL, NULL, 0, NULL, NULL, 'activo', 2),
(7, 0x644765427835545a6c6a784f6f746a582b443954554841793654674457504e376477705859454e4d4244773d, 0x2f2b5367612f66704462462f49304c43366c3357333952485a712b724d4173373858396b376c43614e6d343d, 0x774a512f306641677676496977314a51484c4a372f4c55675375446375516e6b714e396b302f564333384d3d, 0x58523879352f4a634c5736444749756a6f4d614f2f6c7959685865377a79376856673852753571495151383d, '77777776', 'luis@residente.com', 'luis', '$2y$12$WKGVdR47fmdWvggQ99bktuBvTOxFuVfzuPfDuJvfBxDVldQEWF882', 1, '2025-10-31 18:55:11', NULL, NULL, 0, NULL, NULL, 'activo', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona_paga_factura`
--

CREATE TABLE `persona_paga_factura` (
  `id_factura` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `fecha_pago` datetime NOT NULL DEFAULT current_timestamp(),
  `monto_pagado` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `persona_paga_factura`
--

INSERT INTO `persona_paga_factura` (`id_factura`, `id_persona`, `fecha_pago`, `monto_pagado`) VALUES
(2, 5, '2025-10-28 19:31:11', 159.16),
(3, 6, '2025-10-28 19:23:41', 148.66);

--
-- Disparadores `persona_paga_factura`
--
DELIMITER $$
CREATE TRIGGER `after_pago_factura` AFTER INSERT ON `persona_paga_factura` FOR EACH ROW BEGIN
    DECLARE v_fecha_vencimiento DATE;
    DECLARE v_puntualidad VARCHAR(20);
    DECLARE v_monto_total DECIMAL(10,2);
    DECLARE v_total_pagado DECIMAL(10,2);
    DECLARE v_dias_retraso INT;

    -- Obtener fecha de vencimiento y monto total de la factura
    SELECT fecha_vencimiento, monto_total
    INTO v_fecha_vencimiento, v_monto_total
    FROM factura
    WHERE id_factura = NEW.id_factura;

    -- Calcular total pagado hasta ahora (INCLUYENDO el nuevo pago)
    SELECT COALESCE(SUM(monto_pagado), 0)
    INTO v_total_pagado
    FROM persona_paga_factura
    WHERE id_factura = NEW.id_factura;

    -- Calcular días de retraso
    SET v_dias_retraso = DATEDIFF(NEW.fecha_pago, v_fecha_vencimiento);

    -- Determinar puntualidad
    IF NEW.fecha_pago <= v_fecha_vencimiento THEN
        SET v_puntualidad = 'a tiempo';
    ELSE
        SET v_puntualidad = 'atrasado';
    END IF;

    -- Registrar en historial
    INSERT INTO historial_pago (id_factura, id_persona, monto_pagado, observacion)
    VALUES (
               NEW.id_factura,
               NEW.id_persona,
               NEW.monto_pagado,
               CONCAT(
                       'PAGO ', v_puntualidad,
                       ' | Factura #', NEW.id_factura,
                       ' | Monto: $', NEW.monto_pagado,
                       ' | Fecha Venc: ', DATE_FORMAT(v_fecha_vencimiento, '%d/%m/%Y'),
                       ' | Fecha Pago: ', DATE_FORMAT(NEW.fecha_pago, '%d/%m/%Y'),
                       CASE WHEN v_dias_retraso > 0 THEN CONCAT(' | Retraso: ', v_dias_retraso, ' días') ELSE '' END
               )
           );

    -- ACTUALIZAR ESTADO DE FACTURA SI ESTÁ COMPLETAMENTE PAGADA
    IF v_total_pagado >= v_monto_total THEN
        UPDATE factura
        SET estado = 'pagada'
        WHERE id_factura = NEW.id_factura;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva_area_comun`
--

CREATE TABLE `reserva_area_comun` (
  `id_reserva` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `fecha_reserva` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `motivo` text DEFAULT NULL,
  `estado` enum('pendiente','confirmada','cancelada') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Disparadores `reserva_area_comun`
--
DELIMITER $$
CREATE TRIGGER `after_reserva_area_comun_insert` AFTER INSERT ON `reserva_area_comun` FOR EACH ROW BEGIN
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

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_reserva_area_comun_update` AFTER UPDATE ON `reserva_area_comun` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_reserva_area_comun_update_horario` AFTER UPDATE ON `reserva_area_comun` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `rol` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `rol`, `descripcion`) VALUES
(1, 'Administrador', 'Usuario con acceso total al sistema'),
(2, 'Residente', 'Residente del edificio'),
(3, 'Soporte Externo', 'Personal externo de mantenimiento'),
(4, 'Soporte Interno', 'Personal de mantenimiento interno');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `id_servicio` int(11) NOT NULL,
  `nombre` enum('agua','luz','gas') NOT NULL,
  `unidad_medida` varchar(50) NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`id_servicio`, `nombre`, `unidad_medida`, `costo_unitario`, `estado`) VALUES
(1, 'agua', 'm³', 2.00, 'activo'),
(2, 'luz', 'kWh', 2.00, 'activo'),
(3, 'gas', 'm³', 22.50, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiene_departamento`
--

CREATE TABLE `tiene_departamento` (
  `id_dep_per` int(11) NOT NULL,
  `id_departamento` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `tiene_departamento`
--

INSERT INTO `tiene_departamento` (`id_dep_per`, `id_departamento`, `id_persona`, `estado`) VALUES
(1, 1, 2, 'activo'),
(2, 2, 5, 'activo'),
(3, 3, 6, 'activo'),
(4, 4, 7, 'activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alerta_predictiva`
--
ALTER TABLE `alerta_predictiva`
  ADD PRIMARY KEY (`id_alerta`),
  ADD KEY `id_departamento` (`id_departamento`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `area_comun`
--
ALTER TABLE `area_comun`
  ADD PRIMARY KEY (`id_area`);

--
-- Indices de la tabla `cargos_fijos`
--
ALTER TABLE `cargos_fijos`
  ADD PRIMARY KEY (`id_cargo`);

--
-- Indices de la tabla `comunicado`
--
ALTER TABLE `comunicado`
  ADD PRIMARY KEY (`id_comunicado`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `conceptos`
--
ALTER TABLE `conceptos`
  ADD PRIMARY KEY (`id_concepto`),
  ADD KEY `id_factura` (`id_factura`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`id_departamento`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`id_factura`),
  ADD KEY `id_departamento` (`id_departamento`);

--
-- Indices de la tabla `historial_consumo`
--
ALTER TABLE `historial_consumo`
  ADD PRIMARY KEY (`id_historial_consumo`),
  ADD KEY `id_medidor` (`id_medidor`);

--
-- Indices de la tabla `historial_incidente`
--
ALTER TABLE `historial_incidente`
  ADD PRIMARY KEY (`id_historial_incidente`),
  ADD KEY `id_incidente` (`id_incidente`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `historial_login`
--
ALTER TABLE `historial_login`
  ADD PRIMARY KEY (`id_historial_login`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `historial_pago`
--
ALTER TABLE `historial_pago`
  ADD PRIMARY KEY (`id_historial_pago`),
  ADD KEY `id_factura` (`id_factura`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `incidente`
--
ALTER TABLE `incidente`
  ADD PRIMARY KEY (`id_incidente`),
  ADD KEY `id_departamento` (`id_departamento`),
  ADD KEY `id_residente` (`id_residente`),
  ADD KEY `id_creador` (`id_creador`),
  ADD KEY `id_area` (`id_area`);

--
-- Indices de la tabla `incidente_asignado`
--
ALTER TABLE `incidente_asignado`
  ADD PRIMARY KEY (`id_asignacion`),
  ADD KEY `id_incidente` (`id_incidente`),
  ADD KEY `id_personal` (`id_personal`);

--
-- Indices de la tabla `lector_sensor_consumo`
--
ALTER TABLE `lector_sensor_consumo`
  ADD PRIMARY KEY (`id_lectura`),
  ADD KEY `id_medidor` (`id_medidor`);

--
-- Indices de la tabla `medidor`
--
ALTER TABLE `medidor`
  ADD PRIMARY KEY (`id_medidor`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `id_servicio` (`id_servicio`),
  ADD KEY `id_departamento` (`id_departamento`);

--
-- Indices de la tabla `notificacion_persona`
--
ALTER TABLE `notificacion_persona`
  ADD KEY `id_alerta` (`id_alerta`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`id_persona`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `persona_paga_factura`
--
ALTER TABLE `persona_paga_factura`
  ADD PRIMARY KEY (`id_factura`,`id_persona`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `reserva_area_comun`
--
ALTER TABLE `reserva_area_comun`
  ADD PRIMARY KEY (`id_reserva`),
  ADD UNIQUE KEY `id_area` (`id_area`,`fecha_reserva`,`hora_inicio`,`hora_fin`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`id_servicio`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tiene_departamento`
--
ALTER TABLE `tiene_departamento`
  ADD PRIMARY KEY (`id_dep_per`),
  ADD KEY `id_departamento` (`id_departamento`),
  ADD KEY `id_persona` (`id_persona`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alerta_predictiva`
--
ALTER TABLE `alerta_predictiva`
  MODIFY `id_alerta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `area_comun`
--
ALTER TABLE `area_comun`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `cargos_fijos`
--
ALTER TABLE `cargos_fijos`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `comunicado`
--
ALTER TABLE `comunicado`
  MODIFY `id_comunicado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `conceptos`
--
ALTER TABLE `conceptos`
  MODIFY `id_concepto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `departamento`
--
ALTER TABLE `departamento`
  MODIFY `id_departamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `historial_consumo`
--
ALTER TABLE `historial_consumo`
  MODIFY `id_historial_consumo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `historial_incidente`
--
ALTER TABLE `historial_incidente`
  MODIFY `id_historial_incidente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_login`
--
ALTER TABLE `historial_login`
  MODIFY `id_historial_login` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `historial_pago`
--
ALTER TABLE `historial_pago`
  MODIFY `id_historial_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `incidente`
--
ALTER TABLE `incidente`
  MODIFY `id_incidente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `incidente_asignado`
--
ALTER TABLE `incidente_asignado`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lector_sensor_consumo`
--
ALTER TABLE `lector_sensor_consumo`
  MODIFY `id_lectura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=373;

--
-- AUTO_INCREMENT de la tabla `medidor`
--
ALTER TABLE `medidor`
  MODIFY `id_medidor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `reserva_area_comun`
--
ALTER TABLE `reserva_area_comun`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tiene_departamento`
--
ALTER TABLE `tiene_departamento`
  MODIFY `id_dep_per` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alerta_predictiva`
--
ALTER TABLE `alerta_predictiva`
  ADD CONSTRAINT `alerta_predictiva_ibfk_1` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`),
  ADD CONSTRAINT `alerta_predictiva_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicio` (`id_servicio`);

--
-- Filtros para la tabla `comunicado`
--
ALTER TABLE `comunicado`
  ADD CONSTRAINT `comunicado_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`);

--
-- Filtros para la tabla `conceptos`
--
ALTER TABLE `conceptos`
  ADD CONSTRAINT `conceptos_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `factura` (`id_factura`),
  ADD CONSTRAINT `conceptos_ibfk_2` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`);

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_1` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`);

--
-- Filtros para la tabla `historial_consumo`
--
ALTER TABLE `historial_consumo`
  ADD CONSTRAINT `historial_consumo_ibfk_1` FOREIGN KEY (`id_medidor`) REFERENCES `medidor` (`id_medidor`);

--
-- Filtros para la tabla `historial_incidente`
--
ALTER TABLE `historial_incidente`
  ADD CONSTRAINT `historial_incidente_ibfk_1` FOREIGN KEY (`id_incidente`) REFERENCES `incidente` (`id_incidente`),
  ADD CONSTRAINT `historial_incidente_ibfk_2` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`);

--
-- Filtros para la tabla `historial_login`
--
ALTER TABLE `historial_login`
  ADD CONSTRAINT `historial_login_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`);

--
-- Filtros para la tabla `historial_pago`
--
ALTER TABLE `historial_pago`
  ADD CONSTRAINT `historial_pago_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `factura` (`id_factura`),
  ADD CONSTRAINT `historial_pago_ibfk_2` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`);

--
-- Filtros para la tabla `incidente`
--
ALTER TABLE `incidente`
  ADD CONSTRAINT `incidente_ibfk_1` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`),
  ADD CONSTRAINT `incidente_ibfk_2` FOREIGN KEY (`id_residente`) REFERENCES `persona` (`id_persona`),
  ADD CONSTRAINT `incidente_ibfk_3` FOREIGN KEY (`id_creador`) REFERENCES `persona` (`id_persona`),
  ADD CONSTRAINT `incidente_ibfk_4` FOREIGN KEY (`id_area`) REFERENCES `area_comun` (`id_area`);

--
-- Filtros para la tabla `incidente_asignado`
--
ALTER TABLE `incidente_asignado`
  ADD CONSTRAINT `incidente_asignado_ibfk_1` FOREIGN KEY (`id_incidente`) REFERENCES `incidente` (`id_incidente`),
  ADD CONSTRAINT `incidente_asignado_ibfk_2` FOREIGN KEY (`id_personal`) REFERENCES `persona` (`id_persona`);

--
-- Filtros para la tabla `lector_sensor_consumo`
--
ALTER TABLE `lector_sensor_consumo`
  ADD CONSTRAINT `lector_sensor_consumo_ibfk_1` FOREIGN KEY (`id_medidor`) REFERENCES `medidor` (`id_medidor`);

--
-- Filtros para la tabla `medidor`
--
ALTER TABLE `medidor`
  ADD CONSTRAINT `medidor_ibfk_1` FOREIGN KEY (`id_servicio`) REFERENCES `servicio` (`id_servicio`),
  ADD CONSTRAINT `medidor_ibfk_2` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`);

--
-- Filtros para la tabla `notificacion_persona`
--
ALTER TABLE `notificacion_persona`
  ADD CONSTRAINT `notificacion_persona_ibfk_1` FOREIGN KEY (`id_alerta`) REFERENCES `alerta_predictiva` (`id_alerta`),
  ADD CONSTRAINT `notificacion_persona_ibfk_2` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`);

--
-- Filtros para la tabla `persona`
--
ALTER TABLE `persona`
  ADD CONSTRAINT `persona_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`);

--
-- Filtros para la tabla `persona_paga_factura`
--
ALTER TABLE `persona_paga_factura`
  ADD CONSTRAINT `persona_paga_factura_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `factura` (`id_factura`),
  ADD CONSTRAINT `persona_paga_factura_ibfk_2` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`);

--
-- Filtros para la tabla `reserva_area_comun`
--
ALTER TABLE `reserva_area_comun`
  ADD CONSTRAINT `reserva_area_comun_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`),
  ADD CONSTRAINT `reserva_area_comun_ibfk_2` FOREIGN KEY (`id_area`) REFERENCES `area_comun` (`id_area`);

--
-- Filtros para la tabla `tiene_departamento`
--
ALTER TABLE `tiene_departamento`
  ADD CONSTRAINT `tiene_departamento_ibfk_1` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`),
  ADD CONSTRAINT `tiene_departamento_ibfk_2` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
