<?php
// controllers/eliminar_habitacion.php
declare(strict_types=1);

session_start();
require_once '../config/auth.php';
require_admin();

header('Content-Type: application/json; charset=utf-8');

require_once '../config/db.php';
require_once '../config/bitacora.php'; // [BITÁCORA]

// Validación del parámetro ID
$idHabitacion = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($idHabitacion <= 0) {
  bitacora_log($pdo, 'Habitaciones', 'eliminar', [
    'motivo' => 'id_invalido',
    'id'     => $_GET['id'] ?? null
  ], 'ERROR');

  echo json_encode(['success' => false, 'message' => 'ID de habitación no especificado o inválido.']);
  exit;
}

try {
  $stmt = $pdo->prepare("DELETE FROM habitaciones WHERE id_habitacion = :id LIMIT 1");
  $stmt->execute([':id' => $idHabitacion]);

  if ($stmt->rowCount() > 0) {
    // OK
    bitacora_log($pdo, 'Habitaciones', 'eliminar', [
      'id_habitacion'   => $idHabitacion,
      'filas_afectadas' => $stmt->rowCount()
    ], 'OK');

    echo json_encode(['success' => true, 'message' => 'Habitación eliminada correctamente.']);
  } else {
    // No encontrada
    bitacora_log($pdo, 'Habitaciones', 'eliminar', [
      'id_habitacion' => $idHabitacion,
      'motivo'        => 'no_encontrada'
    ], 'ERROR');

    echo json_encode(['success' => false, 'message' => 'No se encontró la habitación o ya fue eliminada.']);
  }
} catch (PDOException $e) {
  // Detectar violación de FK (MySQL 1451) → habitación asociada a reservas
  $sqlState   = (string)$e->getCode();                    // '23000' para integridad referencial
  $mysqlCode  = isset($e->errorInfo[1]) ? (int)$e->errorInfo[1] : null; // 1451 delete restrict
  $isFk       = ($sqlState === '23000' && $mysqlCode === 1451)
             || (strpos($e->getMessage(), '1451') !== false);

  if ($isFk) {
    bitacora_log($pdo, 'Habitaciones', 'eliminar', [
      'id_habitacion' => $idHabitacion,
      'motivo'        => 'fk_constraint',
      'sqlstate'      => $sqlState,
      'mysql_code'    => $mysqlCode
    ], 'ERROR');

    echo json_encode([
      'success' => false,
      'message' => 'No se puede eliminar la habitación porque está asociada a reservas activas.'
    ]);
  } else {
    bitacora_log($pdo, 'Habitaciones', 'eliminar', [
      'id_habitacion' => $idHabitacion,
      'sqlstate'      => $sqlState,
      'mysql_code'    => $mysqlCode,
      'ex'            => $e->getMessage()
    ], 'ERROR');

    echo json_encode([
      'success' => false,
      'message' => 'Error al eliminar la habitación: ' . $e->getMessage()
    ]);
  }
}
