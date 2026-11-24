-- Backup generado por Sistema Web Posada Las Mandarinas
-- Base de datos: `posadalasmandarinas_db`
-- Fecha: 2025-11-05 03:46:44

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
) ENGINE=InnoDB AUTO_INCREMENT=324 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(80,'2025-11-04 02:09:02',2,'Backups','listar','{\"count\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(81,'2025-11-04 02:09:06',2,'Backups','crear','{\"db\":\"posadalasmandarinas_db\",\"file\":\"posadalasmandarinas_db_20251104_070906.sql\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(82,'2025-11-04 02:09:06',2,'Backups','listar','{\"count\":4}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(83,'2025-11-04 02:09:13',2,'Backups','eliminar','{\"file\":\"posadalasmandarinas_db_20251103_141703.sql\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(84,'2025-11-04 02:09:13',2,'Backups','listar','{\"count\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(85,'2025-11-04 02:09:16',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(86,'2025-11-04 02:09:16',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(87,'2025-11-04 02:09:27',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(88,'2025-11-04 02:09:27',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(89,'2025-11-04 02:36:46',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(90,'2025-11-04 02:36:46',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(91,'2025-11-04 02:36:47',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(92,'2025-11-04 02:36:48',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(93,'2025-11-04 02:36:48',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(94,'2025-11-04 02:36:51',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(95,'2025-11-04 02:36:51',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(96,'2025-11-04 02:36:52',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(97,'2025-11-04 02:36:52',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(98,'2025-11-04 02:36:52',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(99,'2025-11-04 02:36:56',2,'Reservas','crear','{\"id_reserva\":0,\"id_habitacion\":1,\"documento_cliente\":\"13577225\",\"tipo_tarifa\":\"3 Horas\",\"dias\":1,\"monto_total\":5,\"metodo_pago\":1,\"llegada\":\"2025-11-06 02:36:00\",\"salida\":\"2025-11-06 05:36:00\",\"estado\":\"Confirmada\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(100,'2025-11-04 02:36:56',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(101,'2025-11-04 02:36:56',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(102,'2025-11-04 02:37:02',2,'Habitaciones','vaciar','{\"id_habitacion\":1,\"hab_afectadas\":1,\"reservas_finalizadas\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(103,'2025-11-04 02:37:04',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(104,'2025-11-04 02:37:04',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(105,'2025-11-04 02:38:03',2,'Autenticación','logout','{\"id_usuario\":2,\"ip\":\"::1\",\"remember\":\"no\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(106,'2025-11-04 09:14:44',2,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":2,\"email\":\"cristofermedinar6@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(107,'2025-11-04 09:14:44',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(108,'2025-11-04 09:14:44',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(109,'2025-11-04 09:17:26',2,'Autenticación','logout','{\"id_usuario\":2,\"ip\":\"::1\",\"remember\":\"no\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(110,'2025-11-04 09:28:35',2,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":2,\"email\":\"cristofermedinar6@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(111,'2025-11-04 09:28:35',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(112,'2025-11-04 09:28:35',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(113,'2025-11-04 09:28:41',2,'Reportes','exportar','{\"seccion\":\"reservas\",\"filtros\":{\"desde\":\"\",\"hasta\":\"\",\"estado\":\"\"},\"total\":43,\"monto\":700}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(114,'2025-11-04 09:28:52',2,'Habitaciones','tipos','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(115,'2025-11-04 09:28:52',2,'Habitaciones','listar','{\"total\":11}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(116,'2025-11-04 09:28:53',2,'TiposHabitacion','listar','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(117,'2025-11-04 09:28:53',2,'Tarifas','listar','{\"total\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(118,'2025-11-04 09:29:03',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(119,'2025-11-04 09:29:03',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(120,'2025-11-04 09:29:08',2,'Backups','listar','{\"count\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(121,'2025-11-04 09:29:11',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(122,'2025-11-04 09:29:11',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(123,'2025-11-04 09:29:14',2,'Autenticación','logout','{\"id_usuario\":2,\"ip\":\"::1\",\"remember\":\"no\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(124,'2025-11-04 09:37:19',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(125,'2025-11-04 09:37:19',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(126,'2025-11-04 09:37:25',2,'Backups','listar','{\"count\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(127,'2025-11-04 09:37:27',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(128,'2025-11-04 09:37:27',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(129,'2025-11-04 09:37:29',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(130,'2025-11-04 09:37:29',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(131,'2025-11-04 09:37:32',2,'Autenticación','logout','{\"id_usuario\":2,\"ip\":\"::1\",\"remember\":\"no\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(132,'2025-11-04 10:24:57',2,'Autenticación','login','{\"ip\":\"192.168.1.14\",\"id_usuario\":2,\"email\":\"cristofermedinar6@gmail.com\"}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(133,'2025-11-04 10:24:59',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(134,'2025-11-04 10:24:59',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(135,'2025-11-04 10:25:41',2,'Reportes','exportar','{\"seccion\":\"reservas\",\"filtros\":{\"desde\":\"\",\"hasta\":\"\",\"estado\":\"\"},\"total\":43,\"monto\":700}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(136,'2025-11-04 10:25:55',2,'Habitaciones','tipos','{\"total\":2}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(137,'2025-11-04 10:25:55',2,'Habitaciones','listar','{\"total\":11}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(138,'2025-11-04 10:25:56',2,'TiposHabitacion','listar','{\"total\":2}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(139,'2025-11-04 10:25:57',2,'Tarifas','listar','{\"total\":3}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(140,'2025-11-04 10:26:36',2,'Backups','listar','{\"count\":3}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(141,'2025-11-04 10:26:42',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(142,'2025-11-04 10:26:42',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(143,'2025-11-04 10:26:45',2,'Autenticación','logout','{\"id_usuario\":2,\"ip\":\"192.168.1.14\",\"remember\":\"no\"}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(144,'2025-11-04 15:59:08',2,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":2,\"email\":\"cristofermedinar6@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(145,'2025-11-04 15:59:09',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(146,'2025-11-04 15:59:09',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(147,'2025-11-04 15:59:13',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(148,'2025-11-04 15:59:13',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(149,'2025-11-04 15:59:27',2,'Habitaciones','tipos','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(150,'2025-11-04 15:59:27',2,'Habitaciones','listar','{\"total\":11}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(151,'2025-11-04 15:59:27',2,'TiposHabitacion','listar','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(152,'2025-11-04 15:59:27',2,'Tarifas','listar','{\"total\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(153,'2025-11-04 16:00:56',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(154,'2025-11-04 16:00:56',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(155,'2025-11-04 16:11:56',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(156,'2025-11-04 16:11:56',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(157,'2025-11-04 16:12:08',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(158,'2025-11-04 16:12:08',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(159,'2025-11-04 16:12:23',2,'Backups','listar','{\"count\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(160,'2025-11-04 16:12:33',2,'Backups','crear','{\"db\":\"posadalasmandarinas_db\",\"file\":\"posadalasmandarinas_db_20251104_211233.sql\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(161,'2025-11-04 16:12:33',2,'Backups','listar','{\"count\":4}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(162,'2025-11-04 16:12:42',2,'Backups','eliminar','{\"file\":\"posadalasmandarinas_db_20251104_211233.sql\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(163,'2025-11-04 16:12:42',2,'Backups','listar','{\"count\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(164,'2025-11-04 20:14:07',2,'Backups','restaurar','{\"ok\":true,\"stmts\":32}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(165,'2025-11-04 16:14:10',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(166,'2025-11-04 16:14:10',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(167,'2025-11-04 16:14:36',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(168,'2025-11-04 16:14:36',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(169,'2025-11-04 16:14:56',2,'Usuarios','reset_password','{\"actor_id\":2,\"target_id\":3,\"filas_afectadas\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(170,'2025-11-04 16:15:00',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(171,'2025-11-04 16:15:00',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(172,'2025-11-04 16:15:34',NULL,'Autenticación','rehash','{\"id_usuario\":3}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(173,'2025-11-04 16:15:34',3,'Autenticación','login','{\"ip\":\"192.168.1.14\",\"id_usuario\":3,\"email\":\"fabian@gmail.com\"}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(174,'2025-11-04 16:15:35',3,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(175,'2025-11-04 16:15:35',3,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(176,'2025-11-04 16:16:00',3,'Backups','listar','{\"count\":4}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(177,'2025-11-04 16:16:05',3,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(178,'2025-11-04 16:16:05',3,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(179,'2025-11-04 16:17:44',3,'Autenticación','logout','{\"id_usuario\":3,\"ip\":\"192.168.1.14\",\"remember\":\"no\"}','192.168.1.14','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36','OK'),
(180,'2025-11-04 16:18:10',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(181,'2025-11-04 16:18:10',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','OK'),
(182,'2025-11-04 19:16:21',2,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":2,\"email\":\"cristofermedinar6@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(183,'2025-11-04 19:16:22',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(184,'2025-11-04 19:16:22',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(185,'2025-11-04 19:16:31',2,'Habitaciones','tipos','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(186,'2025-11-04 19:16:31',2,'Habitaciones','listar','{\"total\":11}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(187,'2025-11-04 19:16:31',2,'TiposHabitacion','listar','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(188,'2025-11-04 19:16:31',2,'Tarifas','listar','{\"total\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(189,'2025-11-04 19:19:26',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(190,'2025-11-04 19:19:26',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(191,'2025-11-04 19:46:50',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(192,'2025-11-04 19:46:51',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(193,'2025-11-04 19:48:42',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(194,'2025-11-04 19:48:42',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(195,'2025-11-04 19:48:52',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(196,'2025-11-04 19:48:52',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(197,'2025-11-04 19:50:32',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(198,'2025-11-04 19:50:32',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(199,'2025-11-04 19:51:13',2,'Autenticación','logout','{\"id_usuario\":2,\"ip\":\"::1\",\"remember\":\"no\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(200,'2025-11-04 19:53:20',2,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":2,\"email\":\"cristofermedinar6@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK');
INSERT INTO `bitacora` (`id_bitacora`,`fecha`,`id_usuario`,`modulo`,`accion`,`detalle`,`ip`,`user_agent`,`resultado`) VALUES 
(201,'2025-11-04 19:53:20',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(202,'2025-11-04 19:53:20',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(203,'2025-11-04 19:53:30',2,'Autenticación','logout','{\"id_usuario\":2,\"ip\":\"::1\",\"remember\":\"no\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(204,'2025-11-04 19:54:02',2,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":2,\"email\":\"cristofermedinar6@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(205,'2025-11-04 19:54:02',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(206,'2025-11-04 19:54:02',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(207,'2025-11-04 19:55:06',2,'Reportes','exportar','{\"seccion\":\"reservas\",\"filtros\":{\"desde\":\"2025-11-04\",\"hasta\":\"2025-11-04\",\"estado\":\"\"},\"total\":2,\"monto\":20}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(208,'2025-11-04 19:55:30',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(209,'2025-11-04 19:55:30',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(210,'2025-11-04 19:55:32',2,'Autenticación','logout','{\"id_usuario\":2,\"ip\":\"::1\",\"remember\":\"no\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(211,'2025-11-04 20:01:34',2,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":2,\"email\":\"cristofermedinar6@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(212,'2025-11-04 20:01:34',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(213,'2025-11-04 20:01:34',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(214,'2025-11-04 20:06:29',2,'Autenticación','logout','{\"id_usuario\":2,\"ip\":\"::1\",\"remember\":\"no\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(215,'2025-11-04 21:14:57',2,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":2,\"email\":\"cristofermedinar6@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(216,'2025-11-04 21:14:57',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(217,'2025-11-04 21:14:57',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(218,'2025-11-04 21:15:21',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"24 Horas\",\"precio\":10}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(219,'2025-11-04 21:15:21',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"24 Horas\",\"precio\":10}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(220,'2025-11-04 21:15:23',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"1\",\"tipo_tarifa\":\"24 Horas\",\"precio\":10}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(221,'2025-11-04 21:15:28',2,'Reservas','crear','{\"id_reserva\":0,\"id_habitacion\":1,\"documento_cliente\":\"13577225\",\"tipo_tarifa\":\"24 Horas\",\"dias\":1,\"monto_total\":10,\"metodo_pago\":2,\"llegada\":\"2025-11-04 21:15:28\",\"salida\":\"2025-11-05 21:15:28\",\"estado\":\"Confirmada\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(222,'2025-11-04 21:15:28',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(223,'2025-11-04 21:15:28',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(224,'2025-11-04 21:15:48',2,'Habitaciones','vaciar','{\"id_habitacion\":2,\"hab_afectadas\":1,\"reservas_finalizadas\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(225,'2025-11-04 21:15:50',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(226,'2025-11-04 21:15:50',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(227,'2025-11-04 21:16:11',2,'Reportes','exportar','{\"seccion\":\"reservas\",\"filtros\":{\"desde\":\"\",\"hasta\":\"\",\"estado\":\"\"},\"total\":44,\"monto\":710}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(228,'2025-11-04 21:16:46',2,'Habitaciones','tipos','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(229,'2025-11-04 21:16:46',2,'Habitaciones','listar','{\"total\":11}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(230,'2025-11-04 21:16:46',2,'TiposHabitacion','listar','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(231,'2025-11-04 21:16:46',2,'Tarifas','listar','{\"total\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(232,'2025-11-04 21:17:45',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(233,'2025-11-04 21:17:45',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(234,'2025-11-04 21:18:14',2,'Backups','listar','{\"count\":4}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(235,'2025-11-04 21:18:31',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(236,'2025-11-04 21:18:31',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(237,'2025-11-04 21:18:34',2,'Autenticación','logout','{\"id_usuario\":2,\"ip\":\"::1\",\"remember\":\"no\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(238,'2025-11-04 21:18:42',3,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":3,\"email\":\"fabian@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(239,'2025-11-04 21:18:42',3,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(240,'2025-11-04 21:18:42',3,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(241,'2025-11-04 21:19:48',3,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(242,'2025-11-04 21:19:48',3,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(243,'2025-11-04 21:19:55',3,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(244,'2025-11-04 21:19:55',3,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(245,'2025-11-04 21:20:02',3,'Backups','listar','{\"count\":4}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(246,'2025-11-04 21:20:08',3,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(247,'2025-11-04 21:20:08',3,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(248,'2025-11-04 21:20:28',3,'Reportes','exportar','{\"seccion\":\"reservas\",\"filtros\":{\"desde\":\"2025-11-04\",\"hasta\":\"\",\"estado\":\"\"},\"total\":4,\"monto\":35}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(249,'2025-11-04 21:22:18',3,'Reportes','exportar','{\"seccion\":\"reservas\",\"filtros\":{\"desde\":\"2025-05-04\",\"hasta\":\"2025-05-31\",\"estado\":\"\"},\"total\":15,\"monto\":150}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(250,'2025-11-04 21:22:57',3,'Autenticación','logout','{\"id_usuario\":3,\"ip\":\"::1\",\"remember\":\"no\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(251,'2025-11-04 21:23:13',2,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":2,\"email\":\"cristofermedinar6@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(252,'2025-11-04 21:23:13',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(253,'2025-11-04 21:23:13',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(254,'2025-11-04 21:23:37',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(255,'2025-11-04 21:23:38',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(256,'2025-11-04 21:25:18',2,'Usuarios','eliminar','{\"motivo\":\"self_delete_denied\",\"actor_id\":2,\"target_id\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','ERROR'),
(257,'2025-11-04 21:25:56',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(258,'2025-11-04 21:25:56',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(259,'2025-11-04 21:27:39',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(260,'2025-11-04 21:27:39',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(261,'2025-11-04 21:28:02',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"2\",\"tipo_tarifa\":\"24 Horas\",\"precio\":10}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(262,'2025-11-04 21:28:02',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"2\",\"tipo_tarifa\":\"24 Horas\",\"precio\":10}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(263,'2025-11-04 21:28:02',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"2\",\"tipo_tarifa\":\"24 Horas\",\"precio\":10}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(264,'2025-11-04 21:33:31',2,'Backups','listar','{\"count\":4}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(265,'2025-11-04 21:33:33',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(266,'2025-11-04 21:33:33',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(267,'2025-11-04 22:15:07',2,'Autenticación','logout','{\"id_usuario\":2,\"ip\":\"::1\",\"remember\":\"no\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(268,'2025-11-04 22:15:20',2,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":2,\"email\":\"cristofermedinar6@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(269,'2025-11-04 22:15:21',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(270,'2025-11-04 22:15:21',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(271,'2025-11-04 22:15:35',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"2\",\"tipo_tarifa\":\"24 Horas\",\"precio\":10}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(272,'2025-11-04 22:15:35',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"2\",\"tipo_tarifa\":\"24 Horas\",\"precio\":10}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(273,'2025-11-04 22:15:35',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"2\",\"tipo_tarifa\":\"24 Horas\",\"precio\":10}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(274,'2025-11-04 22:15:40',2,'Reservas','crear','{\"id_reserva\":0,\"id_habitacion\":2,\"documento_cliente\":\"15789654\",\"tipo_tarifa\":\"24 Horas\",\"dias\":1,\"monto_total\":10,\"metodo_pago\":1,\"llegada\":\"2025-11-04 22:15:40\",\"salida\":\"2025-11-05 22:15:40\",\"estado\":\"Confirmada\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(275,'2025-11-04 22:15:40',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(276,'2025-11-04 22:15:40',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(277,'2025-11-04 22:15:45',2,'Habitaciones','vaciar','{\"id_habitacion\":2,\"hab_afectadas\":1,\"reservas_finalizadas\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(278,'2025-11-04 22:15:46',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(279,'2025-11-04 22:15:46',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(280,'2025-11-04 22:15:50',2,'Habitaciones','vaciar','{\"id_habitacion\":1,\"hab_afectadas\":1,\"reservas_finalizadas\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(281,'2025-11-04 22:15:50',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(282,'2025-11-04 22:15:50',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(283,'2025-11-04 22:15:57',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"4\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(284,'2025-11-04 22:15:57',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"4\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(285,'2025-11-04 22:15:57',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"4\",\"tipo_tarifa\":\"3 Horas\",\"precio\":5}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(286,'2025-11-04 22:16:03',2,'Reservas','crear','{\"id_reserva\":0,\"id_habitacion\":4,\"documento_cliente\":\"13577225\",\"tipo_tarifa\":\"3 Horas\",\"dias\":1,\"monto_total\":5,\"metodo_pago\":2,\"llegada\":\"2025-11-04 22:16:03\",\"salida\":\"2025-11-05 01:16:03\",\"estado\":\"Confirmada\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(287,'2025-11-04 22:16:03',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(288,'2025-11-04 22:16:03',2,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(289,'2025-11-04 22:16:17',2,'Autenticación','logout','{\"id_usuario\":2,\"ip\":\"::1\",\"remember\":\"no\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(290,'2025-11-04 22:23:22',NULL,'Autenticación','rehash','{\"id_usuario\":4}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(291,'2025-11-04 22:23:22',4,'Autenticación','login','{\"ip\":\"::1\",\"id_usuario\":4,\"email\":\"maryrangel06@gmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(292,'2025-11-04 22:23:22',4,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(293,'2025-11-04 22:23:22',4,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(294,'2025-11-04 22:24:04',4,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(295,'2025-11-04 22:24:04',4,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(296,'2025-11-04 22:26:15',4,'Clientes','crear','{\"documento\":\"8017933\",\"correo\":\"sangulo11@hotmail.com\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(297,'2025-11-04 22:26:15',4,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(298,'2025-11-04 22:26:15',4,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(299,'2025-11-04 22:26:41',4,'Clientes','editar','{\"doc_original\":\"8017933\",\"documento_nuevo\":\"8017933\",\"correo\":\"sangulo11@hotmail.com\",\"filas_afectadas\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(300,'2025-11-04 22:28:04',4,'Reportes','exportar','{\"seccion\":\"reservas\",\"filtros\":{\"desde\":\"2025-11-04\",\"hasta\":\"2025-11-04\",\"estado\":\"\"},\"total\":5,\"monto\":45}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(301,'2025-11-04 22:29:00',4,'Habitaciones','listar','{\"total\":11}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(302,'2025-11-04 22:29:00',4,'Habitaciones','tipos','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(303,'2025-11-04 22:29:01',4,'TiposHabitacion','listar','{\"total\":2}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(304,'2025-11-04 22:29:01',4,'Tarifas','listar','{\"total\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(305,'2025-11-04 22:31:47',4,'TiposHabitacion','crear','{\"id_tipo_habitacion\":7,\"nombre\":\"Doble\",\"capacidad\":4}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(306,'2025-11-04 22:31:47',4,'TiposHabitacion','listar','{\"total\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(307,'2025-11-04 22:32:09',4,'Tarifas','tipos','{\"total\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(308,'2025-11-04 22:32:50',4,'Tarifas','crear','{\"id_tarifa\":8,\"id_tipo\":7,\"tipo_tarifa\":\"24 Horas\",\"precio\":20}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(309,'2025-11-04 22:32:50',4,'Tarifas','listar','{\"total\":4}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(310,'2025-11-04 22:33:04',4,'Habitaciones','tipos','{\"total\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(311,'2025-11-04 22:33:04',4,'Habitaciones','listar','{\"total\":11}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(312,'2025-11-04 22:33:04',4,'TiposHabitacion','listar','{\"total\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(313,'2025-11-04 22:33:04',4,'Tarifas','listar','{\"total\":4}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(314,'2025-11-04 22:33:24',4,'Habitaciones','crear','{\"id_habitacion\":20,\"nombre\":\"12\",\"id_tipo\":7,\"estado\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(315,'2025-11-04 22:33:24',4,'Habitaciones','listar','{\"total\":12}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(316,'2025-11-04 22:33:42',4,'Tarifas','consultar_precio','{\"id_habitacion\":\"7\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(317,'2025-11-04 22:33:42',4,'Tarifas','consultar_precio','{\"id_habitacion\":\"8\",\"tipo_tarifa\":\"24 Horas\",\"precio\":15}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(318,'2025-11-04 22:33:42',4,'Tarifas','consultar_precio','{\"id_habitacion\":\"20\",\"tipo_tarifa\":\"24 Horas\",\"precio\":20}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(319,'2025-11-04 22:34:15',4,'Habitaciones','tipos','{\"total\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(320,'2025-11-04 22:34:15',4,'Habitaciones','listar','{\"total\":12}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(321,'2025-11-04 22:34:15',4,'TiposHabitacion','listar','{\"total\":3}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(322,'2025-11-04 22:34:15',4,'Tarifas','listar','{\"total\":4}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK'),
(323,'2025-11-04 22:42:14',4,'Backups','listar','{\"count\":4}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','OK');

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
(29852369,'V','Carlos','Alarcon',4124569873,'carlo@gmail.com','','2025-11-04 00:57:01'),
(30192253,'V','Cristofer','Medina R',2222222222,'crris@gmail.com','nada','2025-05-11 00:34:44'),
(31023654,'V','Marta','Habla',77777777777,'marta@gmail.com','nada','2025-05-11 00:41:52'),
('31092233-4','J','Valmorca','CA',2746549874,'valmor@gma.com','Empresa','2025-03-29 22:21:18'),
(31236459,'V','Cristian Miguel','Medina Rangel',4147896541,'cris@gmail.com','Nada','2025-03-27 00:25:12'),
(4563217899,'J','Seleccion Nacional de Karate','Del estado Merida',2742210666,'km@gmail.com','Merida - Venezuela','2025-05-11 00:40:23'),
(5555555555,'J','Asociacion','De Natacion del estado Merida',24244444444,'am@gamil.com','ninguna','2025-05-14 21:44:13'),
(8017933,'V','Estefany','Angulo',04261785245,'sangulo11@hotmail.com','automovil','2025-11-04 22:26:15'),
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `habitaciones`
--

INSERT INTO `habitaciones` (`id_habitacion`,`nombre_habitacion`,`descripcion_habitacion`,`id_tipo_habitacion`,`estado_habitacion`) VALUES 
(1,1,'',1,1),
(2,2,'',1,1),
(3,3,'',1,1),
(4,4,'',1,0),
(5,5,'',1,1),
(6,6,'',1,1),
(7,7,'',2,1),
(8,8,'',2,1),
(9,9,'',1,1),
(10,10,'',1,1),
(11,11,'',1,1),
(20,12,'',7,1);

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
(1,'::1','fab@gmail.com',4,'2025-11-03 21:14:47',NULL),
(2,'::1','efefe@gm.com',1,'2025-11-03 09:01:32',NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(46,2,15789654,1,'2025-11-03 16:06:23','2025-11-03 19:06:23',2,5.00,1,'Finalizada','','','2025-11-03 16:06:23'),
(47,7,12777710,3,'2025-11-04 00:43:47','2025-11-05 00:43:47',3,15.00,1,'Finalizada','','','2025-11-04 00:43:47'),
(48,1,29852369,1,'2025-11-04 01:23:34','2025-11-04 04:23:34',2,5.00,2,'Finalizada','','','2025-11-04 01:23:34'),
(49,1,13577225,1,'2025-11-06 02:36:00','2025-11-06 05:36:00',1,5.00,1,'Finalizada','','','2025-11-04 02:36:55'),
(50,1,13577225,2,'2025-11-04 21:15:28','2025-11-05 21:15:28',2,10.00,2,'Finalizada','','','2025-11-04 21:15:28'),
(51,2,15789654,2,'2025-11-04 22:15:40','2025-11-05 22:15:40',2,10.00,1,'Finalizada','','','2025-11-04 22:15:40'),
(52,4,13577225,1,'2025-11-04 22:16:03','2025-11-05 01:16:03',2,5.00,2,'Confirmada','','','2025-11-04 22:16:03');

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarifas`
--

INSERT INTO `tarifas` (`id_tarifa`,`id_tipo_habitacion`,`tipo_tarifa`,`precio_tarifa`) VALUES 
(1,1,'3 Horas',5.00),
(2,1,'24 Horas',10.00),
(3,2,'24 Horas',15.00),
(8,7,'24 Horas',20.00);

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_habitaciones`
--

INSERT INTO `tipo_habitaciones` (`id_tipo_habitacion`,`nombre_tipo_habitacion`,`capacidad_tipo_habitacion`) VALUES 
(1,'Matrimonial',2),
(2,'Triple',3),
(7,'Doble',4);

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
(3,'Fabian','Sánchez','fabian@gmail.com','$2y$12$vsUCc9zuskcLuQ2Fi9tAY.y9V3qIp0kONxHXgPtAAizFKEq/1.Zxm','Recepcionista','2025-07-16 01:44:19'),
(4,'Mary','Rangel','maryrangel06@gmail.com','$2y$12$jYpSx0n50Sp4vnlCZy.rGu4EN4UNrCI88sqfkhyH2HI2pZSPwmY9.','Administrador','2025-10-08 10:04:11');

COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
