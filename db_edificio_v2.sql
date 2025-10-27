-- phpMyAdmin SQL Dump
-- version 5.2.2deb1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 27-10-2025 a las 10:34:16
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
-- Base de datos: `db_edificio_v2`
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
(1, 2, NULL, 'incidente', 20000.00, 10, 'incidente', 1, 'Costo por reparación externa: 4465465465464654', '2025-10-27 10:29:48', 'pendiente');

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

--
-- Volcado de datos para la tabla `historial_incidente`
--

INSERT INTO `historial_incidente` (`id_historial_incidente`, `id_incidente`, `id_persona`, `accion`, `observacion`, `estado_anterior`, `estado_nuevo`, `fecha_accion`) VALUES
(1, 1, 2, 'creacion', 'Incidente creado: interno primero', NULL, 'pendiente', '2025-10-26 18:51:08'),
(2, 1, 4, 'asignacion', 'Asignado para: interno segundo\r\n', 'pendiente', 'en_proceso', '2025-10-26 18:51:20'),
(3, 1, 4, 'inicio_atencion', 'Atención iniciada: interno', 'en_proceso', 'en_proceso', '2025-10-26 19:08:31'),
(4, 1, 4, 'resolucion', 'Incidente resuelto: interno primero', 'en_proceso', 'resuelto', '2025-10-26 19:15:15'),
(5, 2, 2, 'creacion', 'Incidente creado: asdasdasdasd', NULL, 'pendiente', '2025-10-26 19:16:38'),
(6, 2, 4, 'asignacion', 'Asignado para: asdasdasd', 'pendiente', 'en_proceso', '2025-10-26 19:16:47'),
(7, 2, 1, 'cancelacion', 'Incidente cancelado: asdasdasdasd', 'en_proceso', 'cancelado', '2025-10-26 19:17:30'),
(8, 3, 2, 'creacion', 'Incidente creado: asdasdasdasdasdasd', NULL, 'pendiente', '2025-10-26 19:25:42'),
(9, 3, 2, 'cancelacion', 'Incidente cancelado: asdasdasdasdasdasd', 'pendiente', 'cancelado', '2025-10-26 19:26:05'),
(10, 4, 2, 'creacion', 'Incidente creado: 555555a5sd45as4d64as65d4as654d', NULL, 'pendiente', '2025-10-26 19:27:24'),
(11, 4, 2, 'cancelacion', 'Incidente cancelado: 555555a5sd45as4d64as65d4as654d', 'pendiente', 'cancelado', '2025-10-26 19:27:37'),
(12, 5, 2, 'creacion', 'Incidente creado: 4654654654656546546546465', NULL, 'pendiente', '2025-10-26 19:29:58'),
(13, 5, 2, 'cancelacion', 'Incidente cancelado: 4654654654656546546546465', 'pendiente', 'cancelado', '2025-10-26 19:30:04'),
(14, 6, 7, 'creacion', 'Incidente creado: 55555555555', NULL, 'pendiente', '2025-10-26 19:38:43'),
(15, 6, 4, 'asignacion', 'Asignado para: 12121', 'pendiente', 'en_proceso', '2025-10-26 19:38:59'),
(16, 6, 3, 'reasignacion', 'Reasignado: Reasignación completada', 'en_proceso', 'en_proceso', '2025-10-26 19:39:13'),
(17, 6, 3, 'inicio_atencion', 'Atención iniciada: asdasdasd', 'en_proceso', 'en_proceso', '2025-10-26 19:49:58'),
(18, 6, 3, 'actualizacion', 'Progreso: es dificil ', 'en_proceso', 'en_proceso', '2025-10-26 20:13:03'),
(19, 6, 4, 'reasignacion', 'Reasignado: arreglalo', 'en_proceso', 'en_proceso', '2025-10-26 20:22:50'),
(20, 7, 7, 'creacion', 'Incidente creado: asdaaaaaaaaaa', NULL, 'pendiente', '2025-10-26 20:23:39'),
(21, 7, 3, 'asignacion', 'Asignado para: assss', 'pendiente', 'en_proceso', '2025-10-26 20:23:49'),
(22, 7, 3, 'inicio_atencion', 'Atención iniciada: aaaaaa', 'en_proceso', 'en_proceso', '2025-10-26 20:24:19'),
(23, 7, 3, 'actualizacion', 'Progreso: esta dificil', 'en_proceso', 'en_proceso', '2025-10-26 20:24:42'),
(24, 7, 4, 'reasignacion', 'Reasignado: ayudalo', 'en_proceso', 'en_proceso', '2025-10-26 20:25:47'),
(25, 7, 7, 'cancelacion', 'Incidente cancelado: asdaaaaaaaaaa', 'en_proceso', 'cancelado', '2025-10-26 20:35:02'),
(26, 6, 7, 'cancelacion', 'Incidente cancelado: 55555555555', 'en_proceso', 'cancelado', '2025-10-26 20:35:06'),
(27, 8, 2, 'creacion', 'Incidente creado: pruebazaaaaa', NULL, 'pendiente', '2025-10-27 10:26:13'),
(28, 8, 4, 'asignacion', 'Asignado para: zzzzzz', 'pendiente', 'en_proceso', '2025-10-27 10:26:28'),
(29, 8, 1, 'cancelacion', 'Incidente cancelado: pruebazaaaaa', 'en_proceso', 'cancelado', '2025-10-27 10:26:36'),
(30, 9, 2, 'creacion', 'Incidente creado: asdasdasdasdddd', NULL, 'pendiente', '2025-10-27 10:26:54'),
(31, 9, 2, 'cancelacion', 'Incidente cancelado: asdasdasdasdddd', 'pendiente', 'cancelado', '2025-10-27 10:27:18'),
(32, 10, 2, 'creacion', 'Incidente creado: 4465465465464654', NULL, 'pendiente', '2025-10-27 10:27:44'),
(33, 10, 4, 'asignacion', 'Asignado para: asdasdasdas', 'pendiente', 'en_proceso', '2025-10-27 10:28:06'),
(34, 10, 4, 'inicio_atencion', 'Atención iniciada: 54655565454654', 'en_proceso', 'en_proceso', '2025-10-27 10:28:45'),
(35, 10, 4, 'actualizacion', 'Progreso: dfsfsdfsd', 'en_proceso', 'en_proceso', '2025-10-27 10:28:55'),
(36, 10, 3, 'reasignacion', 'Reasignado: fsdfsdfsdfsdfsd', 'en_proceso', 'en_proceso', '2025-10-27 10:29:18'),
(37, 10, 3, 'resolucion', 'Incidente resuelto: 4465465465464654', 'en_proceso', 'resuelto', '2025-10-27 10:29:48');

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
(1, 1, '2025-10-26 18:50:43', 'exitoso'),
(2, 1, '2025-10-26 19:16:19', 'exitoso'),
(3, 2, '2025-10-26 19:17:13', 'exitoso'),
(4, 4, '2025-10-26 19:18:05', 'exitoso'),
(5, 1, '2025-10-26 19:21:09', 'exitoso'),
(6, 2, '2025-10-26 19:23:24', 'exitoso'),
(7, 1, '2025-10-26 19:30:43', 'exitoso'),
(8, 3, '2025-10-26 19:49:50', 'exitoso'),
(9, 1, '2025-10-26 20:13:18', 'exitoso'),
(10, 1, '2025-10-26 20:23:25', 'exitoso'),
(11, 3, '2025-10-26 20:24:11', 'exitoso'),
(12, 4, '2025-10-26 20:26:23', 'exitoso'),
(13, 7, '2025-10-26 20:34:49', 'exitoso'),
(14, 4, '2025-10-26 20:35:17', 'exitoso'),
(15, 1, '2025-10-27 09:26:01', 'exitoso'),
(16, 3, '2025-10-27 09:34:58', 'exitoso'),
(17, 4, '2025-10-27 09:35:13', 'exitoso'),
(18, 7, '2025-10-27 09:38:10', 'exitoso'),
(19, 1, '2025-10-27 09:38:59', 'exitoso'),
(20, 2, '2025-10-27 09:43:27', 'exitoso'),
(21, 4, '2025-10-27 10:18:45', 'exitoso'),
(22, 1, '2025-10-27 10:22:25', 'exitoso'),
(23, 1, '2025-10-27 10:25:13', 'exitoso'),
(24, 1, '2025-10-27 10:25:58', 'exitoso'),
(25, 2, '2025-10-27 10:27:03', 'exitoso'),
(26, 1, '2025-10-27 10:27:27', 'exitoso'),
(27, 2, '2025-10-27 10:28:14', 'exitoso'),
(28, 4, '2025-10-27 10:28:35', 'exitoso'),
(29, 1, '2025-10-27 10:29:06', 'exitoso'),
(30, 3, '2025-10-27 10:29:34', 'exitoso'),
(31, 2, '2025-10-27 10:30:00', 'exitoso');

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
-- Volcado de datos para la tabla `incidente`
--

