<?php
// config/reservas_conversion.php
declare(strict_types=1);

/**
 * Verifica si la tabla reservas ya tiene las columnas necesarias para
 * guardar la conversión USD -> Bs usada al crear una reserva.
 */
function reservas_tiene_columnas_conversion(PDO $pdo): bool
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    try {
        $stmt = $pdo->prepare("\n            SELECT COLUMN_NAME\n            FROM INFORMATION_SCHEMA.COLUMNS\n            WHERE TABLE_SCHEMA = DATABASE()\n              AND TABLE_NAME = 'reservas'\n              AND COLUMN_NAME IN ('monto_total_bs', 'tasa_conversion', 'modo_tasa_conversion')\n        ");
        $stmt->execute();
        $columnas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $cache = count(array_unique($columnas)) === 3;
    } catch (Throwable $e) {
        $cache = false;
    }

    return $cache;
}
