<?php
// controllers/login_process.php
declare(strict_types=1);
session_start();

// --------- Config sesión ----------
if (function_exists('session_status') && session_status() === PHP_SESSION_ACTIVE) {
  if (PHP_VERSION_ID >= 70300) {
    @session_set_cookie_params([
      'httponly' => true,
      'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
      'samesite' => 'Lax',
    ]);
  }
}

// ---------- Dependencias ----------
require_once __DIR__ . '/../config/db.php';       // Debe exponer $pdo (PDO)
if ($pdo instanceof PDO) {
  // Asegurar excepciones en PDO (por si tu db.php no lo configura)
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
require_once __DIR__ . '/../config/bitacora.php'; // Bitácora

// ---------- Helper bitácora seguro ----------
function log_bita($pdo, string $accion, array $detalle = [], string $resultado = 'OK'): void {
  if ($pdo instanceof PDO) {
    try { bitacora_log($pdo, 'Autenticación', $accion, $detalle, $resultado); } catch (Throwable $e) { /* noop */ }
  }
}

// ---------- Helpers ----------
function redirect_with_status(string $type, string $msg): void {
  $url = '../views/login.php?status=' . rawurlencode($type) . '&msg=' . rawurlencode($msg);
  header('Location: ' . $url);
  exit;
}
function fail(string $msg = 'Solicitud inválida'): void {
  redirect_with_status('error', $msg);
}

// ---------- Método ----------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  log_bita($pdo, 'login', ['motivo' => 'method_not_allowed', 'method' => $_SERVER['REQUEST_METHOD'] ?? null], 'ERROR');
  fail('Método no permitido.');
}