INSERT INTO `incidente` (`id_incidente`, `id_departamento`, `id_residente`, `id_creador`, `tipo`, `descripcion`, `descripcion_detallada`, `costo_externo`, `id_area`, `fecha_registro`, `estado`) VALUES
(1, 1, 2, 2, 'interno', 'interno primero', 'interno\n--- RESUELTO POR PERSONAL ---\nse soluciono', 0.00, NULL, '2025-10-26 18:51:08', 'resuelto'),
(2, 1, 2, 2, 'interno', 'asdasdasdasd', 'asd\n--- CANCELADO ---\nlo solucione', 0.00, NULL, '2025-10-26 19:16:38', 'cancelado'),
(3, 1, 2, 2, 'interno', 'asdasdasdasdasdasd', 'asdasdasdasdasdasdasd\n--- CANCELADO ---\nrgggggg', 0.00, NULL, '2025-10-26 19:25:42', 'cancelado'),
(4, 1, 2, 2, 'interno', '555555a5sd45as4d64as65d4as654d', 'as4d46a4sd465as4d654as65d4\n--- CANCELADO ---\nasdasdasdasdasd', 0.00, NULL, '2025-10-26 19:27:24', 'cancelado'),
(5, 1, 2, 2, 'interno', '4654654654656546546546465', '4646546544654654654654654654654\n--- CANCELADO ---\nasdasdasd', 0.00, NULL, '2025-10-26 19:29:58', 'cancelado'),
(6, 4, 7, 7, 'interno', '55555555555', '546546546546546546546545\n--- CANCELADO ---\nsssss', 0.00, NULL, '2025-10-26 19:38:43', 'cancelado'),
(7, 4, 7, 7, 'externo', 'asdaaaaaaaaaa', 'aaa\n--- CANCELADO ---\nsssss', 0.00, NULL, '2025-10-26 20:23:39', 'cancelado'),
(8, 1, 2, 2, 'interno', 'pruebazaaaaa', 'asd\n--- CANCELADO ---\nxxxxxx', 0.00, NULL, '2025-10-27 10:26:13', 'cancelado'),
(9, 1, 2, 2, 'interno', 'asdasdasdasdddd', 'asd\n--- CANCELADO ---\ndsfsfsfsdf', 0.00, NULL, '2025-10-27 10:26:54', 'cancelado'),
(10, 1, 2, 2, 'externo', '4465465465464654', '6465465465\n--- RESUELTO POR PERSONAL ---\nasdasdasdasdasdasd', 20000.00, 2, '2025-10-27 10:27:44', 'resuelto');

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
-- Volcado de datos para la tabla `incidente_asignado`
--

