<?php
// ================================================================
// CRUD Tipos de Habitaciones
// Tabla: tipo_habitaciones
// Campos: id_tipo_habitacion (AI), nombre_tipo_habitacion (varchar 20), capacidad_tipo_habitacion (tinyint unsigned)
// Reglas:
//  - nombre requerido (1..20), capacidad >=1
//  - nombre único (a nivel aplicación)
//  - eliminar: sólo si NO está referenciado por habitaciones NI por tarifas
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
  try { bitacora_log($pdo, 'TiposHabitacion', 'accion_requerida', ['motivo' => 'sin_action'], 'ERROR'); } catch (Throwable $_) {}
  json_error('Acción requerida.');
}

try {
  if ($action === 'list') {
    $rows = $pdo->query("SELECT id_tipo_habitacion, nombre_tipo_habitacion, capacidad_tipo_habitacion
                         FROM tipo_habitaciones
                         ORDER BY id_tipo_habitacion ASC")->fetchAll(PDO::FETCH_ASSOC);

    try { bitacora_log($pdo, 'TiposHabitacion', 'listar', ['total' => count($rows)], 'OK'); } catch (Throwable $_) {}

    echo json_encode(['ok' => true, 'data' => $rows], JSON_UNESCAPED_UNICODE);

  } elseif ($action === 'create') {
    $nombre    = trim($_POST['nombre_tipo_habitacion'] ?? '');
    $capacidad = (int)($_POST['capacidad_tipo_habitacion'] ?? 0);

    if ($nombre === '' || mb_strlen($nombre) > 20) {
      try { bitacora_log($pdo, 'TiposHabitacion', 'crear', ['motivo' => 'validacion', 'campo' => 'nombre_tipo_habitacion', 'valor' => $nombre], 'ERROR'); } catch (Throwable $_) {}
      json_error('El nombre debe tener 1–20 caracteres.');
    }
    if ($capacidad <= 0) {
      try { bitacora_log($pdo, 'TiposHabitacion', 'crear', ['motivo' => 'validacion', 'campo' => 'capacidad_tipo_habitacion', 'valor' => $capacidad], 'ERROR'); } catch (Throwable $_) {}
      json_error('La capacidad debe ser un número mayor o igual a 1.');
    }

    // unicidad de nombre
    $dup = $pdo->prepare('SELECT COUNT(*) FROM tipo_habitaciones WHERE nombre_tipo_habitacion = ?');
    $dup->execute([$nombre]);
    if ((int)$dup->fetchColumn() > 0) {
      try { bitacora_log($pdo, 'TiposHabitacion', 'crear', ['motivo' => 'duplicado', 'nombre' => $nombre], 'ERROR'); } catch (Throwable $_) {}
      json_error('Ya existe un tipo con ese nombre.');
    }

    $stmt = $pdo->prepare("INSERT INTO tipo_habitaciones (nombre_tipo_habitacion, capacidad_tipo_habitacion)
                           VALUES (?, ?)");
    $stmt->execute([$nombre, $capacidad]);

    $newId = (int)$pdo->lastInsertId();
    try { bitacora_log($pdo, 'TiposHabitacion', 'crear', ['id_tipo_habitacion' => $newId, 'nombre' => $nombre, 'capacidad' => $capacidad], 'OK'); } catch (Throwable $_) {}

    echo json_encode(['ok' => true, 'id' => $newId]);

  } elseif ($action === 'update') {
    $id        = (int)($_POST['id_tipo_habitacion'] ?? 0);
    $nombre    = trim($_POST['nombre_tipo_habitacion'] ?? '');
    $capacidad = (int)($_POST['capacidad_tipo_habitacion'] ?? 0);

    if ($id <= 0) {
      try { bitacora_log($pdo, 'TiposHabitacion', 'editar', ['motivo' => 'validacion', 'campo' => 'id_tipo_habitacion', 'valor' => $id], 'ERROR'); } catch (Throwable $_) {}
      json_error('ID inválido.');
    }
    if ($nombre === '' || mb_strlen($nombre) > 20) {
      try { bitacora_log($pdo, 'TiposHabitacion', 'editar', ['motivo' => 'validacion', 'campo' => 'nombre_tipo_habitacion', 'valor' => $nombre], 'ERROR'); } catch (Throwable $_) {}
      json_error('El nombre debe tener 1–20 caracteres.');
    }
    if ($capacidad <= 0) {
      try { bitacora_log($pdo, 'TiposHabitacion', 'editar', ['motivo' => 'validacion', 'campo' => 'capacidad_tipo_habitacion', 'valor' => $capacidad], 'ERROR'); } catch (Throwable $_) {}
      json_error('La capacidad debe ser un número mayor o igual a 1.');
    }

    // unicidad de nombre (excluyendo el mismo id)
    $dup = $pdo->prepare('SELECT COUNT(*) FROM tipo_habitaciones WHERE nombre_tipo_habitacion = ? AND id_tipo_habitacion <> ?');
    $dup->execute([$nombre, $id]);
    if ((int)$dup->fetchColumn() > 0) {
      try { bitacora_log($pdo, 'TiposHabitacion', 'editar', ['motivo' => 'duplicado', 'id_tipo_habitacion' => $id, 'nombre' => $nombre], 'ERROR'); } catch (Throwable $_) {}
      json_error('Ya existe un tipo con ese nombre.');
    }

    $stmt = $pdo->prepare("UPDATE tipo_habitaciones
                           SET nombre_tipo_habitacion = ?, capacidad_tipo_habitacion = ?
                           WHERE id_tipo_habitacion = ?");
    $stmt->execute([$nombre, $capacidad, $id]);

    try { bitacora_log($pdo, 'TiposHabitacion', 'editar', ['id_tipo_habitacion' => $id, 'nombre' => $nombre, 'capacidad' => $capacidad, 'filas' => $stmt->rowCount()], 'OK'); } catch (Throwable $_) {}

    echo json_encode(['ok' => true]);

  } elseif ($action === 'delete') {
    $id = (int)($_POST['id_tipo_habitacion'] ?? 0);
    if ($id <= 0) {
      try { bitacora_log($pdo, 'TiposHabitacion', 'eliminar', ['motivo' => 'validacion', 'id_tipo_habitacion' => $id], 'ERROR'); } catch (Throwable $_) {}
      json_error('ID inválido.');
    }

    // Verificar referencias en habitaciones
    $cntH = $pdo->prepare('SELECT COUNT(*) FROM habitaciones WHERE id_tipo_habitacion = ?');
    $cntH->execute([$id]);
    $usosH = (int)$cntH->fetchColumn();

    // Verificar referencias en tarifas
    $cntT = $pdo->prepare('SELECT COUNT(*) FROM tarifas WHERE id_tipo_habitacion = ?');
    $cntT->execute([$id]);
    $usosT = (int)$cntT->fetchColumn();

    if ($usosH > 0 || $usosT > 0) {
      // Log de conflicto de referencias
      try { bitacora_log($pdo, 'TiposHabitacion', 'eliminar', ['motivo' => 'con_referencias', 'id_tipo_habitacion' => $id, 'habitaciones' => $usosH, 'tarifas' => $usosT], 'ERROR'); } catch (Throwable $_) {}
      // 👇 usa 409 para expresar “conflicto” y que el front lo capture en .fail(...)
      json_error("No puedes eliminar este tipo porque tiene $usosH habitaciones y $usosT tarifas asociadas.", 409);
    }

    $stmt = $pdo->prepare('DELETE FROM tipo_habitaciones WHERE id_tipo_habitacion = ?');
    $stmt->execute([$id]);

    try { bitacora_log($pdo, 'TiposHabitacion', 'eliminar', ['id_tipo_habitacion' => $id, 'filas' => $stmt->rowCount()], 'OK'); } catch (Throwable $_) {}

    echo json_encode(['ok' => true]);

  } else {
    try { bitacora_log($pdo, 'TiposHabitacion', 'accion_no_soportada', ['action' => $action], 'ERROR'); } catch (Throwable $_) {}
    json_error('Acción no soportada.', 405);
  }

} catch (Throwable $e) {
  try { bitacora_log($pdo, 'TiposHabitacion', 'excepcion', ['ex' => $e->getMessage(), 'action' => $action], 'ERROR'); } catch (Throwable $_) {}
  json_error('Error del servidor: ' . $e->getMessage(), 500);
}
