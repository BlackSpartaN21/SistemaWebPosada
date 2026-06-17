<?php
// controllers/procesar_reserva.php
declare(strict_types=1);

session_start();
require_once '../config/db.php';
require_once '../config/bitacora.php'; // [BITÁCORA]
require_once '../config/reservas_conversion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    try {
        bitacora_log($pdo, 'Reservas', 'crear', [
            'motivo' => 'method_not_allowed',
            'method' => $_SERVER['REQUEST_METHOD'] ?? null
        ], 'ERROR');
    } catch (Throwable $_) {}
    echo "Acceso no permitido.";
    exit;
}

date_default_timezone_set('America/Caracas');

// ==== Entrada (sanitizada/casteada) ====
$documento_cliente = $_POST['cedula_cliente'] ?? '';
$id_habitacion     = (int)($_POST['id_habitacion'] ?? 0);
$tipo_tarifa       = trim($_POST['tipo_tarifa'] ?? '');
$cantidad_personas = max(1, (int)($_POST['cantidad_personas'] ?? 1));
$metodo_pago       = (int)($_POST['metodo_pago'] ?? 0);
$origen            = trim($_POST['origen_reserva'] ?? '');
$observaciones     = trim($_POST['observaciones_reserva'] ?? '');
$dias_estadia_form = isset($_POST['dias_estadia']) ? max(1, (int)$_POST['dias_estadia']) : null;

// Datos informativos de conversión USD -> Bs capturados desde la modal.
// La reserva sigue guardando monto_total en dólares para no romper reportes existentes.
$modo_tasa_conversion = trim($_POST['modo_tasa_conversion'] ?? 'api');
if (!in_array($modo_tasa_conversion, ['api', 'manual'], true)) {
    $modo_tasa_conversion = 'api';
}
$tasa_conversion = isset($_POST['tasa_conversion']) ? (float)$_POST['tasa_conversion'] : 0.0;
if ($tasa_conversion < 0) {
    $tasa_conversion = 0.0;
}

// ==== Fechas (misma lógica que tenías) ====
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

// ==== Verificaciones previas ====

// 1) Verificar habitación y estado actual
$stmt = $pdo->prepare("SELECT id_tipo_habitacion, estado_habitacion FROM habitaciones WHERE id_habitacion = ?");
$stmt->execute([$id_habitacion]);
$hab = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$hab) {
    try {
        bitacora_log($pdo, 'Reservas', 'crear', [
            'motivo'         => 'hab_no_encontrada',
            'id_habitacion'  => $id_habitacion
        ], 'ERROR');
    } catch (Throwable $_) {}
    header("Location: ../views/recepcion.php?reserva=error&code=hab_no_encontrada");
    exit;
}
$id_tipo_habitacion = (int)$hab['id_tipo_habitacion'];
$estado_habitacion  = (int)$hab['estado_habitacion'];

// No permitir reservar si habitación está deshabilitada (3) o no disponible (0)
if ($estado_habitacion === 3) {
    try {
        bitacora_log($pdo, 'Reservas', 'crear', [
            'motivo'        => 'hab_deshabilitada',
            'id_habitacion' => $id_habitacion
        ], 'ERROR');
    } catch (Throwable $_) {}
    header("Location: ../views/recepcion.php?reserva=error&code=hab_deshabilitada");
    exit;
}
if ($estado_habitacion === 0) {
    try {
        bitacora_log($pdo, 'Reservas', 'crear', [
            'motivo'        => 'hab_no_disponible',
            'id_habitacion' => $id_habitacion
        ], 'ERROR');
    } catch (Throwable $_) {}
    header("Location: ../views/recepcion.php?reserva=error&code=hab_no_disponible");
    exit;
}

// 2) Obtener tarifa para ese tipo
$stmt = $pdo->prepare("SELECT id_tarifa, precio_tarifa FROM tarifas WHERE id_tipo_habitacion = ? AND tipo_tarifa = ?");
$stmt->execute([$id_tipo_habitacion, $tipo_tarifa]);
$tarifa = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$tarifa) {
    try {
        bitacora_log($pdo, 'Reservas', 'crear', [
            'motivo'              => 'tarifa_no_encontrada',
            'id_habitacion'       => $id_habitacion,
            'id_tipo_habitacion'  => $id_tipo_habitacion,
            'tipo_tarifa'         => $tipo_tarifa
        ], 'ERROR');
    } catch (Throwable $_) {}
    header("Location: ../views/recepcion.php?reserva=error&code=tarifa_no_encontrada");
    exit;
}
$id_tarifa = (int)$tarifa['id_tarifa'];
$precio    = (float)$tarifa['precio_tarifa'];

