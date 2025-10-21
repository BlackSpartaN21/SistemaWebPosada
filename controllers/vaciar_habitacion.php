<?php
require_once '../config/db.php'; // Ajusta ruta si es necesario

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idHabitacion = $_POST['id_habitacion'];

    // Marcar habitación como disponible
    $sql = "UPDATE habitaciones SET estado_habitacion = 1 WHERE id_habitacion = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $idHabitacion]);

    // Finalizar la reserva activa (si hay alguna)
    $sqlReserva = "UPDATE reservas SET estado_reserva = 'Finalizada' WHERE id_habitacion = :id AND estado_reserva = 'Confirmada'";
    $stmtReserva = $pdo->prepare($sqlReserva);
    $stmtReserva->execute(['id' => $idHabitacion]);

    // Redirigir de nuevo a la página
    header('../views/recepcion.php'); // Cambia por tu archivo principal si es otro
    exit;
}
?>
