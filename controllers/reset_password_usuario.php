<?php
// controllers/reset_password_usuario.php
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

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  try {
    bitacora_log($pdo, 'Usuarios', 'reset_password', [
      'motivo' => 'method_not_allowed',
      'method' => $_SERVER['REQUEST_METHOD'] ?? null
    ], 'ERROR');
  } catch (Throwable $_) {}
  go();
}

$actor_id   = (int)($_SESSION['id_usuario'] ?? 0);
$id_usuario = (int)($_POST['id_usuario'] ?? 0);
$nueva      = (string)($_POST['nueva_contrasena'] ?? '');

if ($id_usuario <= 0 || strlen($nueva) < 6) {
  try {
    bitacora_log($pdo, 'Usuarios', 'reset_password', [
      'motivo'    => 'validacion',
      'actor_id'  => $actor_id,
      'target_id' => $id_usuario
    ], 'ERROR');
  } catch (Throwable $_) {}
  go('error=Datos%20inv%C3%A1lidos');
}

try {
  $hash = password_hash($nueva, PASSWORD_DEFAULT);
  $upd = $pdo->prepare('UPDATE usuarios SET contrasena_usuario = :pass WHERE id_usuario = :id');
  $upd->execute([':pass' => $hash, ':id' => $id_usuario]);

  try {
    bitacora_log($pdo, 'Usuarios', 'reset_password', [
      'actor_id'        => $actor_id,
      'target_id'       => $id_usuario,
      'filas_afectadas' => $upd->rowCount()
    ], 'OK');
  } catch (Throwable $_) {}

  go('ok=1');

} catch (PDOException $e) {
  try {
    bitacora_log($pdo, 'Usuarios', 'reset_password', [
      'actor_id'  => $actor_id,
      'target_id' => $id_usuario,
      'pdo_code'  => $e->getCode(),
      'ex'        => $e->getMessage()
    ], 'ERROR');
  } catch (Throwable $_) {}
  go('error=Error%20de%20base%20de%20datos');
}