// ---------- CSRF ----------
$csrf_session = $_SESSION['csrf_login'] ?? null;
$csrf_post    = $_POST['csrf_token']     ?? null;
if (!$csrf_session || !$csrf_post || !hash_equals((string)$csrf_session, (string)$csrf_post)) {
  log_bita($pdo, 'login', ['motivo' => 'csrf_invalid', 'ip' => ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0')], 'ERROR');
  fail('Token de seguridad inválido. Recarga la página e inténtalo nuevamente.');
}

// ---------- Inputs ----------
$ip        = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$emailRaw  = (string)($_POST['correo_usuario'] ?? $_POST['correo'] ?? '');
$passRaw   = (string)($_POST['contrasena_usuario'] ?? $_POST['contrasena'] ?? '');

$email = filter_var(trim($emailRaw), FILTER_VALIDATE_EMAIL);
$pass  = trim($passRaw);

if (!$email || $email === '' || $pass === '') {
  log_bita($pdo, 'login', ['motivo' => 'inputs_invalidos', 'ip' => $ip], 'ERROR');
  fail('Correo o contraseña inválidos.');
}

// ---------- Rate limiting básico (tabla + fallback) ----------
try {
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS login_attempts (
      id INT AUTO_INCREMENT PRIMARY KEY,
      ip VARCHAR(45) NOT NULL,
      email VARCHAR(190) NOT NULL,
      attempts INT NOT NULL DEFAULT 0,
      last_attempt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      locked_until TIMESTAMP NULL DEFAULT NULL,
      INDEX (ip), INDEX (email), INDEX (locked_until)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ");
} catch (\Throwable $e) {
  // continuar sin tabla
}
$MAX_ATTEMPTS = 5;
$LOCK_SECONDS = 15 * 60;

if (!isset($_SESSION['rl'])) $_SESSION['rl'] = [];
if (!isset($_SESSION['rl'][$ip])) {
  $_SESSION['rl'][$ip] = ['a' => 0, 't' => time(), 'lock' => 0];
}

$locked_until_ts = 0;
$row = null;
try {
  $stmt = $pdo->prepare("SELECT id, attempts, locked_until FROM login_attempts WHERE ip = :ip AND email = :email LIMIT 1");
  $stmt->execute([':ip' => $ip, ':email' => $email]);
  $row = $stmt->fetch();
  if ($row && !empty($row['locked_until'])) {
    $locked_until_ts = strtotime($row['locked_until']);
    if ($locked_until_ts && time() < $locked_until_ts) {
      $minLeft = max(1, (int)ceil(($locked_until_ts - time()) / 60));
      log_bita($pdo, 'login', ['motivo' => 'rate_limited', 'minLeft' => $minLeft, 'ip' => $ip, 'email' => $email], 'ERROR');
      fail("Demasiados intentos fallidos. Inténtalo de nuevo en ~{$minLeft} min.");
    }
  }
} catch (\Throwable $e) {
  if (!empty($_SESSION['rl'][$ip]['lock']) && time() < $_SESSION['rl'][$ip]['lock']) {
    $minLeft = max(1, (int)ceil(($_SESSION['rl'][$ip]['lock'] - time()) / 60));
    log_bita($pdo, 'login', ['motivo' => 'rate_limited_fallback', 'minLeft' => $minLeft, 'ip' => $ip, 'email' => $email], 'ERROR');
    fail("Demasiados intentos fallidos. Inténtalo de nuevo en ~{$minLeft} min.");
  }
}

// ---------- Buscar usuario (sin pedir 'activo' para esquemas sin esa columna) ----------
try {
  $stmt = $pdo->prepare("
    SELECT id_usuario, nombre_usuario, apellido_usuario, correo_usuario, contrasena_usuario, rol_usuario
    FROM usuarios
    WHERE correo_usuario = :email
    LIMIT 1
  ");
  $stmt->execute([':email' => $email]);
  $u = $stmt->fetch();
} catch (\Throwable $e) {
  log_bita($pdo, 'login', ['motivo' => 'db_user_query', 'email' => $email, 'ex' => $e->getMessage()], 'ERROR');
  fail('Error interno al consultar usuario.');
}

// Intentar leer 'activo' si tu tabla lo tiene (opcional)
$isActive = 1;
try {
  if ($u) {
    $stmt = $pdo->prepare("SELECT activo FROM usuarios WHERE id_usuario = :id LIMIT 1");
    $stmt->execute([':id' => (int)$u['id_usuario']]);
    $tmp = $stmt->fetch();
    if ($tmp && isset($tmp['activo'])) {
      $isActive = (int)$tmp['activo'];
    }
  }
} catch (\Throwable $e) {
  $isActive = 1;
}

// ---------- Verificación de contraseña ----------
$validPassword = false;
$needsRehash   = false;

if ($u && (int)$isActive === 1) {
  $hash = (string)($u['contrasena_usuario'] ?? '');

  // password_hash (bcrypt/argon/…)
  if ($hash !== '' && password_get_info($hash)['algo'] !== 0) {
    if (password_verify($pass, $hash)) {
      $validPassword = true;
      $needsRehash   = password_needs_rehash($hash, PASSWORD_DEFAULT, ['cost' => 12]);
    }
  }

  // Legacy MD5
  if (!$validPassword && preg_match('/^[a-f0-9]{32}$/i', $hash)) {
    if (hash_equals($hash, md5($pass))) {
      $validPassword = true;
      $needsRehash   = true;
    }
  }

  // Legacy texto plano
  if (!$validPassword && $hash !== '' && !preg_match('/^\$2y\$/', $hash)) {
    if (hash_equals($hash, $pass)) {
      $validPassword = true;
      $needsRehash   = true;
    }
  }
}

// ---------- Fallo de autenticación ----------
if (!$u || (int)$isActive !== 1 || !$validPassword) {
  try {
    if ($row && isset($row['id'])) {
      $attempts = (int)$row['attempts'] + 1;
      $lock     = null;
      if ($attempts >= $MAX_ATTEMPTS) {
        $lockTs  = time() + $LOCK_SECONDS;
        $lock    = date('Y-m-d H:i:s', $lockTs);
      }
      $stmt = $pdo->prepare("UPDATE login_attempts SET attempts = :a, locked_until = :l WHERE id = :id");
      $stmt->execute([':a' => $attempts, ':l' => $lock, ':id' => $row['id']]);
    } else {
      $stmt = $pdo->prepare("INSERT INTO login_attempts (ip, email, attempts, locked_until) VALUES (:ip,:email,1,NULL)");
      $stmt->execute([':ip' => $ip, ':email' => $email]);
    }
  } catch (\Throwable $e) {
    $_SESSION['rl'][$ip]['a'] = ($_SESSION['rl'][$ip]['a'] ?? 0) + 1;
    if ($_SESSION['rl'][$ip]['a'] >= $MAX_ATTEMPTS) {
      $_SESSION['rl'][$ip]['lock'] = time() + $LOCK_SECONDS;
    }
  }

  log_bita($pdo, 'login', ['motivo' => 'invalid_credentials', 'ip' => $ip, 'email' => $email], 'ERROR');
  fail('Correo o contraseña incorrectos.');
}

// ---------- Éxito ----------
try {
  // Reset intentos
  try {
    if ($row && isset($row['id'])) {
      $stmt = $pdo->prepare("UPDATE login_attempts SET attempts = 0, locked_until = NULL WHERE id = :id");
      $stmt->execute([':id' => $row['id']]);
    } else {
      $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE ip = :ip AND email = :email");
      $stmt->execute([':ip' => $ip, ':email' => $email]);
    }
  } catch (\Throwable $e) {}

  // Rehash si corresponde (migración)
  if ($needsRehash) {
    $newHash = password_hash($pass, PASSWORD_DEFAULT, ['cost' => 12]);
    $upd = $pdo->prepare("UPDATE usuarios SET contrasena_usuario = :h WHERE id_usuario = :id");
    $upd->execute([':h' => $newHash, ':id' => (int)$u['id_usuario']]);
    log_bita($pdo, 'rehash', ['id_usuario' => (int)$u['id_usuario']], 'OK');
  }

  // ------------------------------
  // SESIÓN: NUEVAS Y LEGACY
  // ------------------------------
  session_regenerate_id(true);

  // Convención nueva
  $_SESSION['id_usuario']       = (int)$u['id_usuario'];
  $_SESSION['nombre_usuario']   = (string)$u['nombre_usuario'];
  $_SESSION['apellido_usuario'] = (string)($u['apellido_usuario'] ?? '');
  $_SESSION['rol_usuario']      = (string)$u['rol_usuario'];
  $_SESSION['auth_time']        = time();

  // Alias legacy para compatibilidad total con vistas antiguas
  $_SESSION['nombre']   = $_SESSION['nombre_usuario'];
  $_SESSION['apellido'] = $_SESSION['apellido_usuario'];
  $_SESSION['rol']      = $_SESSION['rol_usuario'];

  // Log de éxito (antes de remember/redirect)
  log_bita($pdo, 'login', [
    'ip'         => $ip,
    'id_usuario' => (int)$u['id_usuario'],
    'email'      => $email
  ], 'OK');

  // Remember me
  $remember = isset($_POST['remember']) && $_POST['remember'] == '1';
  if ($remember) {
    $pdo->exec("
      CREATE TABLE IF NOT EXISTS user_remember_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_usuario INT NOT NULL,
        selector CHAR(18) NOT NULL,
        validator_hash CHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        UNIQUE(selector),
        INDEX(id_usuario),
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    $selector  = bin2hex(random_bytes(9));
    $validator = bin2hex(random_bytes(32));
    $hashValid = hash('sha256', $validator);
    $expires   = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30);

    $ins = $pdo->prepare("INSERT INTO user_remember_tokens (id_usuario, selector, validator_hash, expires_at) VALUES (:u,:s,:v,:e)");
    $ins->execute([
      ':u' => (int)$u['id_usuario'],
      ':s' => $selector,
      ':v' => $hashValid,
      ':e' => $expires,
    ]);

    $cookie = $selector . ':' . $validator;
    setcookie('PLM_REMEMBER', $cookie, [
      'expires'  => time() + 60 * 60 * 24 * 30,
      'path'     => '/',
      'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
      'httponly' => true,
      'samesite' => 'Lax',
    ]);

    log_bita($pdo, 'remember_set', [
      'id_usuario' => (int)$u['id_usuario'],
      'selector'   => $selector,
      'exp'        => $expires
    ], 'OK');
  }

  // Redirige a tu panel principal
  header('Location: ../views/recepcion.php');
  exit;

} catch (\Throwable $e) {
  log_bita($pdo, 'login', ['motivo' => 'exception', 'ex' => $e->getMessage(), 'email' => $email], 'ERROR');
  fail('Error interno al iniciar sesión.');
}
