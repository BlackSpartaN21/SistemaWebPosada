<?php
include '../config/db.php';

if (isset($_POST['id_habitacion']) && isset($_POST['tipo_tarifa'])) {
    $id_habitacion = $_POST['id_habitacion'];
    $tipo_tarifa = $_POST['tipo_tarifa'];

    $query = "SELECT ta.precio_tarifa 
              FROM tarifas ta
              INNER JOIN habitaciones h ON ta.id_tipo_habitacion = h.id_tipo_habitacion
              WHERE h.id_habitacion = :id_habitacion AND ta.tipo_tarifa = :tipo_tarifa
              LIMIT 1";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':id_habitacion' => $id_habitacion,
        ':tipo_tarifa' => $tipo_tarifa
    ]);

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        echo $resultado['precio_tarifa'];
    } else {
        echo "No disponible";
    }
}
?>