INSERT INTO `incidente_asignado` (`id_asignacion`, `id_incidente`, `id_personal`, `fecha_asignacion`, `fecha_atencion`, `observaciones`, `requiere_reasignacion`, `comentario_reasignacion`) VALUES
(1, 1, 4, '2025-10-26 18:51:20', '2025-10-26 19:08:31', 'interno', 0, NULL),
(2, 2, 4, '2025-10-26 19:16:47', NULL, 'asdasdasd', 0, NULL),
(3, 6, 4, '2025-10-26 19:38:59', '2025-10-26 19:49:58', 'arreglalo', 0, NULL),
(4, 7, 4, '2025-10-26 20:23:49', '2025-10-26 20:24:19', 'ayudalo', 0, NULL),
(5, 8, 4, '2025-10-27 10:26:28', NULL, 'zzzzzz', 0, NULL),
(6, 10, 3, '2025-10-27 10:28:06', '2025-10-27 10:28:45', 'fsdfsdfsdfsdfsd', 0, NULL);

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
(1, 0x73336d6f79546f3364466b74587a63334e55433179474e6b723870535355574f3170346c416e64385538383d, 0x485a6b5236387546764b717452445259306a2b53624e6657455451576863465149326d6645424f744642513d, 0x63456f5a486f512b5a4e3476394f4b35774e594a6e636c6c326e4b527044464a59575238473436547668343d, 0x53644b57302f37436565627568597a7a613538343571483166335766486d656e79355255552b4a515770773d, '77777777', 'admin@sistema.com', 'admin', '$2y$12$zJFko1QR1HCxNgAMOtNAXODKGz/XqaI920b1XNxUbWgRz8V0nHfQK', 1, '2025-10-29 18:50:24', NULL, NULL, 0, NULL, NULL, 'activo', 1),
(2, 0x5253496c7a665146534e426b392f366c4e55413733565330614464706736314a314e7668796f4f764f6c343d, 0x634d594f3871683066483256324e5177747074715a5a7778514c4c372f307448464e6161346f4b726a45553d, 0x7545355462663935454767386e30756850526e524e76762f2f4630513638746a326f666d546d5150774d553d, 0x5a64565149696171345143373479314e31474c6838654c4a5577553954554f34392f774944456746464a513d, '77777771', 'maria@residente.com', 'maria', '$2y$12$Aeziuwgkjl64OIMtPxOj0u/IfzoC5VryIbrsjWiw.0JasNeswQpaC', 1, '2025-10-29 18:50:24', NULL, NULL, 0, NULL, NULL, 'activo', 2),
(3, 0x3550645a376e54544f6a787531374a593352594b4a425a79367a2f46646d58516b7667587858486c7167733d, 0x74752f3235633141593359552f4748583534515245476e78334358324e6f68326a706d54526731774f44553d, 0x354d7766363079434b507166376e6b567a4b35446c4666697973785a6e503448456775754843555a3841773d, 0x4646574e544d425059637a4f337a306b666e644e386b56684e3166644d2f7a444b323564443070364859633d, '77777772', 'juan@tecnico.com', 'juan', '$2y$12$vZEn3cY2jThZlO5JGpxEp.XIa8Z9GwiRP1la3AocAijEeo6eqU0wK', 1, '2025-10-29 18:50:24', NULL, NULL, 0, NULL, NULL, 'activo', 3),
(4, 0x5036614c57443261486372542f785a362b7255707737456c59635a7a7033386c4a57733758477077786f6b3d, 0x592f344a6f54553332656a6b2f764d364576695a4d757470673073346f5076397933686d703342743054383d, 0x58666f62694b32595743452b527073736d647771734b736c4b50796f6a633043304c6139705737624e646f3d, 0x666f57362b41376176653639536547676e50794555766173384b4a2b626f6351356836793875674a487a6b3d, '77777773', 'pedro@mantenimiento.com', 'pedro', '$2y$12$Df8u33/iX.YbIKnM2Tgni.TvrCsh83EBew9Vi.9goM8T20Tqc23aK', 1, '2025-10-29 18:50:25', NULL, NULL, 0, NULL, NULL, 'activo', 4),
(5, 0x4653476576576133454c43686c46543136412b6f57614944467a3435314b6c787849322b65717669574e6b3d, 0x326d7a617361346a355138473759546673695a6d64343556763854336b56535a78794336524c6c41764b453d, 0x68566e46744353513330626f644d5a463734567332626b684d472b6e4d68546d4f63504667572f4a7672593d, 0x79464e323769742b62797031426130755950706c382b30594f777458483873654173566639656879634b383d, '77777774', 'carlos@residente.com', 'carlos', '$2y$12$V9i7LQvaCN9eb2gTCRLK7ux4Avpn4u2WothjCTv28Dxcv/gUm9heq', 1, '2025-10-29 18:50:25', NULL, NULL, 0, NULL, NULL, 'activo', 2),
(6, 0x4a4364474b53782f54584a355438476e4d497476557248724a39765774625a666f426a56325a6b657171553d, 0x626a6a552b416c475372496a43735737326e4c32654f68554c7a4164535574334f2f684770635975764c6f3d, 0x345738566d6b7453323754703434414646646a31415046592b2f792f7a454c4742375438524e6a674d2b453d, 0x7861595170414b2f683373334e6172476b2f69516472473245773970447a6d70736a4e4e586972447a4b553d, '77777775', 'ana@residente.com', 'ana', '$2y$12$tA7Ag8MjZ.56TPEaj0mUgei8ujOLc6O0qveqeNANF7zUapwwliaXG', 1, '2025-10-29 18:50:25', NULL, NULL, 0, NULL, NULL, 'activo', 2),
(7, 0x695967635772663746464e426c443952305164515466425430575964635269633971527a675646527452593d, 0x656e77445a5061565542776354394e5850574459556b6a6b2b704a324e47634f756b4149324e334d49336f3d, 0x4153637a4e354e4c6c4b6e4974796f4331715768476e4341494a695a7a4d2b4b537350597234794d3263343d, 0x4a6d774b4e6f414c4c6378586336566d686d3874745030665665413133416d635748714c502f2b466b39673d, '77777776', 'luis@residente.com', 'luis', '$2y$12$57ZTdp5Hn3IMsA0gNLCNt.YwW.CwsswT/ZbVTqoG8O2o3PMTJQdta', 1, '2025-10-29 18:50:25', NULL, NULL, 0, NULL, NULL, 'activo', 2);

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
-- AUTO_INCREMENT de la tabla `comunicado`
--
ALTER TABLE `comunicado`
  MODIFY `id_comunicado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `conceptos`
--
ALTER TABLE `conceptos`
  MODIFY `id_concepto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `departamento`
--
ALTER TABLE `departamento`
  MODIFY `id_departamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_consumo`
--
ALTER TABLE `historial_consumo`
  MODIFY `id_historial_consumo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_incidente`
--
ALTER TABLE `historial_incidente`
  MODIFY `id_historial_incidente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `historial_login`
--
ALTER TABLE `historial_login`
  MODIFY `id_historial_login` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `historial_pago`
--
ALTER TABLE `historial_pago`
  MODIFY `id_historial_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `incidente`
--
ALTER TABLE `incidente`
  MODIFY `id_incidente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `incidente_asignado`
--
ALTER TABLE `incidente_asignado`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `lector_sensor_consumo`
--
ALTER TABLE `lector_sensor_consumo`
  MODIFY `id_lectura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `medidor`
--
ALTER TABLE `medidor`
  MODIFY `id_medidor` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT;

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
