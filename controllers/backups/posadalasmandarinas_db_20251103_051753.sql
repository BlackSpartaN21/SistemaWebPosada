-- Backup generado por Sistema Web Posada Las Mandarinas
-- Base de datos: `posadalasmandarinas_db`
-- Fecha: 2025-11-03 05:17:53

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Estructura de tabla para `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `documento_cliente` varchar(10) NOT NULL,
  `tipo_documento_cliente` enum('V','E','P','J') NOT NULL,
  `nombres_cliente` varchar(50) NOT NULL,
  `apellidos_cliente` varchar(50) NOT NULL,
  `telefono_cliente` varchar(11) NOT NULL,
  `correo_cliente` varchar(50) NOT NULL,
  `descripcion_cliente` varchar(255) DEFAULT NULL,
  `fecha_creacion_cliente` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`documento_cliente`),
  UNIQUE KEY `correo_cliente` (`correo_cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`documento_cliente`,`tipo_documento_cliente`,`nombres_cliente`,`apellidos_cliente`,`telefono_cliente`,`correo_cliente`,`descripcion_cliente`,`fecha_creacion_cliente`) VALUES 
(12777710,'V','Will','Medina',276345678,'wilis@gmail.com','ninguna','2025-03-27 10:19:29'),
(13577225,'V','Maria','Ramirez',4161398957,'mar@gmail.com','ninguna','2025-03-29 19:52:49'),
(13577896,'V','Daniel','Perez',2742210907,'an@gmail.com','Ninguna','2025-05-10 22:47:19'),
(15789654,'V','Fabio','Marquez',88888888888,'fabio@gmail.com','nada','2025-06-25 00:05:03'),
(17999999,'V','Daniel','Altuve',23423434385,'daniela@gmail.com','','2025-10-15 14:46:41'),
(22345098,'P','Juan','Peña',4147896541,'juans@gmail.com','ninguna','2025-03-27 09:37:20'),
(25369852,'V','Daniela','Hernandez',2746549874,'dan@gmail.com','viene de Anzoategui','2025-03-27 01:45:10'),
(30192253,'V','Cristofer','Medina R',2222222222,'crris@gmail.com','nada','2025-05-11 00:34:44'),
(31023654,'V','Marta','Habla',77777777777,'marta@gmail.com','nada','2025-05-11 00:41:52'),
('31092233-4','J','Valmorca','CA',2746549874,'valmor@gma.com','Empresa','2025-03-29 22:21:18'),
(31236459,'V','Cristian Miguel','Medina Rangel',4147896541,'cris@gmail.com','Nada','2025-03-27 00:25:12'),
(4563217899,'J','Seleccion Nacional de Karate','Del estado Merida',2742210666,'km@gmail.com','Merida - Venezuela','2025-05-11 00:40:23'),
(5555555555,'J','Asociacion','De Natacion del estado Merida',24244444444,'am@gamil.com','ninguna','2025-05-14 21:44:13'),
(8456321,'V','Fernan','El crack',55555555555,'fer@gmail.com','El Salvador','2025-05-11 00:38:51');

--
-- Estructura de tabla para `habitaciones`
--

