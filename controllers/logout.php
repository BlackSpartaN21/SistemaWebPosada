<?php
// controllers/logout.php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/bitacora.php'; // [BITÁCORA]

$idUsuario = (int)($_SESSION['id_usuario'] ?? 0);
$ip        = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$hadRemember = isset($_COOKIE['PLM_REMEMBER']);

// [BITÁCORA] Registrar intento de logout (antes de destruir sesión)
try {
  bitacora_log($pdo, 'Autenticación', 'logout', [
    'id_usuario' => $idUsuario,
    'ip'         => $ip,
    'remember'   => $hadRemember ? 'yes' : 'no'
  ], 'OK');
} catch (Throwable $e) {
  // No romper el flujo por bitácora
}

// Eliminar token remember-me si existe cookie
if (isset($_COOKIE['PLM_REMEMBER'])) {
  $cookie = (string)$_COOKIE['PLM_REMEMBER'];
  [$sel, $val] = array_pad(explode(':', $cookie, 2), 2, '');

  if ($sel !== '') {
    try {
      $stmt = $pdo->prepare("DELETE FROM user_remember_tokens WHERE selector = :s");
      $stmt->execute([':s' => $sel]);
    } catch (\Throwable $e) {
      // ignorar
    }
  }

  setcookie('PLM_REMEMBER', '', [
    'expires'  => time() - 3600,
    'path'     => '/',
    'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Lax',
  ]);
}

// Destruir sesión con seguridad
$_SESSION = [];
if (ini_get('session.use_cookies')) {
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

// Mensaje bonito al volver a login (gatilla SweetAlert)
session_start();
$_SESSION['flash'] = ['type' => 'success', 'msg' => 'Sesión cerrada correctamente'];
header('Location: ../views/login.php');
exit;
