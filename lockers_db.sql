-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-04-2025 a las 21:49:09
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `lockers_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id`, `usuario`, `contrasena`) VALUES
(1, 'admin', 'admin'),
(2, 'admin2', '123456');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `boleta` int(11) NOT NULL,
  `solicitud` enum('Primera vez','Renovación') NOT NULL,
  `casilleroAnt` int(3) DEFAULT NULL,
  `nombre` varchar(50) NOT NULL,
  `primerAp` varchar(50) NOT NULL,
  `segundoAp` varchar(50) DEFAULT NULL,
  `telefono` varchar(10) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `curp` varchar(18) NOT NULL,
  `estatura` decimal(3,2) NOT NULL,
  `credencial` varchar(255) DEFAULT NULL,
  `horario` varchar(255) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`boleta`, `solicitud`, `casilleroAnt`, `nombre`, `primerAp`, `segundoAp`, `telefono`, `correo`, `curp`, `estatura`, `credencial`, `horario`, `usuario`, `contrasena`) VALUES
(2023104587, 'Renovación', 99, 'María Fernanda', 'López', 'Hernández', '5536718920', 'maria.lopez@alumno.ipn.mx', 'LOHF000315MDFPRN01', 1.68, '/ProyectoWeb/Docs/Credenciales/2023104587_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023104587_horario.pdf', 'maria_lopez', '123456'),
(2023209845, 'Primera vez', NULL, 'Juan Carlos', 'Martínez ', 'Pérez', '5578942316', 'juan.martinez@alumno.ipn.mx', 'MAPJ991210HDFTRN02', 1.78, '/ProyectoWeb/Docs/Credenciales/2023209845_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023209845_horario.pdf', 'juan_mtz', '123456'),
(2023630100, 'Renovación', 10, 'Lucia Fernanda', 'Galvan', 'Díaz ', '5538589632', 'jgalvand1900@alumno.ipn.mx', 'GARC990301HSLBRL04', 1.65, '/ProyectoWeb/Docs/Credenciales/2023630100_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630100_horario.pdf', 'LuciFer', '666666'),
(2023630109, 'Renovación', 2, 'Teodora Vicenta', 'de la Purísima Concepción', 'de la Inmaculada Trinidad Villavicencio', '5512345678', 'teodora.villavicencio@alumno.ipn.mx', 'VITD730101HDFLLD09', 1.53, '/ProyectoWeb/Docs/Credenciales/2023630109_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630109_horario.pdf', 'duquesa_teodora73', '123456'),
(2023630110, 'Renovación', 60, 'Juan', 'Perez', 'Rodriguez', '5538762332', 'jperezr1900@alumno.ipn.mx', 'PERE789456MTLRBN05', 1.78, '/ProyectoWeb/Docs/Credenciales/2023630110_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630110_horario.pdf', 'perezJ', '123456'),
(2023630111, 'Primera vez', NULL, 'Guadalupe de Jesús', 'Gonzalez', 'Pérez', '1234567890', 'ggonzalesp1900@alumno.ipn.mx', 'GOME123456HDFRRL09', 1.55, '/ProyectoWeb/Docs/Credenciales/2023630111_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630111_horario.pdf', 'guadalupeG', '123456'),
(2023630112, 'Renovación', 12, 'Ana María', 'López ', 'Fernández', '5523456789', 'alopezf1900@alumno.ipn.mx', 'LOPE456789MDFLNR02', 1.58, '/ProyectoWeb/Docs/Credenciales/2023630112_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630112_horario.pdf', 'anaML', '123456'),
(2023630113, 'Primera vez', NULL, 'Pedro', 'Martínez ', 'López', '5534567890', 'pmartinezl1900@alumno.ipn.mx', 'MART020304HDFJLR08', 1.80, '/ProyectoWeb/Docs/Credenciales/2023630113_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630113_horario.pdf', 'PMartinez', '123456'),
(2023630114, 'Renovación', 51, 'Mariana ', 'Pérez ', 'García', '5545678901', 'mperezg1900@alumno.ipn.mx', 'PERE789456MTLRBN05', 1.68, '/ProyectoWeb/Docs/Credenciales/2023630114_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630114_horario.pdf', 'MarPG', '123456'),
(2023630115, 'Primera vez', NULL, 'Ricardo ', 'Ramírez ', 'López', '5556789012', 'rramirezl1900@alumno.ipn.mx', 'RAMI345678MDFLSN01', 1.70, '/ProyectoWeb/Docs/Credenciales/2023630115_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630115_horario.pdf', 'RichiR', '123456'),
(2023630116, 'Primera vez', NULL, 'Laura ', 'Hernández ', 'Ruiz', '5567890123', 'lhernandezr1900@alumno.ipn.mx', 'HERN876543MNLHRZ07', 1.60, '/ProyectoWeb/Docs/Credenciales/2023630116_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630116_horario.pdf', 'LBozzo', '123456'),
(2023630117, 'Primera vez', NULL, 'Maximiliano Aurelio', 'Gutiérrez', 'de la Torre', '5512345678', 'magutierrezdl1900@alumno.ipn.mx', 'GUTM890123HDFLRX04', 1.85, '/ProyectoWeb/Docs/Credenciales/2023630117_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630117_horario.pdf', 'TorreAM', '123456'),
(2023630118, 'Renovación', 1, 'Isidora Valentina', 'Zamorín ', 'del Castillo', '5523456789', 'izamorindc1900@alumno.ipn.mx', 'ZAVI910234MDFLNS02', 1.50, '/ProyectoWeb/Docs/Credenciales/2023630118_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630118_horario.pdf', 'ZarzamoraV', '123456'),
(2023630119, 'Primera vez', NULL, ' Feliciana Inés Cruz', 'López ', 'de la Rocha', '5501234567', 'ficruzldr1900@alumno.ipn.mx', 'CRUF640923MDFLRR10', 1.90, '/ProyectoWeb/Docs/Credenciales/2023630119_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630119_horario.pdf', '3delaRocha', '123456'),
(2023630120, 'Primera vez', NULL, 'Héctor Rigel', 'Ocaña', 'Castro', '5567890125', 'hocañac1900@alumno.ipn.mx', 'OHCR640923MDFLRR10', 1.70, '/ProyectoWeb/Docs/Credenciales/2023630120_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630120_horario.pdf', 'HectorR', '123456'),
(2023630157, 'Renovación', 15, 'Areli Alejandra', 'Guevara', 'Badillo', '5538762357', 'aguevarab1900@alumno.ipn.mx', 'GUBA040725MDFVDRA2', 1.61, '/ProyectoWeb/Docs/Credenciales/2023630157_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630157_horario.pdf', 'venuz', '123456'),
(2023630177, 'Renovación', 3, 'Jugemu Gokono', 'Suriki Rekai', 'Jari Suiki', '5598765432', 'jugemu.suriki@alumno.ipn.mx', 'JUGG850101HDFLLD09', 1.88, '/ProyectoWeb/Docs/Credenciales/2023630177_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630177_horario.pdf', 'jugemu_suriki', '369852'),
(2023630202, 'Renovación', 8, 'Ana', 'Díaz', 'Orbe', '5536985214', 'adiazo1900@alumno.ipn.mx', 'ADOR040725MDFVDRA2', 1.48, '/ProyectoWeb/Docs/Credenciales/2023630202_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023630202_horario.pdf', 'orbeA', '123456'),
(2023633333, 'Primera vez', NULL, 'James', 'Garfield', 'Davis', '5567893333', 'jgarfieldd1900@alumno.ipn.mx', 'JAGD040923MDFLRR10', 1.33, '/ProyectoWeb/Docs/Credenciales/2023633333_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023633333_horario.pdf', 'Garfield', '147852'),
(2023638566, 'Primera vez', NULL, 'Phineas T', 'Kettletree', 'Squire', '5567898566', 'pkettletrees1900@alumno.ipn.mx', 'PTKS640923MDFLRR10', 1.44, '/ProyectoWeb/Docs/Credenciales/2023638566_credencial.pdf', '/ProyectoWeb/Docs/Horarios/2023638566_horario.pdf', 'Bobble', '123456');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `casilleros`
--

