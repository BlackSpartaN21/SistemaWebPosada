<?php
require_once '../config/auth.php';
require_admin();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../views/gestionar_usuarios.php');
  exit;
}

$id_usuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;
$nombre     = trim($_POST['nombre'] ?? '');
$apellido   = trim($_POST['apellido'] ?? '');
$correo     = trim($_POST['correo'] ?? '');
$rol        = trim($_POST['rol'] ?? '');
$contrasena = $_POST['contrasena'] ?? null; // solo en alta

// Validaciones básicas
if ($nombre === '' || $apellido === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL) || !in_array($rol, ['Administrador','Recepcionista'], true)) {
  header('Location: ../views/gestionar_usuarios.php?error=Datos%20inv%C3%A1lidos');
  exit;
}

try {
  // Unicidad de correo
  if ($id_usuario > 0) {
    $stmt = $pdo->prepare('SELECT id_usuario FROM usuarios WHERE correo_usuario = :correo AND id_usuario <> :id');
    $stmt->execute([':correo' => $correo, ':id' => $id_usuario]);
  } else {
    $stmt = $pdo->prepare('SELECT id_usuario FROM usuarios WHERE correo_usuario = :correo');
    $stmt->execute([':correo' => $correo]);
  }
  if ($stmt->fetch()) {
    header('Location: ../views/gestionar_usuarios.php?error=El%20correo%20ya%20est%C3%A1%20registrado');
    exit;
  }

  if ($id_usuario > 0) {
    // --------- Salvaguardas de rol en edición ---------

    // 1) Obtener el rol actual del usuario objetivo
    $cur = $pdo->prepare('SELECT rol_usuario FROM usuarios WHERE id_usuario = :id LIMIT 1');
    $cur->execute([':id' => $id_usuario]);
    $rolActual = $cur->fetchColumn();

    if ($rolActual === false) {
      header('Location: ../views/gestionar_usuarios.php?error=Usuario%20no%20encontrado');
      exit;
    }

    // 2) No permitir que el usuario en sesión se degrade a sí mismo
    if (isset($_SESSION['id_usuario']) && (int)$_SESSION['id_usuario'] === $id_usuario && $rol !== 'Administrador') {
      header('Location: ../views/gestionar_usuarios.php?error=No%20puedes%20cambiar%20tu%20propio%20rol%20a%20Recepcionista');
      exit;
    }

    // 3) No permitir que el último admin sea degradado a recepcionista
    if ($rolActual === 'Administrador' && $rol === 'Recepcionista') {
      $countAdmins = (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol_usuario = 'Administrador'")->fetchColumn();
      if ($countAdmins <= 1) {
        header('Location: ../views/gestionar_usuarios.php?error=No%20puedes%20degradar%20al%20%C3%BAltimo%20Administrador');
        exit;
      }
    }

    // --------- Actualización segura ---------
    $sql = 'UPDATE usuarios
            SET nombre_usuario=:nom, apellido_usuario=:ape, correo_usuario=:corr, rol_usuario=:rol
            WHERE id_usuario=:id';
    $upd = $pdo->prepare($sql);
    $upd->execute([
      ':nom' => $nombre,
      ':ape' => $apellido,
      ':corr' => $correo,
      ':rol' => $rol,
      ':id'  => $id_usuario,
    ]);
  } else {
    if ($contrasena === null || strlen($contrasena) < 6) {
      header('Location: ../views/gestionar_usuarios.php?error=Contrase%C3%B1a%20inv%C3%A1lida');
      exit;
    }
    $hash = password_hash($contrasena, PASSWORD_DEFAULT);
    $ins = $pdo->prepare('INSERT INTO usuarios (nombre_usuario, apellido_usuario, correo_usuario, contrasena_usuario, rol_usuario) VALUES (:nom, :ape, :corr, :pass, :rol)');
    $ins->execute([
      ':nom'  => $nombre,
      ':ape'  => $apellido,
      ':corr' => $correo,
      ':pass' => $hash,
      ':rol'  => $rol,
    ]);
  }

  header('Location: ../views/gestionar_usuarios.php?ok=1');
  exit;

} catch (PDOException $e) {
  header('Location: ../views/gestionar_usuarios.php?error=Error%20de%20base%20de%20datos');
  exit;
}
?>
