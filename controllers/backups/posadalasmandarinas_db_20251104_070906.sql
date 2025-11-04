-- Backup generado por Sistema Web Posada Las Mandarinas
-- Base de datos: `posadalasmandarinas_db`
-- Fecha: 2025-11-04 07:09:06

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Estructura de tabla para `bitacora`
--

DROP TABLE IF EXISTS `bitacora`;
CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL,
  `modulo` varchar(40) NOT NULL,
  `accion` varchar(40) NOT NULL,
  `detalle` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `resultado` enum('OK','ERROR') NOT NULL DEFAULT 'OK',
  PRIMARY KEY (`id_bitacora`),
  KEY `idx_fecha` (`fecha`),
  KEY `idx_modulo` (`modulo`),
  KEY `idx_accion` (`accion`),
  KEY `idx_usuario` (`id_usuario`),
  CONSTRAINT `bitacora_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bitacora`
--

INSERT INTO `bitacora` (`id_bitacora`,`fecha`,`id_usuario`,`modulo`,`accion`,`detalle`,`ip`,`user_agent`,`resultado`) VALUES 
(1,'2025-11-04 00:41:07',2,'Habitaciones','crear','{\"nombre\":\"PRUEBA-BITA-001\",\"id_tipo\":1,\"ex\":\"SQLSTATE[42S02]: Base table or view not found: 1146 Table \'posadalasmandarinas_db.tipos_habitacion\' doesn\'t exist\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','ERROR'),
(2,'2025-11-04 00:48:26',2,'Backups','listar','{\"count\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(3,'2025-11-04 00:48:35',2,'Backups','crear','{\"db\":\"posadalasmandarinas_db\",\"file\":\"posadalasmandarinas_db_20251104_054835.sql\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(4,'2025-11-04 00:48:35',2,'Backups','listar','{\"count\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(5,'2025-11-04 00:57:42',2,'Clientes','editar','{\"doc_original\":\"29852369\",\"documento_nuevo\":\"29852369\",\"correo\":\"carlo@gmail.com\",\"filas_afectadas\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(6,'2025-11-04 01:04:45',2,'Usuarios','eliminar','{\"target_id\":13,\"target_rol\":\"Recepcionista\",\"actor_id\":2,\"filas_borradas\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(7,'2025-11-04 01:05:30',2,'Reportes','exportar','{\"seccion\":\"reservas\",\"filtros\":{\"desde\":\"\",\"hasta\":\"\",\"estado\":\"\"},\"total\":41,\"monto\":690}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(8,'2025-11-04 01:09:15',2,'Habitaciones','excepcion','{\"action\":\"tipos\",\"ex\":\"SQLSTATE[42S02]: Base table or view not found: 1146 Table \'posadalasmandarinas_db.tipos_habitacion\' doesn\'t exist\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','ERROR'),
(9,'2025-11-04 01:09:15',2,'Habitaciones','excepcion','{\"action\":\"list\",\"ex\":\"SQLSTATE[42S02]: Base table or view not found: 1146 Table \'posadalasmandarinas_db.tipos_habitacion\' doesn\'t exist\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','ERROR'),
(10,'2025-11-04 01:09:27',2,'Habitaciones','excepcion','{\"action\":\"tipos\",\"ex\":\"SQLSTATE[42S02]: Base table or view not found: 1146 Table \'posadalasmandarinas_db.tipos_habitacion\' doesn\'t exist\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','ERROR'),
(11,'2025-11-04 01:09:27',2,'Habitaciones','excepcion','{\"action\":\"list\",\"ex\":\"SQLSTATE[42S02]: Base table or view not found: 1146 Table \'posadalasmandarinas_db.tipos_habitacion\' doesn\'t exist\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','ERROR'),
(12,'2025-11-04 01:09:29',2,'Habitaciones','excepcion','{\"action\":\"tipos\",\"ex\":\"SQLSTATE[42S02]: Base table or view not found: 1146 Table \'posadalasmandarinas_db.tipos_habitacion\' doesn\'t exist\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','ERROR'),
(13,'2025-11-04 01:09:29',2,'Habitaciones','excepcion','{\"action\":\"list\",\"ex\":\"SQLSTATE[42S02]: Base table or view not found: 1146 Table \'posadalasmandarinas_db.tipos_habitacion\' doesn\'t exist\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','ERROR'),
(14,'2025-11-04 01:10:42',2,'Habitaciones','excepcion','{\"action\":\"tipos\",\"ex\":\"SQLSTATE[42S02]: Base table or view not found: 1146 Table \'posadalasmandarinas_db.tipos_habitacion\' doesn\'t exist\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','ERROR'),
(15,'2025-11-04 01:10:42',2,'Habitaciones','excepcion','{\"action\":\"list\",\"ex\":\"SQLSTATE[42S02]: Base table or view not found: 1146 Table \'posadalasmandarinas_db.tipos_habitacion\' doesn\'t exist\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','ERROR'),
(16,'2025-11-04 01:11:56',2,'Habitaciones','tipos','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(17,'2025-11-04 01:11:56',2,'Habitaciones','listar','{\"total\":11}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(18,'2025-11-04 01:13:47',2,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":2,\"email\":\"cristofermedinar6@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(19,'2025-11-04 01:14:42',2,'Autenticación','logout','{\"id_usuario\":2,\"ip\":\"::1\",\"remember\":\"no\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(20,'2025-11-04 01:14:47',NULL,'Autenticación','login','{\"motivo\":\"invalid_credentials\",\"ip\":\"::1\",\"email\":\"fab@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','ERROR'),
(21,'2025-11-04 01:14:54',2,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":2,\"email\":\"cristofermedinar6@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(22,'2025-11-04 01:16:51',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(23,'2025-11-04 01:16:51',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(24,'2025-11-04 01:16:53',2,'Habitaciones','tipos','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(25,'2025-11-04 01:16:53',2,'Habitaciones','listar','{\"total\":11}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(26,'2025-11-04 01:17:02',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(27,'2025-11-04 01:17:02',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(28,'2025-11-04 01:23:27',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"24 Horas\",\"precio\":10}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(29,'2025-11-04 01:23:27',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"24 Horas\",\"precio\":10}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(30,'2025-11-04 01:23:27',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"24 Horas\",\"precio\":10}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(31,'2025-11-04 01:23:28',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(32,'2025-11-04 01:23:28',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(33,'2025-11-04 01:23:28',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(34,'2025-11-04 01:23:34',2,'Reservas','crear','{\"id_reserva\":0,\"id_habitacion\":1,\"documento_cliente\":\"29852369\",\"tipo_tarifa\":\"3 Horas\",\"dias\":1,\"monto_total\":5,\"metodo_pago\":2,\"llegada\":\"2025-11-04 01:23:34\",\"salida\":\"2025-11-04 04:23:34\",\"estado\":\"Confirmada\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(35,'2025-11-04 01:23:34',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(36,'2025-11-04 01:23:34',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(37,'2025-11-04 01:32:06',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(38,'2025-11-04 01:32:06',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(39,'2025-11-04 01:32:12',2,'Habitaciones','vaciar','{\"id_habitacion\":1,\"hab_afectadas\":1,\"reservas_finalizadas\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(40,'2025-11-04 01:32:13',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(41,'2025-11-04 01:32:13',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(42,'2025-11-04 01:32:15',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(43,'2025-11-04 01:32:15',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(44,'2025-11-04 01:32:29',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(45,'2025-11-04 01:32:29',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(46,'2025-11-04 01:32:58',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(47,'2025-11-04 01:32:58',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(48,'2025-11-04 01:33:12',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(49,'2025-11-04 01:33:12',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(50,'2025-11-04 01:33:15',2,'Backups','listar','{\"count\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(51,'2025-11-04 01:33:20',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(52,'2025-11-04 01:33:20',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(53,'2025-11-04 01:33:38',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(54,'2025-11-04 01:33:38',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(55,'2025-11-04 01:42:07',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(56,'2025-11-04 01:42:07',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(57,'2025-11-04 01:42:43',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(58,'2025-11-04 01:42:43',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(59,'2025-11-04 01:42:49',2,'Habitaciones','tipos','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(60,'2025-11-04 01:42:50',2,'Habitaciones','listar','{\"total\":11}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(61,'2025-11-04 01:42:50',2,'TiposHabitacion','listar','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(62,'2025-11-04 01:42:50',2,'Tarifas','listar','{\"total\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(63,'2025-11-04 01:45:00',2,'Autenticación','logout','{\"id_usuario\":2,\"ip\":\"::1\",\"remember\":\"no\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(64,'2025-11-04 01:45:07',3,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":3,\"email\":\"fabian@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(65,'2025-11-04 01:45:07',3,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(66,'2025-11-04 01:45:08',3,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(67,'2025-11-04 01:45:18',3,'Reportes','exportar','{\"seccion\":\"reservas\",\"filtros\":{\"desde\":\"\",\"hasta\":\"\",\"estado\":\"\"},\"total\":42,\"monto\":695}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(68,'2025-11-04 01:45:28',3,'Autenticación','logout','{\"id_usuario\":3,\"ip\":\"::1\",\"remember\":\"no\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(69,'2025-11-04 01:45:34',2,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":2,\"email\":\"cristofermedinar6@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(70,'2025-11-04 01:45:34',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(71,'2025-11-04 01:45:34',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(72,'2025-11-04 01:49:47',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(73,'2025-11-04 01:49:47',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(74,'2025-11-04 02:03:10',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(75,'2025-11-04 02:03:10',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(76,'2025-11-04 02:03:24',2,'Habitaciones','tipos','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(77,'2025-11-04 02:03:24',2,'Habitaciones','listar','{\"total\":11}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(78,'2025-11-04 02:03:24',2,'TiposHabitacion','listar','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(79,'2025-11-04 02:03:24',2,'Tarifas','listar','{\"total\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(80,'2025-11-04 02:09:02',2,'Backups','listar','{\"count\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK');

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
(29852369,'V','Carlos','Alarcon',04124569873,'carlo@gmail.com','','2025-11-04 00:57:01'),
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `habitaciones`
--

INSERT INTO `habitaciones` (`id_habitacion`,`nombre_habitacion`,`descripcion_habitacion`,`id_tipo_habitacion`,`estado_habitacion`) VALUES 
(1,1,'',1,1),
(2,2,'',1,0),
(3,3,'',1,1),
(4,4,'',1,1),
(5,5,'',1,1),
(6,6,'',1,1),
(7,7,'',2,1),
(8,8,'',2,1),
(9,9,'',1,1),
(10,10,'',1,1),
(11,11,'',1,1);

--
-- Estructura de tabla para `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `email` varchar(190) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `last_attempt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `locked_until` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `email` (`email`),
  KEY `locked_until` (`locked_until`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `login_attempts`
--

INSERT INTO `login_attempts` (`id`,`ip`,`email`,`attempts`,`last_attempt`,`locked_until`) VALUES 
(1,'::1','fab@gmail.com',4,'2025-11-04 01:14:47',NULL),
(2,'::1','efefe@gm.com',1,'2025-11-03 13:01:32',NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(43,2,12777710,2,'2025-11-03 10:34:00','2025-11-07 10:34:00',2,40.00,1,'Finalizada','','','2025-11-03 10:34:47'),
(44,4,13577896,1,'2025-11-03 10:56:32','2025-11-03 13:56:32',2,5.00,2,'Finalizada','','','2025-11-03 10:56:32'),
(45,5,'31092233-4',2,'2025-11-03 10:58:05','2025-11-04 10:58:05',2,10.00,1,'Finalizada','','','2025-11-03 10:58:05'),
(46,2,15789654,1,'2025-11-03 16:06:23','2025-11-03 19:06:23',2,5.00,1,'Confirmada','','','2025-11-03 16:06:23'),
(47,7,12777710,3,'2025-11-04 00:43:47','2025-11-05 00:43:47',3,15.00,1,'Finalizada','','','2025-11-04 00:43:47'),
(48,1,29852369,1,'2025-11-04 01:23:34','2025-11-04 04:23:34',2,5.00,2,'Finalizada','','','2025-11-04 01:23:34');

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
(3,2,'24 Horas',15.00);

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
(2,'Triple',3);

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`,`nombre_usuario`,`apellido_usuario`,`correo_usuario`,`contrasena_usuario`,`rol_usuario`,`fecha_creacion`) VALUES 
(2,'Cristofer','Medina','cristofermedinar6@gmail.com','$2y$12$6S.KkBoYiThhnXV2Mf3uw.q32Z7kQqVs8JHa.aZIay8yC.6.aHCkG','Administrador','2025-07-15 22:23:52'),
(3,'Fabian','Sánchez','fabian@gmail.com','$2y$12$c7DNijSQmConrTdOV6Y0FuW6BgOEBMNUQKy.JXB7FFeRACehX0iAG','Recepcionista','2025-07-16 01:44:19'),
(4,'Mary','Rangel','maryrangel06@gmail.com','$2y$10$k6dw57Cze6ENrTznOMMMiO8vkwLYPQm6943LQ3mCx5gC505LLBFyq','Administrador','2025-10-08 10:04:11');

COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
