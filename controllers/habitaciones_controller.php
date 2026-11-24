<?php
// ================================================================
// CRUD Habitaciones
// Listar, crear, actualizar, (des)habilitar con estado 3, eliminar en cascada
// Estados:
//   1 = Disponible (se muestra en recepción)
//   0 = No disponible / Ocupada (NO se puede deshabilitar para no perder reserva)
//   3 = Deshabilitada (NO se muestra en recepción; se puede eliminar)
// ================================================================
header('Content-Type: application/json; charset=utf-8');

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

function json_error($message, $code = 400) {
  http_response_code($code);
  echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
  exit;
}

// Conexión PDO ($pdo)
$pdo = null;
$paths = [__DIR__ . '/../config/db.php', __DIR__ . '/../db.php'];
foreach ($paths as $p) {
  if (file_exists($p)) { require_once $p; break; }
}
if (!isset($pdo) || !$pdo) json_error('No se encontró conexión PDO. Verifica config/db.php o db.php', 500);

// Bitácora
require_once __DIR__ . '/../config/bitacora.php';

$action = $_REQUEST['action'] ?? null;
if (!$action) {
  bitacora_log($pdo, 'Habitaciones', 'accion_requerida', ['motivo' => 'sin_action'], 'ERROR');
  json_error('Acción requerida.');
}

