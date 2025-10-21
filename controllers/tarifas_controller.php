<?php
// ================================================================
// CRUD Tarifas
// Tabla: tarifas (id_tarifa, id_tipo_habitacion, tipo_tarifa, precio_tarifa)
// Reglas:
//  - (id_tipo_habitacion, tipo_tarifa) debe ser único (a nivel app)
//  - precio_tarifa >= 0 (2 decimales)
//  - delete bloqueado si hay reservas con esa tarifa
//  - endpoint 'tipos' para poblar el select de tipos de habitación
// ================================================================
header('Content-Type: application/json; charset=utf-8');

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

$action = $_REQUEST['action'] ?? null;
if (!$action) json_error('Acción requerida.');

try {
  if ($action === 'list') {
    $sql = "SELECT tf.*, th.nombre_tipo_habitacion, th.capacidad_tipo_habitacion
            FROM tarifas tf
            JOIN tipo_habitaciones th ON th.id_tipo_habitacion = tf.id_tipo_habitacion
            ORDER BY tf.id_tarifa ASC";
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['ok' => true, 'data' => $rows], JSON_UNESCAPED_UNICODE);

  } elseif ($action === 'create') {
    $tipo_id    = (int)($_POST['id_tipo_habitacion'] ?? 0);
    $tipo_tarifa = trim($_POST['tipo_tarifa'] ?? '');
    $precio      = $_POST['precio_tarifa'] ?? '';

    if ($tipo_id <= 0) json_error('Seleccione un tipo de habitación válido.');
    if ($tipo_tarifa === '' || mb_strlen($tipo_tarifa) > 30) json_error('El nombre de la tarifa es requerido (máx. 30).');

    // Precio: acepta coma o punto, formatea a 2 decimales
    $precio = str_replace(',', '.', $precio);
    if (!is_numeric($precio)) json_error('El precio debe ser numérico.');
    $precio = round((float)$precio, 2);
    if ($precio < 0) json_error('El precio no puede ser negativo.');

    // Duplicados (tipo + nombre tarifa)
    $dup = $pdo->prepare('SELECT COUNT(*) FROM tarifas WHERE id_tipo_habitacion = ? AND tipo_tarifa = ?');
    $dup->execute([$tipo_id, $tipo_tarifa]);
    if ((int)$dup->fetchColumn() > 0) json_error('Ya existe una tarifa con ese nombre para ese tipo.');

    $stmt = $pdo->prepare("INSERT INTO tarifas (id_tipo_habitacion, tipo_tarifa, precio_tarifa) VALUES (?, ?, ?)");
    $stmt->execute([$tipo_id, $tipo_tarifa, $precio]);

    echo json_encode(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);

  } elseif ($action === 'update') {
    $id         = (int)($_POST['id_tarifa'] ?? 0);
    $tipo_id    = (int)($_POST['id_tipo_habitacion'] ?? 0);
    $tipo_tarifa = trim($_POST['tipo_tarifa'] ?? '');
    $precio      = $_POST['precio_tarifa'] ?? '';

    if ($id <= 0) json_error('ID inválido.');
    if ($tipo_id <= 0) json_error('Seleccione un tipo de habitación válido.');
    if ($tipo_tarifa === '' || mb_strlen($tipo_tarifa) > 30) json_error('El nombre de la tarifa es requerido (máx. 30).');

    $precio = str_replace(',', '.', $precio);
    if (!is_numeric($precio)) json_error('El precio debe ser numérico.');
    $precio = round((float)$precio, 2);
    if ($precio < 0) json_error('El precio no puede ser negativo.');

    $dup = $pdo->prepare('SELECT COUNT(*) FROM tarifas WHERE id_tipo_habitacion = ? AND tipo_tarifa = ? AND id_tarifa <> ?');
    $dup->execute([$tipo_id, $tipo_tarifa, $id]);
    if ((int)$dup->fetchColumn() > 0) json_error('Ya existe una tarifa con ese nombre para ese tipo.');

    $stmt = $pdo->prepare("UPDATE tarifas SET id_tipo_habitacion = ?, tipo_tarifa = ?, precio_tarifa = ? WHERE id_tarifa = ?");
    $stmt->execute([$tipo_id, $tipo_tarifa, $precio, $id]);

    echo json_encode(['ok' => true]);

  } elseif ($action === 'delete') {
    $id = (int)($_POST['id_tarifa'] ?? 0);
    if ($id <= 0) json_error('ID inválido.');

    // Bloquear si hay reservas asociadas
    $cnt = $pdo->prepare('SELECT COUNT(*) FROM reservas WHERE id_tarifa = ?');
    $cnt->execute([$id]);
    if ((int)$cnt->fetchColumn() > 0) {
      json_error('No puedes eliminar esta tarifa porque está asociada a una o más reservas.', 409);
    }

    $stmt = $pdo->prepare('DELETE FROM tarifas WHERE id_tarifa = ?');
    $stmt->execute([$id]);

    echo json_encode(['ok' => true]);

  } elseif ($action === 'tipos') {
    // Para poblar el select de tipos
    $rows = $pdo->query('SELECT id_tipo_habitacion, nombre_tipo_habitacion, capacidad_tipo_habitacion FROM tipo_habitaciones ORDER BY id_tipo_habitacion')->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['ok' => true, 'data' => $rows], JSON_UNESCAPED_UNICODE);

  } else {
    json_error('Acción no soportada.', 405);
  }

} catch (Throwable $e) {
  json_error('Error del servidor: ' . $e->getMessage(), 500);
}
