-- Agrega campos para reflejar en reportes el monto total convertido a bolívares.
-- Ejecutar una sola vez en la base de datos posadalasmandarinas_db.
-- Compatible con MariaDB 10.4+ (XAMPP/phpMyAdmin).

ALTER TABLE `reservas`
  ADD COLUMN IF NOT EXISTS `monto_total_bs` DECIMAL(12,2) NULL DEFAULT NULL AFTER `monto_total`,
  ADD COLUMN IF NOT EXISTS `tasa_conversion` DECIMAL(10,4) NULL DEFAULT NULL AFTER `monto_total_bs`,
  ADD COLUMN IF NOT EXISTS `modo_tasa_conversion` ENUM('api','manual') NULL DEFAULT NULL AFTER `tasa_conversion`;
