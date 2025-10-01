<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    date_default_timezone_set('America/Caracas');

    $documento_cliente   = $_POST['cedula_cliente'];
    $id_habitacion       = $_POST['id_habitacion'];
    $tipo_tarifa         = $_POST['tipo_tarifa'];
    $cantidad_personas   = $_POST['cantidad_personas'];
    $metodo_pago         = $_POST['metodo_pago'];
    $origen              = $_POST['origen_reserva'];
    $observaciones       = $_POST['observaciones_reserva'] ?? '';
    $dias_estadia_form   = isset($_POST['dias_estadia']) ? max(1, (int)$_POST['dias_estadia']) : null;

    // === Fechas ===
    if (!empty($_POST['fecha_llegada'])) {
        $fecha_llegada = date('Y-m-d H:i:s', strtotime($_POST['fecha_llegada']));

        if ($tipo_tarifa === '3 Horas') {
            $fecha_salida = date('Y-m-d H:i:s', strtotime($fecha_llegada . ' +3 hours'));
            $dias_estadia = 1;
        } elseif ($tipo_tarifa === '24 Horas' && $dias_estadia_form !== null) {
            $fecha_salida = date('Y-m-d H:i:s', strtotime($fecha_llegada . " +$dias_estadia_form days"));
            $dias_estadia = $dias_estadia_form;
        } elseif (!empty($_POST['fecha_salida'])) {
            $fecha_salida = date('Y-m-d H:i:s', strtotime($_POST['fecha_salida']));
            $dias_estadia = ceil((strtotime($fecha_salida) - strtotime($fecha_llegada)) / (60 * 60 * 24));
            $dias_estadia = max(1, (int)$dias_estadia);
        } else {
            $fecha_salida = date('Y-m-d H:i:s', strtotime($fecha_llegada . ' +1 day'));
            $dias_estadia = 1;
        }
    } else {
        $fecha_llegada = date('Y-m-d H:i:s');
        if ($tipo_tarifa === '3 Horas') {
            $fecha_salida = date('Y-m-d H:i:s', strtotime('+3 hours'));
            $dias_estadia = 1;
        } elseif ($tipo_tarifa === '24 Horas' && $dias_estadia_form !== null) {
            $fecha_salida = date('Y-m-d H:i:s', strtotime("+$dias_estadia_form days"));
            $dias_estadia = $dias_estadia_form;
        } else {
            $fecha_salida = date('Y-m-d H:i:s', strtotime('+1 day'));
            $dias_estadia = 1;
        }
    }

    // === Obtener tipo de habitación ===
    $stmt = $pdo->prepare("SELECT id_tipo_habitacion FROM habitaciones WHERE id_habitacion = ?");
    $stmt->execute([$id_habitacion]);
    $id_tipo_habitacion = $stmt->fetchColumn();

    // === Obtener tarifa ===
    $stmt = $pdo->prepare("SELECT id_tarifa, precio_tarifa FROM tarifas WHERE id_tipo_habitacion = ? AND tipo_tarifa = ?");
    $stmt->execute([$id_tipo_habitacion, $tipo_tarifa]);
    $tarifa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tarifa) {
        header("Location: ../views/recepcion.php?reserva=error");
        exit;
    }

    $id_tarifa = $tarifa['id_tarifa'];
    $precio    = $tarifa['precio_tarifa'];
    $estado    = 'Confirmada';

    // === Calcular monto total ===
    $monto_total = $dias_estadia * $precio;

    // === Insertar reserva ===
    $stmt = $pdo->prepare("
        INSERT INTO reservas (
            id_habitacion, documento_cliente, id_tarifa, fecha_llegada, fecha_salida,
            cantidad_personas, monto_total, id_metodo_pago, estado_reserva, observaciones_reserva, origen_reserva
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $exito = $stmt->execute([
        $id_habitacion, $documento_cliente, $id_tarifa, $fecha_llegada, $fecha_salida,
        $cantidad_personas, $monto_total, $metodo_pago, $estado, $observaciones, $origen
    ]);

    // === Marcar habitación como ocupada ===
    $stmt = $pdo->prepare("UPDATE habitaciones SET estado_habitacion = 0 WHERE id_habitacion = ?");
    $stmt->execute([$id_habitacion]);

    header('Location: ../views/recepcion.php?reserva=success');
    exit;
} else {
    echo "Acceso no permitido.";
}
?>
