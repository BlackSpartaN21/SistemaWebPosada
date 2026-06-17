<?php
// controllers/obtener_tasa_bcv.php
declare(strict_types=1);

session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

require_once '../config/db.php';
require_once '../config/bitacora.php';
require_once '../config/tasa_bcv.php';

try {
    $resultado = obtener_tasa_dolar_oficial_bcv();

    if (($resultado['ok'] ?? false) === true) {
        bitacora_log($pdo, 'Tarifas', 'consultar_tasa_bcv', [
            'tasa' => $resultado['tasa'],
            'fuente' => $resultado['fuente'] ?? 'oficial',
            'fechaActualizacion' => $resultado['fechaActualizacion'] ?? null,
        ], 'OK');

        echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    bitacora_log($pdo, 'Tarifas', 'consultar_tasa_bcv', [
        'motivo' => $resultado['error'] ?? 'error_api',
    ], 'ERROR');

    http_response_code(503);
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    try {
        bitacora_log($pdo, 'Tarifas', 'consultar_tasa_bcv', [
            'ex' => $e->getMessage(),
        ], 'ERROR');
    } catch (Throwable $_) {}

    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => 'No se pudo consultar la tasa oficial BCV.',
        'fuente' => 'api',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