// 3) Validar capacidad máxima según tipo
$stmtCap = $pdo->prepare("
  SELECT th.capacidad_tipo_habitacion
  FROM habitaciones h
  JOIN tipo_habitaciones th ON th.id_tipo_habitacion = h.id_tipo_habitacion
  WHERE h.id_habitacion = ?
");
$stmtCap->execute([$id_habitacion]);
$capMax = (int)$stmtCap->fetchColumn();
if ($capMax <= 0) {
    try {
        bitacora_log($pdo, 'Reservas', 'crear', [
            'motivo'        => 'capacidad_indefinida',
            'id_habitacion' => $id_habitacion
        ], 'ERROR');
    } catch (Throwable $_) {}
    header("Location: ../views/recepcion.php?reserva=error&code=capacidad_indefinida");
    exit;
}
if ($cantidad_personas < 1 || $cantidad_personas > $capMax) {
    try {
        bitacora_log($pdo, 'Reservas', 'crear', [
            'motivo'            => 'capacidad_excedida',
            'id_habitacion'     => $id_habitacion,
            'capacidad_maxima'  => $capMax,
            'solicitadas'       => $cantidad_personas
        ], 'ERROR');
    } catch (Throwable $_) {}
    header("Location: ../views/recepcion.php?reserva=error&code=capacidad_excedida&max=$capMax");
    exit;
}

// 4) Chequear traslape de reservas confirmadas en la misma habitación
// (nuevo_llegada < existente_salida) AND (nuevo_salida > existente_llegada)
$stmt = $pdo->prepare("
  SELECT COUNT(*) 
  FROM reservas 
  WHERE id_habitacion = ?
    AND estado_reserva = 'Confirmada'
    AND NOT (fecha_salida <= ? OR fecha_llegada >= ?)
");
$stmt->execute([$id_habitacion, $fecha_llegada, $fecha_salida]);
$hayTraslape = (int)$stmt->fetchColumn() > 0;
if ($hayTraslape) {
    try {
        bitacora_log($pdo, 'Reservas', 'crear', [
            'motivo'         => 'traslape',
            'id_habitacion'  => $id_habitacion,
            'llegada'        => $fecha_llegada,
            'salida'         => $fecha_salida
        ], 'ERROR');
    } catch (Throwable $_) {}
    header("Location: ../views/recepcion.php?reserva=error&code=traslape");
    exit;
}

// ==== Calcular monto total ====
$monto_total = $dias_estadia * $precio;
$monto_total_bs = $tasa_conversion > 0 ? round($monto_total * $tasa_conversion, 2) : null;
$estado      = 'Confirmada';

// ==== Transacción: insertar reserva + marcar habitación ocupada ====
try {
    $pdo->beginTransaction();

    $tieneColumnasConversion = reservas_tiene_columnas_conversion($pdo);

    if ($tieneColumnasConversion) {
        $stmt = $pdo->prepare("
            INSERT INTO reservas (
                id_habitacion, documento_cliente, id_tarifa, fecha_llegada, fecha_salida,
                cantidad_personas, monto_total, monto_total_bs, tasa_conversion, modo_tasa_conversion,
                id_metodo_pago, estado_reserva, observaciones_reserva, origen_reserva
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ok1 = $stmt->execute([
            $id_habitacion, $documento_cliente, $id_tarifa, $fecha_llegada, $fecha_salida,
            $cantidad_personas, $monto_total, $monto_total_bs,
            $tasa_conversion > 0 ? $tasa_conversion : null,
            $tasa_conversion > 0 ? $modo_tasa_conversion : null,
            $metodo_pago, $estado, $observaciones, $origen
        ]);
    } else {
        // Compatibilidad: si aún no se ejecutó la migración, la reserva se guarda
        // sin romper el flujo actual y los reportes mostrarán "—" en Monto Total Bs.
        $stmt = $pdo->prepare("
            INSERT INTO reservas (
                id_habitacion, documento_cliente, id_tarifa, fecha_llegada, fecha_salida,
                cantidad_personas, monto_total, id_metodo_pago, estado_reserva, observaciones_reserva, origen_reserva
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ok1 = $stmt->execute([
            $id_habitacion, $documento_cliente, $id_tarifa, $fecha_llegada, $fecha_salida,
            $cantidad_personas, $monto_total, $metodo_pago, $estado, $observaciones, $origen
        ]);
    }

    // Marcar habitación como ocupada (estado = 0)
    $stmt = $pdo->prepare("UPDATE habitaciones SET estado_habitacion = 0 WHERE id_habitacion = ?");
    $ok2 = $stmt->execute([$id_habitacion]);

    if (!$ok1 || !$ok2) {
        $pdo->rollBack();

        try {
            bitacora_log($pdo, 'Reservas', 'crear', [
                'motivo'            => 'transaccion_fallida',
                'id_habitacion'     => $id_habitacion,
                'documento_cliente' => $documento_cliente
            ], 'ERROR');
        } catch (Throwable $_) {}

        header("Location: ../views/recepcion.php?reserva=error&code=bd");
        exit;
    }

    $reservaId = (int)$pdo->lastInsertId();
    $pdo->commit();

    try {
        bitacora_log($pdo, 'Reservas', 'crear', [
            'id_reserva'        => $reservaId,
            'id_habitacion'     => $id_habitacion,
            'documento_cliente' => $documento_cliente,
            'tipo_tarifa'       => $tipo_tarifa,
            'dias'              => $dias_estadia,
            'monto_total'       => $monto_total,
            'monto_total_bs'    => $monto_total_bs,
            'tasa_conversion'   => $tasa_conversion > 0 ? $tasa_conversion : null,
            'modo_tasa'         => $modo_tasa_conversion,
            'metodo_pago'       => $metodo_pago,
            'llegada'           => $fecha_llegada,
            'salida'            => $fecha_salida,
            'estado'            => $estado
        ], 'OK');
    } catch (Throwable $_) {}

    header('Location: ../views/recepcion.php?reserva=success');
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();

    try {
        bitacora_log($pdo, 'Reservas', 'crear', [
            'motivo'            => 'excepcion',
            'ex'                => $e->getMessage(),
            'id_habitacion'     => $id_habitacion,
            'documento_cliente' => $documento_cliente
        ], 'ERROR');
    } catch (Throwable $_) {}

    header("Location: ../views/recepcion.php?reserva=error&code=excepcion");
    exit;
}
