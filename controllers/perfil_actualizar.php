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
  header('Location: ../views/perfil.php?error=No%20puedes%20editar%20este%20perfil');
  exit;
}

$nombre   = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$correo   = trim($_POST['correo'] ?? '');

if ($nombre === '' || $apellido === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
  header('Location: ../views/perfil.php?error=Datos%20inv%C3%A1lidos');
  exit;
}

try {
  // Verificar unicidad de correo para otros usuarios
  $stmt = $pdo->prepare('SELECT id_usuario FROM usuarios WHERE correo_usuario = :correo AND id_usuario <> :id');
  $stmt->execute([':correo' => $correo, ':id' => $idForm]);
  if ($stmt->fetch()) {
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

  // Reflejar cambios en la sesión
  $_SESSION['nombre']   = $nombre;
  $_SESSION['apellido'] = $apellido;
  $_SESSION['correo']   = $correo;

  header('Location: ../views/perfil.php?ok=1');
  exit;

} catch (PDOException $e) {
  header('Location: ../views/perfil.php?error=Error%20de%20base%20de%20datos');
  exit;
}