DROP TABLE IF EXISTS `habitaciones`;
CREATE TABLE `habitaciones` (
  `id_habitacion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_habitacion` varchar(2) NOT NULL,
  `descripcion_habitacion` varchar(255) NOT NULL,
  `id_tipo_habitacion` int(11) NOT NULL,
  `estado_habitacion` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_habitacion`),
  UNIQUE KEY `nombre_habitacion` (`nombre_habitacion`),
  KEY `id_tipo_habitacion` (`id_tipo_habitacion`),
  CONSTRAINT `habitaciones_ibfk_1` FOREIGN KEY (`id_tipo_habitacion`) REFERENCES `tipo_habitaciones` (`id_tipo_habitacion`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `habitaciones`
--

INSERT INTO `habitaciones` (`id_habitacion`,`nombre_habitacion`,`descripcion_habitacion`,`id_tipo_habitacion`,`estado_habitacion`) VALUES 
(1,1,'',1,1),
(2,2,'',1,1),
(3,3,'',1,1),
(4,4,'',1,1),
(5,5,'',1,1),
(6,6,'',1,1),
(7,7,'',2,1),
(8,8,'',2,1),
(9,9,'',1,1),
(10,10,'',1,1),
(11,11,'',1,1),
(17,12,'Dos camas matrimoniales',6,1);

--
-- Estructura de tabla para `metodos_de_pago`
--

DROP TABLE IF EXISTS `metodos_de_pago`;
CREATE TABLE `metodos_de_pago` (
  `id_metodo_pago` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_metodo_pago` varchar(20) NOT NULL,
  `descripcion_metodo_pago` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_metodo_pago`),
  UNIQUE KEY `nombre_metodo_pago` (`nombre_metodo_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodos_de_pago`
--

INSERT INTO `metodos_de_pago` (`id_metodo_pago`,`nombre_metodo_pago`,`descripcion_metodo_pago`) VALUES 
(1,'Efectivo','Bolivares, Dolares o Pesos '),
(2,'Pago Movil','App de pago movil Bs.');

--
-- Estructura de tabla para `reservas`
--

DROP TABLE IF EXISTS `reservas`;
CREATE TABLE `reservas` (
  `id_reserva` int(11) NOT NULL AUTO_INCREMENT,
  `id_habitacion` int(11) NOT NULL,
  `documento_cliente` varchar(10) NOT NULL,
  `id_tarifa` int(11) NOT NULL,
  `fecha_llegada` datetime NOT NULL,
  `fecha_salida` datetime NOT NULL,
  `cantidad_personas` tinyint(3) unsigned NOT NULL,
  `monto_total` decimal(6,2) NOT NULL,
  `id_metodo_pago` int(11) NOT NULL,
  `estado_reserva` enum('Pendiente','Confirmada','Cancelada','Finalizada') NOT NULL,
  `observaciones_reserva` varchar(255) DEFAULT NULL,
  `origen_reserva` enum('Online','Física') NOT NULL,
  `fecha_creacion_reserva` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_reserva`),
  KEY `documento_cliente` (`documento_cliente`),
  KEY `id_tarifa` (`id_tarifa`),
  KEY `id_habitacion` (`id_habitacion`),
  KEY `id_metodo_pago` (`id_metodo_pago`),
  CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`documento_cliente`) REFERENCES `clientes` (`documento_cliente`),
  CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`id_tarifa`) REFERENCES `tarifas` (`id_tarifa`),
  CONSTRAINT `reservas_ibfk_3` FOREIGN KEY (`id_habitacion`) REFERENCES `habitaciones` (`id_habitacion`),
  CONSTRAINT `reservas_ibfk_4` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodos_de_pago` (`id_metodo_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id_reserva`,`id_habitacion`,`documento_cliente`,`id_tarifa`,`fecha_llegada`,`fecha_salida`,`cantidad_personas`,`monto_total`,`id_metodo_pago`,`estado_reserva`,`observaciones_reserva`,`origen_reserva`,`fecha_creacion_reserva`) VALUES 
(1,1,13577225,2,'2025-05-11 23:16:15','2025-05-12 23:16:15',1,10.00,1,'Finalizada','','','2025-05-11 23:16:15'),
(2,1,4563217899,2,'2025-05-12 23:22:00','2025-05-15 23:22:00',1,10.00,1,'Finalizada','','','2025-05-11 23:22:31'),
(3,1,13577896,1,'2025-05-12 00:34:42','2025-05-12 03:34:42',1,5.00,1,'Finalizada','','','2025-05-12 00:34:42'),
(4,1,'31092233-4',2,'2025-05-12 01:44:27','2025-05-13 01:44:27',2,10.00,1,'Finalizada','','','2025-05-12 01:44:27'),
(5,1,'31092233-4',2,'2025-05-12 01:45:37','2025-05-13 01:45:37',2,10.00,2,'Finalizada','','','2025-05-12 01:45:37'),
(6,2,13577225,2,'2025-05-12 10:45:42','2025-05-13 10:45:42',2,10.00,2,'Finalizada','','','2025-05-12 10:45:42'),
(7,1,5555555555,2,'2025-05-15 00:31:00','2025-05-16 00:31:00',2,10.00,1,'Finalizada','','','2025-05-15 00:31:00'),
(8,8,12777710,3,'2025-05-16 00:31:00','2025-05-17 00:32:00',3,15.00,2,'Finalizada','','','2025-05-15 00:32:07'),
(9,1,13577225,2,'2025-05-15 01:06:35','2025-05-16 01:06:35',2,10.00,1,'Finalizada','','','2025-05-15 01:06:35'),
(10,8,12777710,3,'2025-05-15 08:35:19','2025-05-16 08:35:19',3,15.00,2,'Finalizada','','','2025-05-15 08:35:19'),
(11,1,31236459,2,'2025-05-15 09:36:18','2025-05-16 09:36:18',1,10.00,1,'Finalizada','','','2025-05-15 09:36:18'),
(12,1,22345098,2,'2025-05-15 10:10:40','2025-05-16 10:10:40',2,10.00,2,'Finalizada','','','2025-05-15 10:10:40'),
(13,1,12777710,2,'2025-05-15 10:23:21','2025-05-16 10:23:21',1,10.00,1,'Finalizada','','','2025-05-15 10:23:21'),
(14,2,13577896,1,'2025-05-15 10:39:30','2025-05-15 13:39:30',2,5.00,1,'Finalizada','','','2025-05-15 10:39:30'),
(15,3,'31092233-4',2,'2025-05-15 10:43:23','2025-05-16 10:43:23',2,10.00,2,'Finalizada','','','2025-05-15 10:43:23'),
(17,2,13577225,2,'2025-06-06 15:42:00','2025-06-08 15:42:00',2,20.00,1,'Finalizada','','','2025-06-04 15:43:05'),
(18,2,22345098,1,'2025-06-04 16:01:17','2025-06-04 19:01:17',2,5.00,2,'Finalizada','','','2025-06-04 16:01:17'),
(19,2,31236459,2,'2025-06-16 23:15:34','2025-06-17 23:15:34',2,10.00,1,'Finalizada','','','2025-06-16 23:15:34'),
(20,1,'31092233-4',2,'2025-06-18 22:18:59','2025-06-19 22:18:59',1,10.00,1,'Finalizada','','','2025-06-18 22:18:59'),
(21,1,12777710,1,'2025-06-18 22:19:00','2025-06-18 22:20:00',1,5.00,1,'Finalizada','','','2025-06-18 22:20:04'),
(22,2,12777710,2,'2025-06-24 21:59:00','2025-06-25 21:59:00',2,10.00,1,'Finalizada','','','2025-06-24 22:01:32'),
(23,1,13577225,2,'2025-06-24 22:05:00','2025-06-28 22:05:00',2,40.00,2,'Finalizada','','','2025-06-24 22:05:47'),
(24,2,15789654,1,'2025-06-25 01:30:32','2025-06-25 04:30:32',2,5.00,1,'Finalizada','','','2025-06-25 01:30:32'),
(25,2,12777710,2,'2025-07-08 00:25:00','2025-07-20 00:25:00',2,120.00,1,'Finalizada','','','2025-07-08 00:25:34'),
(26,1,15789654,1,'2025-07-09 11:36:28','2025-07-09 14:36:28',2,5.00,1,'Finalizada','','','2025-07-09 11:36:28'),
(27,7,'31092233-4',3,'2025-07-09 11:37:01','2025-07-10 11:37:01',3,15.00,2,'Finalizada','','','2025-07-09 11:37:01'),
(28,2,15789654,2,'2025-07-16 10:17:00','2025-07-21 10:17:00',2,50.00,1,'Finalizada','','','2025-07-16 10:18:13'),
(29,11,12777710,1,'2025-10-14 12:19:30','2025-10-14 15:19:30',2,5.00,1,'Finalizada','','','2025-10-14 12:19:30'),
(31,1,13577225,2,'2025-10-15 09:33:40','2025-10-16 09:33:40',2,10.00,2,'Finalizada','','','2025-10-15 09:33:40'),
(32,2,31023654,2,'2025-10-17 10:56:00','2025-10-20 10:56:00',1,30.00,2,'Finalizada','','','2025-10-17 10:56:51'),
(33,2,25369852,2,'2025-10-17 14:01:00','2025-10-19 14:01:00',2,20.00,2,'Finalizada','','','2025-10-17 13:56:50'),
(34,1,12777710,1,'2025-10-17 13:57:28','2025-10-17 16:57:28',2,5.00,1,'Finalizada','','','2025-10-17 13:57:28'),
(35,3,17999999,2,'2025-10-17 13:58:14','2025-10-18 13:58:14',1,10.00,1,'Finalizada','','','2025-10-17 13:58:14'),
(36,1,12777710,2,'2025-10-20 12:11:00','2025-10-24 12:11:00',2,40.00,2,'Finalizada','','','2025-10-20 12:11:36'),
(40,1,13577225,1,'2025-11-01 19:53:41','2025-11-01 22:53:41',2,5.00,2,'Finalizada','','','2025-11-01 19:53:41'),
(41,8,'31092233-4',3,'2025-11-01 19:55:00','2025-11-04 19:55:00',3,45.00,1,'Finalizada','','','2025-11-01 19:56:29'),
(42,17,15789654,7,'2025-11-01 20:05:00','2025-11-04 20:05:00',4,60.00,2,'Finalizada','','','2025-11-01 20:06:14');

--
-- Estructura de tabla para `tarifas`
--

DROP TABLE IF EXISTS `tarifas`;
CREATE TABLE `tarifas` (
  `id_tarifa` int(11) NOT NULL AUTO_INCREMENT,
  `id_tipo_habitacion` int(11) NOT NULL,
  `tipo_tarifa` enum('3 Horas','24 Horas') NOT NULL,
  `precio_tarifa` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id_tarifa`),
  KEY `id_tipo_habitacion` (`id_tipo_habitacion`),
  CONSTRAINT `tarifas_ibfk_1` FOREIGN KEY (`id_tipo_habitacion`) REFERENCES `tipo_habitaciones` (`id_tipo_habitacion`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarifas`
--

INSERT INTO `tarifas` (`id_tarifa`,`id_tipo_habitacion`,`tipo_tarifa`,`precio_tarifa`) VALUES 
(1,1,'3 Horas',5.00),
(2,1,'24 Horas',10.00),
(3,2,'24 Horas',15.00),
(7,6,'24 Horas',20.00);

--
-- Estructura de tabla para `tipo_habitaciones`
--

DROP TABLE IF EXISTS `tipo_habitaciones`;
CREATE TABLE `tipo_habitaciones` (
  `id_tipo_habitacion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_tipo_habitacion` varchar(20) NOT NULL,
  `capacidad_tipo_habitacion` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id_tipo_habitacion`),
  UNIQUE KEY `nombre_tipo_habitacion` (`nombre_tipo_habitacion`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_habitaciones`
--

INSERT INTO `tipo_habitaciones` (`id_tipo_habitacion`,`nombre_tipo_habitacion`,`capacidad_tipo_habitacion`) VALUES 
(1,'Matrimonial',2),
(2,'Triple',3),
(6,'Doble',4);

--
-- Estructura de tabla para `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(24) NOT NULL,
  `apellido_usuario` varchar(24) NOT NULL,
  `correo_usuario` varchar(50) NOT NULL,
  `contrasena_usuario` varchar(255) NOT NULL,
  `rol_usuario` enum('Recepcionista','Administrador') NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `correo_usuario` (`correo_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`,`nombre_usuario`,`apellido_usuario`,`correo_usuario`,`contrasena_usuario`,`rol_usuario`,`fecha_creacion`) VALUES 
(2,'Cristofer','Medina','cristofermedinar6@gmail.com','$2y$10$wFYcQ3Oua/XgJDaIxKaFxOOsGx8spjyJfLnBU8.KihGwBeaYvGqmK','Administrador','2025-07-15 22:23:52'),
(3,'Fabian','Sánchez','fabian@gmail.com','$2y$10$wmlxpWMMRWxoM5B1DzrYfOSwQUWMtkBB9P8wi0tT8lTA/wVEUge1C','Recepcionista','2025-07-16 01:44:19'),
(4,'Mary','Rangel','maryrangel06@gmail.com','$2y$10$k6dw57Cze6ENrTznOMMMiO8vkwLYPQm6943LQ3mCx5gC505LLBFyq','Administrador','2025-10-08 10:04:11'),
(12,'Wilson','Santander','wilson@gmail.com','$2y$10$PxtZ3/Va1FA7nPpxEWRRK.f15uokomUDVrVn68MES7M4OtM65hfjK','Recepcionista','2025-11-02 11:38:25');

COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
