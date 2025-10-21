<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    header("Location: ../views/recepcion.php?reserva=error&code=hab_no_encontrada");
    exit;
}
$id_tipo_habitacion = (int)$hab['id_tipo_habitacion'];
$estado_habitacion  = (int)$hab['estado_habitacion'];

// No permitir reservar si habitación está deshabilitada (3) o no disponible (0)
if ($estado_habitacion === 3) {
    header("Location: ../views/recepcion.php?reserva=error&code=hab_deshabilitada");
    exit;
}
if ($estado_habitacion === 0) {
    header("Location: ../views/recepcion.php?reserva=error&code=hab_no_disponible");
    exit;
}

// 2) Obtener tarifa para ese tipo
$stmt = $pdo->prepare("SELECT id_tarifa, precio_tarifa FROM tarifas WHERE id_tipo_habitacion = ? AND tipo_tarifa = ?");
$stmt->execute([$id_tipo_habitacion, $tipo_tarifa]);
$tarifa = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$tarifa) {
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
    header("Location: ../views/recepcion.php?reserva=error&code=capacidad_indefinida");
    exit;
}
if ($cantidad_personas < 1 || $cantidad_personas > $capMax) {
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
    header("Location: ../views/recepcion.php?reserva=error&code=traslape");
    exit;
}

// ==== Calcular monto total ====
$monto_total = $dias_estadia * $precio;
$estado      = 'Confirmada';

// ==== Transacción: insertar reserva + marcar habitación ocupada ====
try {
    $pdo->beginTransaction();

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

    // Marcar habitación como ocupada (estado = 0)
    $stmt = $pdo->prepare("UPDATE habitaciones SET estado_habitacion = 0 WHERE id_habitacion = ?");
    $ok2 = $stmt->execute([$id_habitacion]);

    if (!$ok1 || !$ok2) {
        $pdo->rollBack();
        header("Location: ../views/recepcion.php?reserva=error&code=bd");
        exit;
    }

    $pdo->commit();
    header('Location: ../views/recepcion.php?reserva=success');
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    // Loguea $e->getMessage() si tienes logger
    header("Location: ../views/recepcion.php?reserva=error&code=excepcion");
    exit;
}
