<?php
// controllers/registrar.php
session_start();
require_once '../config/db.php';

function back_to($fallback = '../views/recepcion.php') {
  $to = $_SERVER['HTTP_REFERER'] ?? $fallback;
  header('Location: ' . $to);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tipo        = trim($_POST['tipo_documento_cliente'] ?? '');
  $documento   = trim($_POST['documento_cliente'] ?? '');
  $nombres     = trim($_POST['nombres_cliente'] ?? '');
  $apellidos   = trim($_POST['apellidos_cliente'] ?? '');
  $telefono    = trim($_POST['telefono_cliente'] ?? '');
  $correo      = trim($_POST['correo_cliente'] ?? '');
  $descripcion = trim($_POST['descripcion_cliente'] ?? '');

  $errors = [];
  if (!in_array($tipo, ['V','E','P','J'], true))                                 $errors[] = 'Tipo de documento inválido.';
  if ($documento === '' || !ctype_digit($documento) || strlen($documento) > 10)  $errors[] = 'Documento inválido.';
  if ($telefono === '' || !ctype_digit($telefono) || strlen($telefono) !== 11)   $errors[] = 'Teléfono inválido.';
  if ($nombres === ''  || mb_strlen($nombres) > 50)                               $errors[] = 'Nombres inválidos.';
  if ($apellidos === ''|| mb_strlen($apellidos) > 50)                             $errors[] = 'Apellidos inválidos.';
  if ($correo === ''   || !filter_var($correo, FILTER_VALIDATE_EMAIL))            $errors[] = 'Correo inválido.';
  if (mb_strlen($descripcion) > 100)                                              $errors[] = 'Descripción muy larga (máx. 100).';

  if ($errors) {
    $_SESSION['flash_alert_type'] = 'error';
    $_SESSION['flash_alert_msg']  = implode(' ', $errors);
    back_to();
  }

  try {
    $sql = "INSERT INTO clientes
              (tipo_documento_cliente, documento_cliente, nombres_cliente, apellidos_cliente, telefono_cliente, correo_cliente, descripcion_cliente, fecha_creacion_cliente)
            VALUES
              (:tipo, :documento, :nombres, :apellidos, :telefono, :correo, :descripcion, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':tipo'        => $tipo,
      ':documento'   => $documento,
      ':nombres'     => $nombres,
      ':apellidos'   => $apellidos,
      ':telefono'    => $telefono,
      ':correo'      => $correo,
      ':descripcion' => $descripcion,
    ]);

    $_SESSION['flash_alert_type'] = 'success';
    $_SESSION['flash_alert_msg']  = 'Cliente registrado correctamente.';
  } catch (PDOException $e) {
    $msg = ($e->getCode() === '23000')
         ? 'Documento o correo ya registrados.'
         : 'Error al registrar (código '.$e->getCode().').';
    $_SESSION['flash_alert_type'] = 'error';
    $_SESSION['flash_alert_msg']  = $msg;
  }

  back_to();
}

http_response_code(405);
echo 'Método no permitido';
