-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-05-2024 a las 22:51:46
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_demo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajero`
--

CREATE TABLE `cajero` (
  `id_cajero` int(11) NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `apellido` varchar(60) NOT NULL,
  `alias` varchar(40) NOT NULL,
  `correo` varchar(80) NOT NULL,
  `contrasena` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cajero`
--

INSERT INTO `cajero` (`id_cajero`, `nombre`, `apellido`, `alias`, `correo`, `contrasena`) VALUES
(1, 'Elmer', 'Carias', 'ecarias262', 'elmer.carias55@gmail.com', '$2y$10$4V17KkkvCT2/9F0xVHsEqO8vTsFskb2xy37FXppa5.icKaWdbpfCe');

--La contraseña es: #DXLatam24
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `apellido` varchar(60) NOT NULL,
  `usuario` varchar(40) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `contrasena` varchar(400) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nombre`, `apellido`, `usuario`, `correo`, `contrasena`) VALUES
(9, 'Elmer', 'Carias', 'Karme', 'elmer.carias55@gmail.com', '$2y$10$4V17KkkvCT2/9F0xVHsEqO8vTsFskb2xy37FXppa5.icKaWdbpfCe');

--La contraseña es: #DXLatam24
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta`
--

CREATE TABLE `cuenta` (
  `id_cuenta` int(11) NOT NULL,
  `n_cuenta` double NOT NULL,
  `saldo_cuenta` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuenta`
--

INSERT INTO `cuenta` (`id_cuenta`, `n_cuenta`, `saldo_cuenta`) VALUES
(2, 58687868, 250000);

--
-- Disparadores `cuenta`
--
DELIMITER $$
CREATE TRIGGER `tg_movimientos` AFTER UPDATE ON `cuenta` FOR EACH ROW BEGIN
  DECLARE diferencia DOUBLE;
  DECLARE id_historial INT;

  SELECT RCC.id_historial INTO id_historial FROM relacion_clientecuenta RCC WHERE RCC.id_cuenta = OLD.id_cuenta;

  IF NEW.saldo_cuenta < OLD.saldo_cuenta THEN
    SET diferencia = OLD.saldo_cuenta - NEW.saldo_cuenta;
    INSERT INTO movimientos (id_historial, variacion, fecha) VALUES (id_historial, -diferencia, NOW());
  ELSEIF NEW.saldo_cuenta > OLD.saldo_cuenta THEN
    SET diferencia = NEW.saldo_cuenta - OLD.saldo_cuenta;
    INSERT INTO movimientos (id_historial, variacion, fecha) VALUES (id_historial, diferencia, NOW());
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id_movimiento` int(11) NOT NULL,
  `id_historial` int(11) NOT NULL,
  `variacion` double NOT NULL,
  `fecha` date NOT NULL,
  `id_cajero` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`id_movimiento`, `id_historial`, `variacion`, `fecha`, `id_cajero`) VALUES
(4, 1, -60, '2024-05-07', 1),
(5, 1, 700, '2024-05-07', 1),
(6, 1, 1450, '2024-05-07', 1),
(7, 1, -500, '2024-05-07', 1),
(8, 1, 500, '2024-05-07', 1),
(9, 1, 100, '2024-05-07', 1),
(10, 1, 1200, '2024-05-07', 1),
(11, 1, -3800, '2024-05-07', 1),
(12, 1, 50000, '2024-05-07', 1),
(13, 1, -49100, '2024-05-07', 1),
(14, 1, 1000, '2024-05-07', 1),
(15, 1, 248100, '2024-05-07', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `relacion_clientecuenta`
--

CREATE TABLE `relacion_clientecuenta` (
  `id_historial` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_cuenta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `relacion_clientecuenta`
--

INSERT INTO `relacion_clientecuenta` (`id_historial`, `id_cliente`, `id_cuenta`) VALUES
(1, 9, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cajero`
--
ALTER TABLE `cajero`
  ADD PRIMARY KEY (`id_cajero`),
  ADD UNIQUE KEY `u_alias` (`alias`),
  ADD UNIQUE KEY `u_correo` (`correo`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `U_apellido_cliente` (`usuario`),
  ADD UNIQUE KEY `U_correo_cliente` (`correo`);

--
-- Indices de la tabla `cuenta`
--
ALTER TABLE `cuenta`
  ADD PRIMARY KEY (`id_cuenta`),
  ADD UNIQUE KEY `U_ncuenta` (`n_cuenta`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `fk_id_cajero` (`id_cajero`),
  ADD KEY `fk_id_historial` (`id_historial`) USING BTREE;

--
-- Indices de la tabla `relacion_clientecuenta`
--
ALTER TABLE `relacion_clientecuenta`
  ADD PRIMARY KEY (`id_historial`),
  ADD UNIQUE KEY `U_cliencuen` (`id_cliente`) USING BTREE,
  ADD UNIQUE KEY `id_cuenta` (`id_cuenta`) USING BTREE;

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cajero`
--
ALTER TABLE `cajero`
  MODIFY `id_cajero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `cuenta`
--
ALTER TABLE `cuenta`
  MODIFY `id_cuenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `relacion_clientecuenta`
--
ALTER TABLE `relacion_clientecuenta`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`id_historial`) REFERENCES `relacion_clientecuenta` (`id_historial`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movimientos_ibfk_2` FOREIGN KEY (`id_cajero`) REFERENCES `cajero` (`id_cajero`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `relacion_clientecuenta`
--
ALTER TABLE `relacion_clientecuenta`
  ADD CONSTRAINT `relacion_clientecuenta_ibfk_1` FOREIGN KEY (`id_cuenta`) REFERENCES `cuenta` (`id_cuenta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `relacion_clientecuenta_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
