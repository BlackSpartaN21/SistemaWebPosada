<?php
include '../config/db.php';
require_once '../config/db.php';
require_once '../config/auth.php';
require_admin();

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de habitación no especificado.']);
    exit;
}

$idHabitacion = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("DELETE FROM habitaciones WHERE id_habitacion = ?");
    $stmt->execute([$idHabitacion]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Habitación eliminada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontró la habitación o ya fue eliminada.']);
    }
} catch (PDOException $e) {
    // Verificar si es error de restricción de clave foránea (habitacion ocupada)
    if ($e->getCode() == '23000' && strpos($e->getMessage(), '1451') !== false) {
        echo json_encode([
            'success' => false,
            'message' => 'No se puede eliminar la habitación porque está asociada a reservas activas.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al eliminar la habitación: ' . $e->getMessage()
        ]);
    }
}
?>
