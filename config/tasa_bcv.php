<?php
// config/tasa_bcv.php
declare(strict_types=1);

/**
 * Obtiene la tasa oficial USD/VEF desde DolarApi Venezuela.
 * Devuelve un arreglo normalizado para usarlo desde controladores AJAX.
 */
function obtener_tasa_dolar_oficial_bcv(int $timeoutSegundos = 6): array
{
    $url = 'https://ve.dolarapi.com/v1/dolares/oficial';

    $respuesta = null;
    $httpCode = 0;
    $error = null;

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => $timeoutSegundos,
            CURLOPT_TIMEOUT => $timeoutSegundos,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);
        $respuesta = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($respuesta === false) {
            $error = curl_error($ch) ?: 'No se pudo conectar con DolarApi.';
        }
        curl_close($ch);
    } else {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => $timeoutSegundos,
                'header' => "Accept: application/json\r\n",
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);
        $respuesta = @file_get_contents($url, false, $context);
        if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m)) {
            $httpCode = (int)$m[1];
        }
        if ($respuesta === false) {
            $error = 'No se pudo conectar con DolarApi.';
        }
    }

    if ($respuesta === false || $respuesta === null || $respuesta === '') {
        return [
            'ok' => false,
            'error' => $error ?: 'Respuesta vacía de DolarApi.',
            'fuente' => 'api',
        ];
    }

    if ($httpCode !== 0 && ($httpCode < 200 || $httpCode >= 300)) {
        return [
            'ok' => false,
            'error' => 'DolarApi respondió con HTTP ' . $httpCode,
            'fuente' => 'api',
        ];
    }

    $json = json_decode($respuesta, true);
    if (!is_array($json)) {
        return [
            'ok' => false,
            'error' => 'La respuesta de DolarApi no es JSON válido.',
            'fuente' => 'api',
        ];
    }

    $tasa = isset($json['promedio']) ? (float)$json['promedio'] : 0.0;
    if ($tasa <= 0) {
        return [
            'ok' => false,
            'error' => 'DolarApi no devolvió una tasa promedio válida.',
            'fuente' => 'api',
            'raw' => $json,
        ];
    }

    return [
        'ok' => true,
        'tasa' => $tasa,
        'moneda' => $json['moneda'] ?? 'USD',
        'fuente' => $json['fuente'] ?? 'oficial',
        'nombre' => $json['nombre'] ?? 'Dólar oficial',
        'compra' => isset($json['compra']) ? (float)$json['compra'] : null,
        'venta' => isset($json['venta']) ? (float)$json['venta'] : null,
        'fechaActualizacion' => $json['fechaActualizacion'] ?? null,
    ];
}
