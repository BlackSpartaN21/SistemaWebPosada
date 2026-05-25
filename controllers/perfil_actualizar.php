<?php
// controllers/perfil_actualizar.php
declare(strict_types=1);

require_once '../config/auth.php';
require_login();
require_once '../config/db.php';
require_once '../config/bitacora.php'; // [BITÁCORA]

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  try {
    bitacora_log($pdo, 'Perfil', 'actualizar', [
      'motivo' => 'method_not_allowed',
      'method' => $_SERVER['REQUEST_METHOD'] ?? null
    ], 'ERROR');
  } catch (Throwable $_) {}
  header('Location: ../views/perfil.php');
  exit;
}

$idSesion = (int)($_SESSION['id_usuario'] ?? 0);
$idForm   = (int)($_POST['id_usuario'] ?? 0);
if ($idForm <= 0 || $idForm !== $idSesion) {
  try {
    bitacora_log($pdo, 'Perfil', 'actualizar', [
      'motivo'     => 'id_mismatch',
      'id_sesion'  => $idSesion,
      'id_form'    => $idForm
    ], 'ERROR');
  } catch (Throwable $_) {}
  header('Location: ../views/perfil.php?error=No%20puedes%20editar%20este%20perfil');
  exit;
}

$nombre   = trim((string)($_POST['nombre']   ?? ''));
$apellido = trim((string)($_POST['apellido'] ?? ''));
$correo   = trim((string)($_POST['correo']   ?? ''));

if ($nombre === '' || $apellido === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
  try {
    bitacora_log($pdo, 'Perfil', 'actualizar', [
      'motivo'   => 'validacion',
      'id'       => $idForm,
      'correo'   => $correo
    ], 'ERROR');
  } catch (Throwable $_) {}
  header('Location: ../views/perfil.php?error=Datos%20inv%C3%A1lidos');
  exit;
}

try {
  // Verificar unicidad de correo para otros usuarios
  $stmt = $pdo->prepare('SELECT id_usuario FROM usuarios WHERE correo_usuario = :correo AND id_usuario <> :id');
  $stmt->execute([':correo' => $correo, ':id' => $idForm]);
  if ($stmt->fetch()) {
    try {
      bitacora_log($pdo, 'Perfil', 'actualizar', [
        'motivo' => 'correo_duplicado',
        'id'     => $idForm,
        'correo' => $correo
      ], 'ERROR');
    } catch (Throwable $_) {}
    header('Location: ../views/perfil.php?error=El%20correo%20ya%20est%C3%A1%20registrado%20por%20otro%20usuario');
    exit;
  }

  // Actualizar datos
  $upd = $pdo->prepare('
    UPDATE usuarios
       SET nombre_usuario = :nom,
           apellido_usuario = :ape,
           correo_usuario = :corr
     WHERE id_usuario = :id
  ');
  $upd->execute([
    ':nom'  => $nombre,
    ':ape'  => $apellido,
    ':corr' => $correo,
    ':id'   => $idForm,
  ]);

  // Bitácora OK
  try {
    bitacora_log($pdo, 'Perfil', 'actualizar', [
      'id_usuario'      => $idForm,
      'correo'          => $correo,
      'filas_afectadas' => $upd->rowCount()
    ], 'OK');
  } catch (Throwable $_) {}

  // Reflejar cambios en la sesión (mantengo tus claves)
  $_SESSION['nombre']   = $nombre;
  $_SESSION['apellido'] = $apellido;
  $_SESSION['correo']   = $correo;

  header('Location: ../views/perfil.php?ok=1');
  exit;

} catch (PDOException $e) {
  try {
    bitacora_log($pdo, 'Perfil', 'actualizar', [
      'id_usuario' => $idForm,
      'pdo_code'   => $e->getCode(),
      'ex'         => $e->getMessage()
    ], 'ERROR');
  } catch (Throwable $_) {}
  header('Location: ../views/perfil.php?error=Error%20de%20base%20de%20datos');
  exit;
}
