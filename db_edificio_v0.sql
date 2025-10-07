-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-10-2025 a las 15:33:26
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_edificio`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alerta_predictiva`
--

CREATE TABLE `alerta_predictiva` (
  `id_alerta` int(11) NOT NULL,
  `id_historial_pago` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `contador_meses_atrasados` int(11) NOT NULL,
  `nivel_riesgo` enum('riesgo','corte') NOT NULL,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `atiende_incidente`
--

CREATE TABLE `atiende_incidente` (
  `id_incidente` int(11) NOT NULL,
  `id_personal` int(11) NOT NULL,
  `fecha_atendido` datetime NOT NULL,
  `acciones_tomadas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE `departamento` (
  `id_departamento` int(11) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `piso` int(11) NOT NULL,
  `id_edificio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `edificio`
--

CREATE TABLE `edificio` (
  `id_edificio` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `edificio`
--

INSERT INTO `edificio` (`id_edificio`, `nombre`, `direccion`) VALUES
(1, 'Bilbao', 'Av. Principal 123, Zona Centro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `id_factura` int(11) NOT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `estado_pago` varchar(50) DEFAULT NULL,
  `qr_code_url` varchar(255) DEFAULT NULL,
  `id_historial_consumo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_consumo`
--

CREATE TABLE `historial_consumo` (
  `id_historial_consumo` int(11) NOT NULL,
  `fecha_inicio_lectura` datetime NOT NULL,
  `fecha_fin_lectura` datetime NOT NULL,
  `consumo_total` decimal(10,2) DEFAULT NULL,
  `id_lectura` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_incidentes`
--

CREATE TABLE `historial_incidentes` (
  `id_historial_incidente` int(11) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_atendido` datetime NOT NULL,
  `acciones_tomadas` text DEFAULT NULL,
  `id_incidente` int(11) NOT NULL,
  `id_personal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_pagos`
--

CREATE TABLE `historial_pagos` (
  `id_historial_pago` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `fecha_pago` date NOT NULL,
  `metodo_pago` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidente`
--

CREATE TABLE `incidente` (
  `id_incidente` int(11) NOT NULL,
  `titulo_incidente` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `id_departamento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lectura_sensor`
--

CREATE TABLE `lectura_sensor` (
  `id_lectura` int(11) NOT NULL,
  `hora_lectura` datetime NOT NULL,
  `consumo` int(11) NOT NULL,
  `id_departamento` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion`
--

CREATE TABLE `notificacion` (
  `id_notificacion` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `id_alerta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `id_usuario` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `fecha_pago` date NOT NULL,
  `metodo_pago` varchar(150) NOT NULL,
  `referencia_bancaria` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `id_persona` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `appaterno` varchar(100) NOT NULL,
  `apmaterno` varchar(100) NOT NULL,
  `fecha_naci` date NOT NULL,
  `ci` varchar(20) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`id_persona`, `nombre`, `appaterno`, `apmaterno`, `fecha_naci`, `ci`, `telefono`, `email`) VALUES
(1, 'Maximo', 'Decimo', 'Meridio', '1980-01-01', '99999999', '000000000', 'admin@gmail.com'),
(2, 'Franz', 'Velasquez', 'Laura', '2000-08-08', '1', '77238533', 'prueba@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal`
--

CREATE TABLE `personal` (
  `id_personal` int(11) NOT NULL,
  `cargo` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `fecha_contratacion` date NOT NULL,
  `id_persona` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `codigo_recuperacion` varchar(6) DEFAULT NULL,
  `expiracion_codigo` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `personal`
--

INSERT INTO `personal` (`id_personal`, `cargo`, `username`, `password_hash`, `fecha_contratacion`, `id_persona`, `id_rol`, `codigo_recuperacion`, `expiracion_codigo`) VALUES
(1, 'Administrador', 'admin', '$2y$10$gkK1rykKa5RHpyIriwNuGeIyj/AbmEuxh969PGZlt71kpnGozceuS', '2025-10-02', 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pertenece_dep`
--

CREATE TABLE `pertenece_dep` (
  `id_usuario` int(11) NOT NULL,
  `id_departamento` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('activo','desactivado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES
(1, 'Administrador', 'Acceso total al sistema');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `id_servicio` int(11) NOT NULL,
  `nombre_servicio` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo_tarifa` enum('medidor','fijo') NOT NULL,
  `costo_unitario` decimal(10,4) DEFAULT NULL,
  `tarifa_fija` decimal(10,2) DEFAULT NULL,
  `unidad_medida` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiene_noti`
--

CREATE TABLE `tiene_noti` (
  `id_usuario` int(11) NOT NULL,
  `id_notificacion` int(11) NOT NULL,
  `fecha_hora_notificacion` date NOT NULL,
  `estado` enum('recibido','leido') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiene_serv`
--

CREATE TABLE `tiene_serv` (
  `id_departamento` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `fecha_inicio_servicio` date NOT NULL,
  `fecha_fin_servicio` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `codigo_recuperacion` varchar(6) DEFAULT NULL,
  `expiracion_codigo` datetime DEFAULT NULL,
  `id_persona` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `username`, `password_hash`, `codigo_recuperacion`, `expiracion_codigo`, `id_persona`) VALUES
(1, 'franz', '$2y$10$oPaY8NWPrNrdoPQAHC/4/ObPOtF5l5.6Yu.rUnGF0Hn4QdGO8CCaO', NULL, NULL, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alerta_predictiva`
--
ALTER TABLE `alerta_predictiva`
  ADD PRIMARY KEY (`id_alerta`),
  ADD KEY `id_historial_pago` (`id_historial_pago`),
  ADD KEY `id_factura` (`id_factura`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `atiende_incidente`
--
ALTER TABLE `atiende_incidente`
  ADD KEY `id_incidente` (`id_incidente`),
  ADD KEY `id_personal` (`id_personal`);

--
-- Indices de la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`id_departamento`),
  ADD KEY `id_edificio` (`id_edificio`);

--
-- Indices de la tabla `edificio`
--
ALTER TABLE `edificio`
  ADD PRIMARY KEY (`id_edificio`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`id_factura`),
  ADD KEY `id_historial_consumo` (`id_historial_consumo`);

--
-- Indices de la tabla `historial_consumo`
--
ALTER TABLE `historial_consumo`
  ADD PRIMARY KEY (`id_historial_consumo`),
  ADD KEY `id_lectura` (`id_lectura`);

--
-- Indices de la tabla `historial_incidentes`
--
ALTER TABLE `historial_incidentes`
  ADD PRIMARY KEY (`id_historial_incidente`),
  ADD KEY `id_incidente` (`id_incidente`),
  ADD KEY `id_personal` (`id_personal`);

--
-- Indices de la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  ADD PRIMARY KEY (`id_historial_pago`),
  ADD KEY `id_factura` (`id_factura`);

--
-- Indices de la tabla `incidente`
--
ALTER TABLE `incidente`
  ADD PRIMARY KEY (`id_incidente`),
  ADD KEY `id_departamento` (`id_departamento`);

--
-- Indices de la tabla `lectura_sensor`
--
ALTER TABLE `lectura_sensor`
  ADD PRIMARY KEY (`id_lectura`),
  ADD KEY `id_departamento` (`id_departamento`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `id_alerta` (`id_alerta`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_factura` (`id_factura`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`id_persona`),
  ADD UNIQUE KEY `ci` (`ci`);

--
-- Indices de la tabla `personal`
--
ALTER TABLE `personal`
  ADD PRIMARY KEY (`id_personal`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_persona` (`id_persona`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `pertenece_dep`
--
ALTER TABLE `pertenece_dep`
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_departamento` (`id_departamento`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `tiene_noti`
--
ALTER TABLE `tiene_noti`
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `tiene_serv`
--
ALTER TABLE `tiene_serv`
  ADD KEY `id_departamento` (`id_departamento`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `username` (`username`),
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
-- AUTO_INCREMENT de la tabla `departamento`
--
ALTER TABLE `departamento`
  MODIFY `id_departamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `edificio`
--
ALTER TABLE `edificio`
  MODIFY `id_edificio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT de la tabla `historial_incidentes`
--
ALTER TABLE `historial_incidentes`
  MODIFY `id_historial_incidente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  MODIFY `id_historial_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `incidente`
--
ALTER TABLE `incidente`
  MODIFY `id_incidente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lectura_sensor`
--
ALTER TABLE `lectura_sensor`
  MODIFY `id_lectura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `personal`
--
ALTER TABLE `personal`
  MODIFY `id_personal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alerta_predictiva`
--
ALTER TABLE `alerta_predictiva`
  ADD CONSTRAINT `alerta_predictiva_ibfk_1` FOREIGN KEY (`id_historial_pago`) REFERENCES `historial_pagos` (`id_historial_pago`),
  ADD CONSTRAINT `alerta_predictiva_ibfk_2` FOREIGN KEY (`id_factura`) REFERENCES `factura` (`id_factura`),
  ADD CONSTRAINT `alerta_predictiva_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `atiende_incidente`
--
ALTER TABLE `atiende_incidente`
  ADD CONSTRAINT `atiende_incidente_ibfk_1` FOREIGN KEY (`id_incidente`) REFERENCES `incidente` (`id_incidente`),
  ADD CONSTRAINT `atiende_incidente_ibfk_2` FOREIGN KEY (`id_personal`) REFERENCES `personal` (`id_personal`);

--
-- Filtros para la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD CONSTRAINT `departamento_ibfk_1` FOREIGN KEY (`id_edificio`) REFERENCES `edificio` (`id_edificio`);

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_1` FOREIGN KEY (`id_historial_consumo`) REFERENCES `historial_consumo` (`id_historial_consumo`);

--
-- Filtros para la tabla `historial_consumo`
--
ALTER TABLE `historial_consumo`
  ADD CONSTRAINT `historial_consumo_ibfk_1` FOREIGN KEY (`id_lectura`) REFERENCES `lectura_sensor` (`id_lectura`);

--
-- Filtros para la tabla `historial_incidentes`
--
ALTER TABLE `historial_incidentes`
  ADD CONSTRAINT `historial_incidentes_ibfk_1` FOREIGN KEY (`id_incidente`) REFERENCES `incidente` (`id_incidente`),
  ADD CONSTRAINT `historial_incidentes_ibfk_2` FOREIGN KEY (`id_personal`) REFERENCES `personal` (`id_personal`);

--
-- Filtros para la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  ADD CONSTRAINT `historial_pagos_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `factura` (`id_factura`);

--
-- Filtros para la tabla `incidente`
--
ALTER TABLE `incidente`
  ADD CONSTRAINT `incidente_ibfk_1` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`);

--
-- Filtros para la tabla `lectura_sensor`
--
ALTER TABLE `lectura_sensor`
  ADD CONSTRAINT `lectura_sensor_ibfk_1` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`),
  ADD CONSTRAINT `lectura_sensor_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicio` (`id_servicio`);

--
-- Filtros para la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD CONSTRAINT `notificacion_ibfk_1` FOREIGN KEY (`id_alerta`) REFERENCES `alerta_predictiva` (`id_alerta`);

--
-- Filtros para la tabla `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `pago_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `pago_ibfk_2` FOREIGN KEY (`id_factura`) REFERENCES `factura` (`id_factura`);

--
-- Filtros para la tabla `personal`
--
ALTER TABLE `personal`
  ADD CONSTRAINT `personal_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`),
  ADD CONSTRAINT `personal_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);

--
-- Filtros para la tabla `pertenece_dep`
--
ALTER TABLE `pertenece_dep`
  ADD CONSTRAINT `pertenece_dep_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `pertenece_dep_ibfk_2` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`);

--
-- Filtros para la tabla `tiene_noti`
--
ALTER TABLE `tiene_noti`
  ADD CONSTRAINT `tiene_noti_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `tiene_noti_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `tiene_serv`
--
ALTER TABLE `tiene_serv`
  ADD CONSTRAINT `tiene_serv_ibfk_1` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`),
  ADD CONSTRAINT `tiene_serv_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicio` (`id_servicio`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
