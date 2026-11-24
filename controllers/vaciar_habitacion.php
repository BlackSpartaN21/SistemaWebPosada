<?php
// controllers/vaciar_habitacion.php
declare(strict_types=1);

session_start();
require_once '../config/db.php';          // $pdo
require_once '../config/bitacora.php';    // bitacora_log

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $idHabitacion = (int)($_POST['id_habitacion'] ?? 0);

    try {
        // Marcar habitación como disponible (1)
        $sql = "UPDATE habitaciones SET estado_habitacion = 1 WHERE id_habitacion = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $idHabitacion]);
        $habAfectadas = $stmt->rowCount();

        // Finalizar la(s) reserva(s) activa(s) (Confirmada -> Finalizada)
        $sqlReserva = "UPDATE reservas
                       SET estado_reserva = 'Finalizada'
                       WHERE id_habitacion = :id AND estado_reserva = 'Confirmada'";
        $stmtReserva = $pdo->prepare($sqlReserva);
        $stmtReserva->execute([':id' => $idHabitacion]);
        $resAfectadas = $stmtReserva->rowCount();

        // Bitácora OK
        try {
            bitacora_log($pdo, 'Habitaciones', 'vaciar', [
                'id_habitacion'   => $idHabitacion,
                'hab_afectadas'   => $habAfectadas,
                'reservas_finalizadas' => $resAfectadas
            ], 'OK');
        } catch (Throwable $_) {}

    } catch (Throwable $e) {
        // Bitácora ERROR
        try {
            bitacora_log($pdo, 'Habitaciones', 'vaciar', [
                'id_habitacion' => $idHabitacion,
                'ex'            => $e->getMessage()
            ], 'ERROR');
        } catch (Throwable $_) {}
    }

    // Redirigir de nuevo a la página principal
    header('Location: ../views/recepcion.php'); // (corregido)
    exit;
}
