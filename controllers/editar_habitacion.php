<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_habitacion'];
    $nombre = trim($_POST['nombre_habitacion']);
    $descripcion = $_POST['descripcion_habitacion'] ?? '';
    $id_tipo = $_POST['id_tipo_habitacion'];
    $estado = isset($_POST['estado_habitacion']) ? 1 : 0;

    // Verificar si el nombre ya existe en otra habitación
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM habitaciones WHERE nombre_habitacion = ? AND id_habitacion != ?");
    $stmt->execute([$nombre, $id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Nombre duplicado, redirigir con error
        header("Location: ../views/gestionar_habitaciones.php?error=nombre_duplicado");
        exit;
    }

    // Actualizar habitación
    $stmt = $pdo->prepare("UPDATE habitaciones SET nombre_habitacion = ?, descripcion_habitacion = ?, id_tipo_habitacion = ?, estado_habitacion = ? WHERE id_habitacion = ?");
    $stmt->execute([$nombre, $descripcion, $id_tipo, $estado, $id]);

    header("Location: ../views/gestionar_habitaciones.php?success=editado");
    exit;
}
?>
