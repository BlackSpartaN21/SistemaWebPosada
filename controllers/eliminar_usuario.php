<?php
// controllers/eliminar_usuario.php
declare(strict_types=1);

session_start();
require_once '../config/auth.php';
require_admin();

require_once '../config/db.php';
require_once '../config/bitacora.php'; // [BITÁCORA]

function go(string $qs = ''): void {
  $url = '../views/gestionar_usuarios.php';
  if ($qs !== '') $url .= (str_contains($qs, '=') ? ('?' . $qs) : $qs);
  header('Location: ' . $url);
  exit;
}

// 1) Método permitido
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  bitacora_log($pdo, 'Usuarios', 'eliminar', [
    'motivo' => 'method_not_allowed',
    'method' => $_SERVER['REQUEST_METHOD'] ?? null
  ], 'ERROR');
  go(); // Redirige sin mensaje (igual que tu código)
}

// 2) Validar id_usuario
$id_usuario = (int)($_POST['id_usuario'] ?? 0);
if ($id_usuario <= 0) {
  bitacora_log($pdo, 'Usuarios', 'eliminar', [
    'motivo' => 'id_invalido',
    'id'     => $_POST['id_usuario'] ?? null
  ], 'ERROR');
  go('error=ID%20inv%C3%A1lido');
}

// 3) No permitir eliminarse a sí mismo
$actor_id = (int)($_SESSION['id_usuario'] ?? 0);
if ($actor_id === $id_usuario) {
  bitacora_log($pdo, 'Usuarios', 'eliminar', [
    'motivo'    => 'self_delete_denied',
    'actor_id'  => $actor_id,
    'target_id' => $id_usuario
  ], 'ERROR');
  go('error=No%20puedes%20eliminar%20tu%20propia%20cuenta');
}

try {
  // 4) Verificar que el usuario exista y obtener su rol
  $q = $pdo->prepare('SELECT rol_usuario FROM usuarios WHERE id_usuario = :id');
  $q->execute([':id' => $id_usuario]);
  $row = $q->fetch(PDO::FETCH_ASSOC);

  if (!$row) {
    bitacora_log($pdo, 'Usuarios', 'eliminar', [
      'motivo'    => 'usuario_no_encontrado',
      'target_id' => $id_usuario
    ], 'ERROR');
    go('error=Usuario%20no%20encontrado');
  }

  // 5) Si es Administrador, no permitir eliminar al último
  if (($row['rol_usuario'] ?? '') === 'Administrador') {
    $c = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol_usuario = 'Administrador'");
    $countAdmin = (int)$c->fetchColumn();
    if ($countAdmin <= 1) {
      bitacora_log($pdo, 'Usuarios', 'eliminar', [
        'motivo'     => 'last_admin_denied',
        'target_id'  => $id_usuario,
        'target_rol' => 'Administrador',
        'total_admin'=> $countAdmin
      ], 'ERROR');
      go('error=No%20puedes%20eliminar%20al%20%C3%BAltimo%20Administrador');
    }
  }

  // 6) Eliminar
  $del = $pdo->prepare('DELETE FROM usuarios WHERE id_usuario = :id');
  $del->execute([':id' => $id_usuario]);

  // Resultado
  if ($del->rowCount() > 0) {
    bitacora_log($pdo, 'Usuarios', 'eliminar', [
      'target_id'    => $id_usuario,
      'target_rol'   => $row['rol_usuario'] ?? null,
      'actor_id'     => $actor_id,
      'filas_borradas'=> $del->rowCount()
    ], 'OK');
    go('ok=1');
  } else {
    // Raro: existía, pero no borró filas
    bitacora_log($pdo, 'Usuarios', 'eliminar', [
      'motivo'    => 'no_rows_deleted',
      'target_id' => $id_usuario
    ], 'ERROR');
    go('error=No%20se%20pudo%20eliminar');
  }

} catch (PDOException $e) {
  bitacora_log($pdo, 'Usuarios', 'eliminar', [
    'target_id' => $id_usuario,
    'pdo_code'  => $e->getCode(),
    'ex'        => $e->getMessage()
  ], 'ERROR');
  go('error=Error%20de%20base%20de%20datos');
}
