<?php
// controllers/editar_habitacion.php
declare(strict_types=1);

session_start();
require_once '../config/auth.php';
require_admin();

require_once '../config/db.php';
require_once '../config/bitacora.php'; // [BITÁCORA]

function go(string $qs): void {
  header("Location: ../views/gestionar_habitaciones.php?$qs");
  exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  // [BITÁCORA] Método no permitido
  bitacora_log($pdo, 'Habitaciones', 'editar', [
    'motivo' => 'method_not_allowed',
    'method' => $_SERVER['REQUEST_METHOD'] ?? null
  ], 'ERROR');
  http_response_code(405);
  echo 'Método no permitido';
  exit;
}

$id         = (int)($_POST['id_habitacion'] ?? 0);
$nombre     = trim((string)($_POST['nombre_habitacion'] ?? ''));
$descripcion= trim((string)($_POST['descripcion_habitacion'] ?? ''));
$id_tipo    = (int)($_POST['id_tipo_habitacion'] ?? 0);
$estado     = isset($_POST['estado_habitacion']) ? 1 : 0;

// Validaciones básicas
$errors = [];
if ($id <= 0)              $errors[] = 'id_invalido';
if ($nombre === '')        $errors[] = 'nombre_vacio';
if ($id_tipo <= 0)         $errors[] = 'tipo_invalido';

if ($errors) {
  bitacora_log($pdo, 'Habitaciones', 'editar', [
    'id_habitacion' => $id,
    'id_tipo'       => $id_tipo,
    'motivo'        => 'validacion',
    'errores'       => $errors
  ], 'ERROR');
  go('error=validacion');
}

// Verificar que el tipo de habitación exista
$stmtTipo = $pdo->prepare("SELECT id_tipo_habitacion FROM tipos_habitacion WHERE id_tipo_habitacion = :id LIMIT 1");
$stmtTipo->execute([':id' => $id_tipo]);
if (!$stmtTipo->fetch()) {
  bitacora_log($pdo, 'Habitaciones', 'editar', [
    'id_habitacion' => $id,
    'id_tipo'       => $id_tipo,
    'motivo'        => 'tipo_no_existe'
  ], 'ERROR');
  go('error=tipo_no_existe');
}

// Verificar duplicado de nombre en OTRA habitación (case-insensitive)
$stmtDup = $pdo->prepare("
  SELECT COUNT(*) 
  FROM habitaciones 
  WHERE LOWER(nombre_habitacion) = LOWER(:nombre) 
    AND id_habitacion <> :id
");
$stmtDup->execute([':nombre' => $nombre, ':id' => $id]);
if ((int)$stmtDup->fetchColumn() > 0) {
  bitacora_log($pdo, 'Habitaciones', 'editar', [
    'id_habitacion' => $id,
    'nombre'        => $nombre,
    'motivo'        => 'nombre_duplicado'
  ], 'ERROR');
  go('error=nombre_duplicado');
}

try {
  $pdo->beginTransaction();

  $stmt = $pdo->prepare("
    UPDATE habitaciones
       SET nombre_habitacion     = :nombre,
           descripcion_habitacion= :descripcion,
           id_tipo_habitacion    = :id_tipo,
           estado_habitacion     = :estado
     WHERE id_habitacion         = :id
     LIMIT 1
  ");
  $stmt->execute([
    ':nombre'      => $nombre,
    ':descripcion' => $descripcion,
    ':id_tipo'     => $id_tipo,
    ':estado'      => $estado,
    ':id'          => $id
  ]);

  $afectadas = $stmt->rowCount();

  // [BITÁCORA] OK
  bitacora_log($pdo, 'Habitaciones', 'editar', [
    'id_habitacion'   => $id,
    'nombre'          => $nombre,
    'id_tipo'         => $id_tipo,
    'estado'          => $estado,
    'filas_afectadas' => $afectadas
  ], 'OK');

  $pdo->commit();
  go('success=editado');
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();

  // [BITÁCORA] ERROR
  bitacora_log($pdo, 'Habitaciones', 'editar', [
    'id_habitacion' => $id,
    'nombre'        => $nombre,
    'id_tipo'       => $id_tipo,
    'estado'        => $estado,
    'ex'            => $e->getMessage()
  ], 'ERROR');

  go('error=excepcion');
}
