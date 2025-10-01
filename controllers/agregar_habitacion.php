<?php
include '../config/db.php';

// Recibir datos del formulario
$nombreHabitacion = $_POST['nombre_habitacion'];
$descripcionHabitacion = $_POST['descripcion_habitacion'];
$idTipoHabitacion = $_POST['id_tipo_habitacion'];

// Verificar si la habitación ya existe
$stmt = $pdo->prepare("SELECT * FROM habitaciones WHERE nombre_habitacion = :nombre");
$stmt->execute(['nombre' => $nombreHabitacion]);
$habitacionExistente = $stmt->fetch(PDO::FETCH_ASSOC);

if ($habitacionExistente) {
    // Si ya existe, devolver un error
    echo json_encode(['success' => false, 'message' => 'El nombre de la habitación ya existe.']);
    exit;
}

// Si no existe, agregar la habitación
$stmtInsert = $pdo->prepare("INSERT INTO habitaciones (nombre_habitacion, descripcion_habitacion, id_tipo_habitacion, estado_habitacion) VALUES (?, ?, ?, ?)");
$stmtInsert->execute([$nombreHabitacion, $descripcionHabitacion, $idTipoHabitacion, 1]);

// Si se agrega correctamente, devolver éxito
echo json_encode(['success' => true, 'message' => 'Habitación agregada correctamente.']);
?>
