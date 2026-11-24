<?php
// controllers/obtener_precio.php
session_start();
require_once '../config/db.php';
require_once '../config/bitacora.php'; // Bitácora

try {
    if (isset($_POST['id_habitacion']) && isset($_POST['tipo_tarifa'])) {
        $id_habitacion = $_POST['id_habitacion'];
        $tipo_tarifa   = $_POST['tipo_tarifa'];

        $query = "SELECT ta.precio_tarifa 
                  FROM tarifas ta
                  INNER JOIN habitaciones h ON ta.id_tipo_habitacion = h.id_tipo_habitacion
                  WHERE h.id_habitacion = :id_habitacion AND ta.tipo_tarifa = :tipo_tarifa
                  LIMIT 1";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':id_habitacion' => $id_habitacion,
            ':tipo_tarifa'   => $tipo_tarifa
        ]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            // Bitácora OK
            bitacora_log($pdo, 'Tarifas', 'consultar_precio', [
                'id_habitacion' => $id_habitacion,
                'tipo_tarifa'   => $tipo_tarifa,
                'precio'        => (float)$resultado['precio_tarifa']
            ], 'OK');

            echo $resultado['precio_tarifa'];
        } else {
            // Bitácora ERROR (no disponible)
            bitacora_log($pdo, 'Tarifas', 'consultar_precio', [
                'id_habitacion' => $id_habitacion,
                'tipo_tarifa'   => $tipo_tarifa,
                'motivo'        => 'no_disponible'
            ], 'ERROR');

            echo "No disponible";
        }
    } else {
        // Parámetros faltantes (no alteramos la respuesta; solo dejamos registro)
        bitacora_log($pdo, 'Tarifas', 'consultar_precio', [
            'motivo' => 'parametros_faltantes',
            'post'   => array_keys($_POST)
        ], 'ERROR');
        // Sin echo aquí para no cambiar el comportamiento original
    }
} catch (Throwable $e) {
    // Registrar error pero mantener la salida original ante fallos
    try {
        bitacora_log($pdo, 'Tarifas', 'consultar_precio', [
            'ex'   => $e->getMessage()
        ], 'ERROR');
    } catch (Throwable $_) {}
    echo "No disponible";
}
