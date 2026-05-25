<?php
// controllers/agregar_habitacion.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

session_start();
require_once '../config/auth.php';
require_admin();

require_once '../config/db.php';
require_once '../config/bitacora.php'; // Bitácora

try {
    // 1) Verificar método
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        exit;
    }

    // 2) Recibir y validar datos
    $nombreHabitacion      = trim((string)($_POST['nombre_habitacion'] ?? ''));
    $descripcionHabitacion = trim((string)($_POST['descripcion_habitacion'] ?? ''));
    $idTipoHabitacion      = (int)($_POST['id_tipo_habitacion'] ?? 0);

    if ($nombreHabitacion === '' || $idTipoHabitacion <= 0) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos: nombre e id de tipo son obligatorios.']);
        exit;
    }

    // 3) Verificar que el tipo de habitación exista
    $stmtTipo = $pdo->prepare("SELECT id_tipo_habitacion FROM tipos_habitacion WHERE id_tipo_habitacion = :id LIMIT 1");
    $stmtTipo->execute([':id' => $idTipoHabitacion]);
    if (!$stmtTipo->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(['success' => false, 'message' => 'El tipo de habitación no existe.']);
        exit;
    }

    // 4) Verificar si el nombre ya existe (case-insensitive)
    $stmtCheck = $pdo->prepare("SELECT 1 FROM habitaciones WHERE LOWER(nombre_habitacion) = LOWER(:nombre) LIMIT 1");
    $stmtCheck->execute([':nombre' => $nombreHabitacion]);
    if ($stmtCheck->fetchColumn()) {
        // Bitácora (ERROR)
        bitacora_log($pdo, 'Habitaciones', 'crear', [
            'nombre'   => $nombreHabitacion,
            'id_tipo'  => $idTipoHabitacion,
            'motivo'   => 'Nombre duplicado'
        ], 'ERROR');

        echo json_encode(['success' => false, 'message' => 'El nombre de la habitación ya existe.']);
        exit;
    }

    // 5) Insertar la habitación (estado_habitacion = 1 -> habilitada/disponible)
    $pdo->beginTransaction();

    $stmtInsert = $pdo->prepare("
        INSERT INTO habitaciones (nombre_habitacion, descripcion_habitacion, id_tipo_habitacion, estado_habitacion)
        VALUES (:nombre, :descripcion, :tipo, :estado)
    ");
    $stmtInsert->execute([
        ':nombre'      => $nombreHabitacion,
        ':descripcion' => $descripcionHabitacion,
        ':tipo'        => $idTipoHabitacion,
        ':estado'      => 1
    ]);

    $idNueva = (int)$pdo->lastInsertId();

    // Bitácora (OK)
    bitacora_log($pdo, 'Habitaciones', 'crear', [
        'id_habitacion' => $idNueva,
        'nombre'        => $nombreHabitacion,
        'id_tipo'       => $idTipoHabitacion
    ], 'OK');

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Habitación agregada correctamente.']);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Bitácora (ERROR)
    try {
        bitacora_log($pdo, 'Habitaciones', 'crear', [
            'nombre'  => $nombreHabitacion ?? null,
            'id_tipo' => $idTipoHabitacion ?? null,
            'ex'      => $e->getMessage()
        ], 'ERROR');
    } catch (Throwable $ignored) {
        // No romper flujo por la bitácora
    }

    echo json_encode(['success' => false, 'message' => 'Error al agregar habitación.']);
}
