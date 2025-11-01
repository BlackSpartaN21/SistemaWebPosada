<?php
require_once '../config/auth.php';
require_login();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../views/perfil.php');
  exit;
}

$idSesion = (int)($_SESSION['id_usuario'] ?? 0);
$idForm   = (int)($_POST['id_usuario'] ?? 0);
if ($idForm <= 0 || $idForm !== $idSesion) {
  header('Location: ../views/perfil.php?error=No%20puedes%20cambiar%20la%20contrase%C3%B1a%20de%20otro%20usuario');
  exit;
}

$actual = (string)($_POST['contrasena_actual'] ?? '');
$nueva  = (string)($_POST['nueva_contrasena'] ?? '');

if ($actual === '' || strlen($nueva) < 6) {
  header('Location: ../views/perfil.php?error=Datos%20inv%C3%A1lidos');
  exit;
}

try {
  // Obtener hash actual
  $stmt = $pdo->prepare('SELECT contrasena_usuario FROM usuarios WHERE id_usuario = :id LIMIT 1');
  $stmt->execute([':id' => $idForm]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$row) {
    header('Location: ../views/perfil.php?error=Usuario%20no%20encontrado');
    exit;
  }

  $hashActual = $row['contrasena_usuario'];

  // Validar contraseña actual
  if (!password_verify($actual, $hashActual)) {
    header('Location: ../views/perfil.php?error=La%20contrase%C3%B1a%20actual%20no%20es%20correcta');
    exit;
  }

  // Evitar reutilizar la misma contraseña
  if (password_verify($nueva, $hashActual)) {
    header('Location: ../views/perfil.php?error=La%20nueva%20contrase%C3%B1a%20no%20puede%20ser%20igual%20a%20la%20anterior');
    exit;
  }

  // Guardar nueva contraseña
  $nuevoHash = password_hash($nueva, PASSWORD_DEFAULT);
  $upd = $pdo->prepare('UPDATE usuarios SET contrasena_usuario = :pass WHERE id_usuario = :id');
  $upd->execute([':pass' => $nuevoHash, ':id' => $idForm]);

  // Seguridad extra: regenerar ID de sesión
  session_regenerate_id(true);

  header('Location: ../views/perfil.php?ok=1');
  exit;

} catch (PDOException $e) {
  header('Location: ../views/perfil.php?error=Error%20de%20base%20de%20datos');
  exit;
}
