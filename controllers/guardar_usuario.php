<?php
// controllers/guardar_usuario.php
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

// Solo POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  bitacora_log($pdo, 'Usuarios', 'guardar', [
    'motivo' => 'method_not_allowed',
    'method' => $_SERVER['REQUEST_METHOD'] ?? null
  ], 'ERROR');
  go(); // mismo comportamiento que tu versión
}

$actor_id  = (int)($_SESSION['id_usuario'] ?? 0);

// Datos
$id_usuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;
$nombre     = trim((string)($_POST['nombre']    ?? ''));
$apellido   = trim((string)($_POST['apellido']  ?? ''));
$correo     = trim((string)($_POST['correo']    ?? ''));
$rol        = trim((string)($_POST['rol']       ?? ''));
$contrasena = $_POST['contrasena'] ?? null; // solo en alta

// Validaciones básicas
if (
  $nombre === '' ||
  $apellido === '' ||
  !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
  !in_array($rol, ['Administrador','Recepcionista'], true)
) {
  bitacora_log($pdo, 'Usuarios', ($id_usuario > 0 ? 'editar' : 'crear'), [
    'motivo'     => 'validacion',
    'target_id'  => $id_usuario,
    'correo'     => $correo,
    'rol'        => $rol
  ], 'ERROR');
  go('error=Datos%20inv%C3%A1lidos');
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
    bitacora_log($pdo, 'Usuarios', ($id_usuario > 0 ? 'editar' : 'crear'), [
      'motivo'    => 'correo_duplicado',
      'target_id' => $id_usuario,
      'correo'    => $correo
    ], 'ERROR');
    go('error=El%20correo%20ya%20est%C3%A1%20registrado');
  }

  if ($id_usuario > 0) {
    // --------- Edición ---------

    // 1) Obtener rol actual del usuario objetivo
    $cur = $pdo->prepare('SELECT rol_usuario FROM usuarios WHERE id_usuario = :id LIMIT 1');
    $cur->execute([':id' => $id_usuario]);
    $rolActual = $cur->fetchColumn();

    if ($rolActual === false) {
      bitacora_log($pdo, 'Usuarios', 'editar', [
        'motivo'    => 'usuario_no_encontrado',
        'target_id' => $id_usuario
      ], 'ERROR');
      go('error=Usuario%20no%20encontrado');
    }

    // 2) No permitir degradarse a sí mismo
    if ($actor_id === $id_usuario && $rol !== 'Administrador') {
      bitacora_log($pdo, 'Usuarios', 'editar', [
        'motivo'    => 'self_role_demotion_denied',
        'actor_id'  => $actor_id,
        'target_id' => $id_usuario,
        'rol_new'   => $rol
      ], 'ERROR');
      go('error=No%20puedes%20cambiar%20tu%20propio%20rol%20a%20Recepcionista');
    }

    // 3) No degradar al último admin
    if ($rolActual === 'Administrador' && $rol === 'Recepcionista') {
      $countAdmins = (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol_usuario = 'Administrador'")->fetchColumn();
      if ($countAdmins <= 1) {
        bitacora_log($pdo, 'Usuarios', 'editar', [
          'motivo'      => 'last_admin_demote_denied',
          'target_id'   => $id_usuario,
          'rol_old'     => 'Administrador',
          'rol_new'     => 'Recepcionista',
          'total_admin' => $countAdmins
        ], 'ERROR');
        go('error=No%20puedes%20degradar%20al%20%C3%BAltimo%20Administrador');
      }
    }

    // Actualización
    $sql = 'UPDATE usuarios
            SET nombre_usuario=:nom, apellido_usuario=:ape, correo_usuario=:corr, rol_usuario=:rol
            WHERE id_usuario=:id';
    $upd = $pdo->prepare($sql);
    $upd->execute([
      ':nom'  => $nombre,
      ':ape'  => $apellido,
      ':corr' => $correo,
      ':rol'  => $rol,
      ':id'   => $id_usuario,
    ]);

    bitacora_log($pdo, 'Usuarios', 'editar', [
      'actor_id'        => $actor_id,
      'target_id'       => $id_usuario,
      'correo'          => $correo,
      'rol_old'         => $rolActual,
      'rol_new'         => $rol,
      'filas_afectadas' => $upd->rowCount()
    ], 'OK');

  } else {
    // --------- Alta ---------
    if ($contrasena === null || strlen($contrasena) < 6) {
      bitacora_log($pdo, 'Usuarios', 'crear', [
        'motivo'   => 'password_invalida',
        'correo'   => $correo,
        'rol'      => $rol
      ], 'ERROR');
      go('error=Contrase%C3%B1a%20inv%C3%A1lida');
    }

    $hash = password_hash($contrasena, PASSWORD_DEFAULT);
    $ins = $pdo->prepare('INSERT INTO usuarios (nombre_usuario, apellido_usuario, correo_usuario, contrasena_usuario, rol_usuario)
                          VALUES (:nom, :ape, :corr, :pass, :rol)');
    $ins->execute([
      ':nom'  => $nombre,
      ':ape'  => $apellido,
      ':corr' => $correo,
      ':pass' => $hash,
      ':rol'  => $rol,
    ]);

    $newId = (int)$pdo->lastInsertId();

    bitacora_log($pdo, 'Usuarios', 'crear', [
      'actor_id'  => $actor_id,
      'target_id' => $newId,
      'correo'    => $correo,
      'rol'       => $rol
    ], 'OK');
  }

  go('ok=1');

} catch (PDOException $e) {
  bitacora_log($pdo, 'Usuarios', ($id_usuario > 0 ? 'editar' : 'crear'), [
    'actor_id'  => $actor_id,
    'target_id' => $id_usuario ?: null,
    'pdo_code'  => $e->getCode(),
    'ex'        => $e->getMessage()
  ], 'ERROR');
  go('error=Error%20de%20base%20de%20datos');
}