try {
  if ($action === 'list') {
    $sql = "SELECT h.*, th.nombre_tipo_habitacion, th.capacidad_tipo_habitacion
            FROM habitaciones h
            JOIN tipo_habitaciones th ON th.id_tipo_habitacion = h.id_tipo_habitacion
            ORDER BY h.id_habitacion ASC";
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    // BITÁCORA OK
    bitacora_log($pdo, 'Habitaciones', 'listar', ['total' => count($rows)], 'OK');

    echo json_encode(['ok' => true, 'data' => $rows], JSON_UNESCAPED_UNICODE);

  } elseif ($action === 'create') {
    $nombre       = trim($_POST['nombre_habitacion'] ?? '');
    $descripcion  = trim($_POST['descripcion_habitacion'] ?? '');
    $tipo_id      = (int)($_POST['id_tipo_habitacion'] ?? 0);
    $estado       = isset($_POST['estado_habitacion']) ? (int)!!$_POST['estado_habitacion'] : 1; // 1 disponible por defecto

    if ($nombre === '' || strlen($nombre) > 2) {
      bitacora_log($pdo, 'Habitaciones', 'crear', ['motivo' => 'validacion', 'campo' => 'nombre_habitacion', 'valor' => $nombre], 'ERROR');
      json_error('El número debe tener 1–2 caracteres.');
    }
    if ($tipo_id <= 0) {
      bitacora_log($pdo, 'Habitaciones', 'crear', ['motivo' => 'validacion', 'campo' => 'id_tipo_habitacion', 'valor' => $tipo_id], 'ERROR');
      json_error('Seleccione un tipo de habitación válido.');
    }

    $stmt = $pdo->prepare("INSERT INTO habitaciones (nombre_habitacion, descripcion_habitacion, id_tipo_habitacion, estado_habitacion) VALUES (?, ?, ?, ?)");
    try {
      $stmt->execute([$nombre, $descripcion, $tipo_id, $estado]);
    } catch (PDOException $e) {
      if ($e->getCode() === '23000') {
        bitacora_log($pdo, 'Habitaciones', 'crear', [
          'motivo' => 'duplicado',
          'nombre' => $nombre,
          'tipo'   => $tipo_id
        ], 'ERROR');
        json_error('Ya existe una habitación con ese número.');
      }
      throw $e;
    }

    $nuevoId = (int)$pdo->lastInsertId();

    // BITÁCORA OK
    bitacora_log($pdo, 'Habitaciones', 'crear', [
      'id_habitacion' => $nuevoId,
      'nombre'        => $nombre,
      'id_tipo'       => $tipo_id,
      'estado'        => $estado
    ], 'OK');

    echo json_encode(['ok' => true, 'id' => $nuevoId]);

  } elseif ($action === 'update') {
    $id           = (int)($_POST['id_habitacion'] ?? 0);
    $nombre       = trim($_POST['nombre_habitacion'] ?? '');
    $descripcion  = trim($_POST['descripcion_habitacion'] ?? '');
    $tipo_id      = (int)($_POST['id_tipo_habitacion'] ?? 0);
    $estado       = isset($_POST['estado_habitacion']) ? (int)!!$_POST['estado_habitacion'] : null; // 1/0 opcional desde el modal

    if ($id <= 0) {
      bitacora_log($pdo, 'Habitaciones', 'editar', ['motivo' => 'validacion', 'campo' => 'id_habitacion', 'valor' => $id], 'ERROR');
      json_error('ID inválido.');
    }
    if ($nombre === '' || strlen($nombre) > 2) {
      bitacora_log($pdo, 'Habitaciones', 'editar', ['motivo' => 'validacion', 'campo' => 'nombre_habitacion', 'valor' => $nombre], 'ERROR');
      json_error('El número debe tener 1–2 caracteres.');
    }
    if ($tipo_id <= 0) {
      bitacora_log($pdo, 'Habitaciones', 'editar', ['motivo' => 'validacion', 'campo' => 'id_tipo_habitacion', 'valor' => $tipo_id], 'ERROR');
      json_error('Seleccione un tipo de habitación válido.');
    }

    // Validar unicidad de número
    $dup = $pdo->prepare('SELECT COUNT(*) FROM habitaciones WHERE nombre_habitacion = ? AND id_habitacion <> ?');
    $dup->execute([$nombre, $id]);
    if ((int)$dup->fetchColumn() > 0) {
      bitacora_log($pdo, 'Habitaciones', 'editar', [
        'motivo' => 'duplicado',
        'id'     => $id,
        'nombre' => $nombre
      ], 'ERROR');
      json_error('Ya existe una habitación con ese número.');
    }

    $sql = 'UPDATE habitaciones SET nombre_habitacion = ?, descripcion_habitacion = ?, id_tipo_habitacion = ?';
    $params = [$nombre, $descripcion, $tipo_id];
    if ($estado !== null) { $sql .= ', estado_habitacion = ?'; $params[] = $estado; }
    $sql .= ' WHERE id_habitacion = ?';
    $params[] = $id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // BITÁCORA OK
    bitacora_log($pdo, 'Habitaciones', 'editar', [
      'id_habitacion' => $id,
      'nombre'        => $nombre,
      'id_tipo'       => $tipo_id,
      'estado'        => $estado,
      'filas'         => $stmt->rowCount()
    ], 'OK');

    echo json_encode(['ok' => true]);

  } elseif ($action === 'toggle') {
    $id     = (int)($_POST['id_habitacion'] ?? 0);
    $estado = (int)($_POST['estado_habitacion'] ?? -1); // destino: 1 o 3 (nunca forzamos 0 aquí)
    if ($id <= 0 || !in_array($estado, [0, 1, 3], true)) {
      bitacora_log($pdo, 'Habitaciones', 'cambiar_estado', [
        'motivo' => 'validacion',
        'id'     => $id,
        'estado' => $estado
      ], 'ERROR');
      json_error('Parámetros inválidos.');
    }

    // Bloquear deshabilitar (-> 3) cuando la habitación está No disponible (0)
    $cur = $pdo->prepare('SELECT estado_habitacion FROM habitaciones WHERE id_habitacion = ?');
    $cur->execute([$id]);
    $current = $cur->fetchColumn();
    if ($current === false) {
      bitacora_log($pdo, 'Habitaciones', 'cambiar_estado', [
        'motivo' => 'no_encontrada',
        'id'     => $id
      ], 'ERROR');
      json_error('Habitación no encontrada.', 404);
    }
    if ((int)$current === 0 && (int)$estado === 3) {
      bitacora_log($pdo, 'Habitaciones', 'cambiar_estado', [
        'motivo'  => 'conflicto_estado',
        'id'      => $id,
        'actual'  => (int)$current,
        'destino' => (int)$estado
      ], 'ERROR');
      json_error('No puedes deshabilitar una habitación en estado "No disponible".', 409);
    }

    $stmt = $pdo->prepare('UPDATE habitaciones SET estado_habitacion = ? WHERE id_habitacion = ?');
    $stmt->execute([$estado, $id]);

    // BITÁCORA OK
    bitacora_log($pdo, 'Habitaciones', 'cambiar_estado', [
      'id_habitacion' => $id,
      'estado_anterior'=> (int)$current,
      'estado_nuevo'  => $estado,
      'filas'         => $stmt->rowCount()
    ], 'OK');

    echo json_encode(['ok' => true]);

  } elseif ($action === 'delete') {
    $id = (int)($_POST['id_habitacion'] ?? 0);
    if ($id <= 0) {
      bitacora_log($pdo, 'Habitaciones', 'eliminar', ['motivo' => 'validacion', 'id' => $id], 'ERROR');
      json_error('ID inválido.');
    }

    // Solo permitir eliminar si está deshabilitada (=3)
    $st = $pdo->prepare('SELECT estado_habitacion FROM habitaciones WHERE id_habitacion = ?');
    $st->execute([$id]);
    $estadoActual = $st->fetchColumn();
    if ($estadoActual === false) {
      bitacora_log($pdo, 'Habitaciones', 'eliminar', ['motivo' => 'no_encontrada', 'id' => $id], 'ERROR');
      json_error('Habitación no encontrada.', 404);
    }
    if ((int)$estadoActual !== 3) {
      bitacora_log($pdo, 'Habitaciones', 'eliminar', [
        'motivo' => 'no_deshabilitada',
        'id'     => $id,
        'estado' => (int)$estadoActual
      ], 'ERROR');
      json_error('Debes deshabilitar la habitación antes de eliminarla.');
    }

    $pdo->beginTransaction();
    try {
      // Eliminar en cascada reservas asociadas (si tu FK no tiene ON DELETE CASCADE)
      $delRes = $pdo->prepare('DELETE FROM reservas WHERE id_habitacion = ?');
      $delRes->execute([$id]);

      $delHab = $pdo->prepare('DELETE FROM habitaciones WHERE id_habitacion = ?');
      $delHab->execute([$id]);

      $pdo->commit();

      // BITÁCORA OK
      bitacora_log($pdo, 'Habitaciones', 'eliminar', [
        'id_habitacion'   => $id,
        'res_borradas'    => $delRes->rowCount(),
        'habit_borradas'  => $delHab->rowCount()
      ], 'OK');

      echo json_encode(['ok' => true]);
    } catch (Throwable $e) {
      $pdo->rollBack();
      // Será capturado por el catch global
      throw $e;
    }

  } elseif ($action === 'tipos') {
    $rows = $pdo->query('SELECT id_tipo_habitacion, nombre_tipo_habitacion, capacidad_tipo_habitacion FROM tipo_habitaciones ORDER BY id_tipo_habitacion')->fetchAll(PDO::FETCH_ASSOC);

    // BITÁCORA OK
    bitacora_log($pdo, 'Habitaciones', 'tipos', ['total' => count($rows)], 'OK');

    echo json_encode(['ok' => true, 'data' => $rows], JSON_UNESCAPED_UNICODE);

  } else {
    bitacora_log($pdo, 'Habitaciones', 'accion_no_soportada', ['action' => $action], 'ERROR');
    json_error('Acción no soportada.', 405);
  }

} catch (Throwable $e) {
  // BITÁCORA ERROR GLOBAL
  try {
    bitacora_log($pdo, 'Habitaciones', 'excepcion', ['ex' => $e->getMessage(), 'action' => $action], 'ERROR');
  } catch (Throwable $_) { /* noop */ }

  json_error('Error del servidor: ' . $e->getMessage(), 500);
}
