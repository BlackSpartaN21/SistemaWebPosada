<?php
// controllers/bitacora_list.php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id_usuario']) || (($_SESSION['rol_usuario'] ?? '') !== 'Administrador')) {
  echo json_encode(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0, 'draw' => (int)($_POST['draw'] ?? 0)]);
  exit;
}

require_once '../config/db.php';

try {
  // --- Parámetros DataTables
  $draw   = max(0, (int)($_POST['draw']   ?? 0));
  $start  = max(0, (int)($_POST['start']  ?? 0));
  $length = (int)($_POST['length'] ?? 25);
  if ($length === -1) { $length = 1000; } // "All" -> cap sensible
  if ($length <= 0)   { $length = 25; }
  if ($length > 2000) { $length = 2000; } // límite anti abuso

  $search = trim((string)($_POST['search']['value'] ?? ''));

  // --- Filtros propios
  $desde     = trim((string)($_POST['desde']     ?? ''));
  $hasta     = trim((string)($_POST['hasta']     ?? ''));
  $idUsuario = trim((string)($_POST['id_usuario'] ?? ''));
  $modulo    = trim((string)($_POST['modulo']    ?? ''));
  $accion    = trim((string)($_POST['accion']    ?? ''));
  $resultado = trim((string)($_POST['resultado'] ?? ''));

  // --- Base
  $sqlBase = "
    FROM bitacora b
    LEFT JOIN usuarios u ON u.id_usuario = b.id_usuario
    WHERE 1=1
  ";

  $params = [];

  // Filtros
  if ($desde !== '') {
    $sqlBase .= " AND DATE(b.fecha) >= :desde";
    $params[':desde'] = $desde;
  }
  if ($hasta !== '') {
    $sqlBase .= " AND DATE(b.fecha) <= :hasta";
    $params[':hasta'] = $hasta;
  }
  if ($idUsuario !== '') {
    $sqlBase .= " AND b.id_usuario = :id_usuario";
    $params[':id_usuario'] = (int)$idUsuario;
  }
  if ($modulo !== '') {
    $sqlBase .= " AND b.modulo LIKE :modulo";
    $params[':modulo'] = '%'.$modulo.'%';
  }
  if ($accion !== '') {
    $sqlBase .= " AND b.accion LIKE :accion";
    $params[':accion'] = '%'.$accion.'%';
  }
  if ($resultado !== '') {
    $sqlBase .= " AND b.resultado = :resultado";
    $params[':resultado'] = $resultado;
  }
  if ($search !== '') {
    $sqlBase .= " AND (
        b.modulo LIKE :q OR b.accion LIKE :q OR b.detalle LIKE :q OR
        CONCAT(u.nombre_usuario, ' ', u.apellido_usuario) LIKE :q
    )";
    $params[':q'] = '%'.$search.'%';
  }

  // Totales
  $total = (int)$pdo->query("SELECT COUNT(*) FROM bitacora")->fetchColumn();

  $stmtCount = $pdo->prepare("SELECT COUNT(*) ".$sqlBase);
  foreach ($params as $k => $v) {
    $stmtCount->bindValue($k, $v);
  }
  $stmtCount->execute();
  $filtrados = (int)$stmtCount->fetchColumn();

  // Orden (usa el índice que manda DataTables y mapea a columnas permitidas)
  $columns = [
    0 => 'b.fecha',
    1 => "CONCAT(u.nombre_usuario, ' ', u.apellido_usuario)",
    2 => 'u.rol_usuario',
    3 => 'b.modulo',
    4 => 'b.accion',
    5 => 'b.detalle',
    6 => 'b.resultado',
    7 => 'b.ip',
    8 => 'b.user_agent'
  ];
  $order = " ORDER BY b.fecha DESC ";
  if (isset($_POST['order'][0]['column'], $_POST['order'][0]['dir'])) {
    $idx = (int)$_POST['order'][0]['column'];
    $dir = strtolower((string)$_POST['order'][0]['dir']) === 'asc' ? 'ASC' : 'DESC';
    if (array_key_exists($idx, $columns)) {
      $order = " ORDER BY {$columns[$idx]} {$dir} ";
    }
  }

  // Paginación
  $limit = " LIMIT :start, :len ";

  // Datos
  $sqlData = "
    SELECT
      DATE_FORMAT(b.fecha, '%Y-%m-%d %H:%i:%s') AS fecha,
      COALESCE(CONCAT(u.nombre_usuario, ' ', u.apellido_usuario), '—') AS usuario,
      COALESCE(u.rol_usuario, '—') AS rol,
      b.modulo, b.accion,
      CASE
        WHEN b.detalle IS NULL OR b.detalle = '' THEN '—'
        WHEN JSON_VALID(b.detalle) THEN b.detalle
        ELSE b.detalle
      END AS detalle,
      b.resultado, b.ip, b.user_agent AS ua
    ".$sqlBase.$order.$limit;

  $stmt = $pdo->prepare($sqlData);
  foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
  }
  $stmt->bindValue(':start', $start, PDO::PARAM_INT);
  $stmt->bindValue(':len',   $length, PDO::PARAM_INT);
  $stmt->execute();

  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Respuesta
  echo json_encode([
    'draw'            => $draw,
    'recordsTotal'    => $total,
    'recordsFiltered' => $filtrados,
    'data'            => $rows,
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
  echo json_encode([
    'draw' => (int)($_POST['draw'] ?? 0),
    'recordsTotal' => 0,
    'recordsFiltered' => 0,
    'data' => [],
    'error' => 'Error en bitacora_list: '.$e->getMessage()
  ], JSON_UNESCAPED_UNICODE);
}