CREATE TABLE `casilleros` (
  `noCasillero` int(11) NOT NULL,
  `altura` decimal(3,2) NOT NULL,
  `estado` enum('Disponible','Asignado') DEFAULT 'Disponible',
  `boletaAsignada` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `casilleros`
--

INSERT INTO `casilleros` (`noCasillero`, `altura`, `estado`, `boletaAsignada`) VALUES
(1, 0.10, 'Asignado', 2023630118),
(2, 0.10, 'Asignado', 2023630109),
(3, 0.10, 'Asignado', 2023630177),
(4, 0.10, 'Disponible', NULL),
(5, 0.10, 'Disponible', NULL),
(6, 0.10, 'Disponible', NULL),
(7, 0.10, 'Disponible', NULL),
(8, 0.10, 'Asignado', 2023630202),
(9, 0.10, 'Disponible', NULL),
(10, 0.10, 'Asignado', 2023630100),
(11, 0.10, 'Disponible', NULL),
(12, 0.10, 'Disponible', NULL),
(13, 0.10, 'Disponible', NULL),
(14, 0.10, 'Disponible', NULL),
(15, 0.10, 'Asignado', 2023630157),
(16, 0.10, 'Disponible', NULL),
(17, 0.10, 'Disponible', NULL),
(18, 0.10, 'Disponible', NULL),
(19, 0.10, 'Disponible', NULL),
(20, 0.10, 'Disponible', NULL),
(21, 0.10, 'Disponible', NULL),
(22, 0.10, 'Disponible', NULL),
(23, 0.10, 'Disponible', NULL),
(24, 0.10, 'Disponible', NULL),
(25, 0.10, 'Disponible', NULL),
(26, 0.55, 'Disponible', NULL),
(27, 0.55, 'Disponible', NULL),
(28, 0.55, 'Disponible', NULL),
(29, 0.55, 'Disponible', NULL),
(30, 0.55, 'Disponible', NULL),
(31, 0.55, 'Disponible', NULL),
(32, 0.55, 'Disponible', NULL),
(33, 0.55, 'Disponible', NULL),
(34, 0.55, 'Disponible', NULL),
(35, 0.55, 'Disponible', NULL),
(36, 0.55, 'Disponible', NULL),
(37, 0.55, 'Disponible', NULL),
(38, 0.55, 'Disponible', NULL),
(39, 0.55, 'Disponible', NULL),
(40, 0.55, 'Asignado', 2023630113),
(41, 0.55, 'Disponible', NULL),
(42, 0.55, 'Disponible', NULL),
(43, 0.55, 'Disponible', NULL),
(44, 0.55, 'Disponible', NULL),
(45, 0.55, 'Disponible', NULL),
(46, 0.55, 'Disponible', NULL),
(47, 0.55, 'Disponible', NULL),
(48, 0.55, 'Disponible', NULL),
(49, 0.55, 'Disponible', NULL),
(50, 0.55, 'Disponible', NULL),
(51, 1.00, 'Asignado', 2023630114),
(52, 1.00, 'Disponible', NULL),
(53, 1.00, 'Disponible', NULL),
(54, 1.00, 'Disponible', NULL),
(55, 1.00, 'Asignado', 2023638566),
(56, 1.00, 'Disponible', NULL),
(57, 1.00, 'Disponible', NULL),
(58, 1.00, 'Disponible', NULL),
(59, 1.00, 'Disponible', NULL),
(60, 1.00, 'Asignado', 2023630110),
(61, 1.00, 'Disponible', NULL),
(62, 1.00, 'Disponible', NULL),
(63, 1.00, 'Disponible', NULL),
(64, 1.00, 'Disponible', NULL),
(65, 1.00, 'Disponible', NULL),
(66, 1.00, 'Disponible', NULL),
(67, 1.00, 'Disponible', NULL),
(68, 1.00, 'Disponible', NULL),
(69, 1.00, 'Disponible', NULL),
(70, 1.00, 'Disponible', NULL),
(71, 1.00, 'Disponible', NULL),
(72, 1.00, 'Disponible', NULL),
(73, 1.00, 'Disponible', NULL),
(74, 1.00, 'Disponible', NULL),
(75, 1.00, 'Disponible', NULL),
(76, 1.45, 'Disponible', NULL),
(77, 1.45, 'Disponible', NULL),
(78, 1.45, 'Disponible', NULL),
(79, 1.45, 'Disponible', NULL),
(80, 1.45, 'Disponible', NULL),
(81, 1.45, 'Disponible', NULL),
(82, 1.45, 'Disponible', NULL),
(83, 1.45, 'Disponible', NULL),
(84, 1.45, 'Disponible', NULL),
(85, 1.45, 'Disponible', NULL),
(86, 1.45, 'Disponible', NULL),
(87, 1.45, 'Disponible', NULL),
(88, 1.45, 'Disponible', NULL),
(89, 1.45, 'Disponible', NULL),
(90, 1.45, 'Disponible', NULL),
(91, 1.45, 'Disponible', NULL),
(92, 1.45, 'Disponible', NULL),
(93, 1.45, 'Disponible', NULL),
(94, 1.45, 'Disponible', NULL),
(95, 1.45, 'Disponible', NULL),
(96, 1.45, 'Disponible', NULL),
(97, 1.45, 'Disponible', NULL),
(98, 1.45, 'Disponible', NULL),
(99, 1.45, 'Asignado', 2023104587),
(100, 1.45, 'Disponible', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id` int(11) NOT NULL,
  `noBoleta` int(11) NOT NULL,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `estadoSolicitud` enum('Pendiente','Aprobada','Lista de espera') DEFAULT 'Pendiente',
  `fechaAprobacion` datetime DEFAULT NULL,
  `comprobantePago` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`id`, `noBoleta`, `fechaRegistro`, `estadoSolicitud`, `fechaAprobacion`, `comprobantePago`) VALUES
(1, 2023630157, '2025-01-14 21:29:33', 'Aprobada', '2025-01-14 22:18:39', NULL),
(2, 2023630110, '2025-01-14 20:39:56', 'Aprobada', '2025-01-14 23:31:05', NULL),
(3, 2023630111, '2025-01-14 20:40:12', 'Lista de espera', NULL, NULL),
(4, 2023630112, '2025-01-14 20:40:36', 'Pendiente', NULL, NULL),
(5, 2023630113, '2025-01-14 20:40:53', 'Aprobada', '2025-01-14 20:41:02', NULL),
(6, 2023630114, '2025-01-14 20:41:34', 'Aprobada', '2025-01-14 23:28:36', NULL),
(7, 2023630115, '2025-01-14 20:42:11', 'Pendiente', NULL, NULL),
(8, 2023630116, '2025-01-14 20:42:24', 'Pendiente', NULL, NULL),
(9, 2023630117, '2025-01-14 20:42:41', 'Pendiente', NULL, NULL),
(10, 2023630118, '2025-01-14 20:42:53', 'Aprobada', '2025-01-14 22:52:59', NULL),
(11, 2023630119, '2025-01-14 20:43:18', 'Pendiente', NULL, NULL),
(12, 2023630120, '2025-01-14 20:45:08', 'Pendiente', NULL, NULL),
(13, 2023638566, '2025-01-14 20:45:52', 'Aprobada', '2025-01-14 20:52:58', '/ProyectoWeb/Docs/Comprobantes/2023638566_comprobante.pdf'),
(14, 2023633333, '2025-01-14 20:46:14', 'Pendiente', NULL, NULL),
(15, 2023630109, '2025-01-14 20:46:34', 'Aprobada', '2025-01-14 20:46:41', '/ProyectoWeb/Docs/Comprobantes/2023630109_comprobante.pdf'),
(16, 2023630177, '2025-01-14 20:47:06', 'Aprobada', '2025-01-14 20:47:11', NULL),
(17, 2023630100, '2025-01-14 20:47:33', 'Aprobada', '2025-01-14 20:51:30', '/ProyectoWeb/Docs/Comprobantes/2023630100_comprobante.pdf'),
(18, 2023104587, '2025-01-14 20:47:46', 'Aprobada', '2025-01-14 23:06:43', '/ProyectoWeb/Docs/Comprobantes/2023104587_comprobante.pdf'),
(19, 2023209845, '2025-01-14 20:48:10', 'Lista de espera', NULL, NULL),
(20, 2023630202, '2025-01-14 21:41:57', 'Aprobada', '2025-01-14 21:41:57', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`usuario`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`boleta`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `casilleros`
--
ALTER TABLE `casilleros`
  ADD PRIMARY KEY (`noCasillero`),
  ADD KEY `boletaAsignada` (`boletaAsignada`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `noBoleta` (`noBoleta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `casilleros`
--
ALTER TABLE `casilleros`
  ADD CONSTRAINT `casilleros_ibfk_1` FOREIGN KEY (`boletaAsignada`) REFERENCES `alumnos` (`boleta`);

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`noBoleta`) REFERENCES `alumnos` (`boleta`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
