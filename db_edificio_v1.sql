-- phpMyAdmin SQL Dump
-- version 5.2.2deb1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 09-10-2025 a las 14:37:24
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
-- Base de datos: `db_edificio_v1`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `area_comun`
--

CREATE TABLE `area_comun` (
  `id_area` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `capacidad` int(11) DEFAULT NULL,
  `estado` enum('disponible','mantenimiento','ocupada') DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE `departamento` (
  `id_departamento` int(11) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `piso` int(11) NOT NULL,
  `metros_cuadrados` decimal(6,2) DEFAULT NULL,
  `estado` enum('ocupado','disponible','mantenimiento') DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `id_factura` int(11) NOT NULL,
  `id_departamento` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `id_historial_consumo` int(11) NOT NULL,
  `fecha_emision` date NOT NULL DEFAULT curdate(),
  `fecha_vencimiento` date NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','pagada','vencida') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_incidente`
--

CREATE TABLE `historial_incidente` (
  `id_historial_incidente` int(11) NOT NULL,
  `id_incidente` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `accion` enum('asignacion','inicio_atencion','actualizacion','resolucion','cancelacion') NOT NULL,
  `observacion` text DEFAULT NULL,
  `estado_anterior` enum('pendiente','en_proceso','resuelto','cancelado') DEFAULT NULL,
  `estado_nuevo` enum('pendiente','en_proceso','resuelto','cancelado') DEFAULT NULL,
  `fecha_accion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_login`
--

CREATE TABLE `historial_login` (
  `id_historial_login` int(11) NOT NULL,
  `id_login` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `estado` enum('exitoso','fallido') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidente`
--

CREATE TABLE `incidente` (
  `id_incidente` int(11) NOT NULL,
  `id_departamento` int(11) NOT NULL,
  `id_residente` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','en_proceso','resuelto','cancelado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidente_asignado`
--

CREATE TABLE `incidente_asignado` (
  `id_asignacion` int(11) NOT NULL,
  `id_incidente` int(11) NOT NULL,
  `id_personal` int(11) NOT NULL,
  `fecha_asignacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_atencion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lector_sensor_consumo`
--

CREATE TABLE `lector_sensor_consumo` (
  `id_lectura` int(11) NOT NULL,
  `id_medidor` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `consumo` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login`
--

CREATE TABLE `login` (
  `id_login` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `verificado` tinyint(1) DEFAULT 0,
  `tiempo_verificacion` datetime DEFAULT NULL,
  `codigo_recuperacion` varchar(6) DEFAULT NULL,
  `tiempo_codigo_recuperacion` datetime DEFAULT NULL,
  `tiempo_bloqueo` datetime DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `id_persona` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

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
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `id_rol` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`id_persona`, `nombre`, `apellido_paterno`, `apellido_materno`, `ci`, `telefono`, `email`, `estado`, `id_rol`) VALUES
(1, 0x596d77746c394d6457746a47357a6f6149312b6b5a6a4e52553231366257354d526b7836576b5a4e4c3074705754526a5130453950513d3d, 0x4b32365035464162354c6d3378714c6d7a584577616d63355532646e53554a4e56326b30566c6855616d6c5a4e32706c4b31453950513d3d, 0x5a4f4e5a42655174617737367645726d626d5133723367344e335a6a5957314f51325a36524770316332706f5957564b536b453950513d3d, 0x4c7278513563304e497969776443526a576a7275536d457257455268536c6c775a54563361544e47646e4e58656e6c576148633950513d3d, '77238533', 'admin@gmail.com', 'activo', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva_area_comun`
--

CREATE TABLE `reserva_area_comun` (
  `id_persona` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `fecha_reserva` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado` enum('pendiente','confirmada','cancelada') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `rol` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `rol`, `descripcion`) VALUES
(1, 'Administrador', 'administra el sistema completo - gestion'),
(2, 'Residente', 'es la persona que habita un departamento'),
(3, 'Soporte', 'es el que le da mantenimiento y soporte a los departamentos');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiene_departamento`
--

CREATE TABLE `tiene_departamento` (
  `id_dep_per` int(11) NOT NULL,
  `id_departamento` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

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
-- Indices de la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`id_departamento`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`id_factura`),
  ADD KEY `id_departamento` (`id_departamento`),
  ADD KEY `id_servicio` (`id_servicio`),
  ADD KEY `id_historial_consumo` (`id_historial_consumo`);

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
  ADD KEY `id_login` (`id_login`);

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
  ADD KEY `id_residente` (`id_residente`);

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
-- Indices de la tabla `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id_login`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_persona` (`id_persona`);

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
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `reserva_area_comun`
--
ALTER TABLE `reserva_area_comun`
  ADD KEY `id_persona` (`id_persona`),
  ADD KEY `id_area` (`id_area`);

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
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `departamento`
--
ALTER TABLE `departamento`
  MODIFY `id_departamento` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_historial_incidente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_login`
--
ALTER TABLE `historial_login`
  MODIFY `id_historial_login` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_pago`
--
ALTER TABLE `historial_pago`
  MODIFY `id_historial_pago` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_lectura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `login`
--
ALTER TABLE `login`
  MODIFY `id_login` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `medidor`
--
ALTER TABLE `medidor`
  MODIFY `id_medidor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tiene_departamento`
--
ALTER TABLE `tiene_departamento`
  MODIFY `id_dep_per` int(11) NOT NULL AUTO_INCREMENT;

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
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_1` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`),
  ADD CONSTRAINT `factura_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicio` (`id_servicio`),
  ADD CONSTRAINT `factura_ibfk_3` FOREIGN KEY (`id_historial_consumo`) REFERENCES `historial_consumo` (`id_historial_consumo`);

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
  ADD CONSTRAINT `historial_login_ibfk_1` FOREIGN KEY (`id_login`) REFERENCES `login` (`id_login`);

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
  ADD CONSTRAINT `incidente_ibfk_2` FOREIGN KEY (`id_residente`) REFERENCES `persona` (`id_persona`);

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
-- Filtros para la tabla `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `login_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`);

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
