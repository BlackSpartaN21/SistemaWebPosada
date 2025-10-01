<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
include '../views/header.php';
require_once '../config/db.php';

try {
$where = [];
$params = [];

if (!empty($_GET['desde']) || !empty($_POST['desde'])) {
    $desde = ($_GET['desde'] ?? $_POST['desde']) . " 00:00:00";
    $where[] = "r.fecha_llegada >= :desde";
    $params[':desde'] = $desde;
}

if (!empty($_GET['hasta']) || !empty($_POST['hasta'])) {
    $hasta = ($_GET['hasta'] ?? $_POST['hasta']) . " 23:59:59";
    $where[] = "r.fecha_llegada <= :hasta";
    $params[':hasta'] = $hasta;
}

if (!empty($_GET['estado']) || !empty($_POST['estado'])) {
    $estado = $_GET['estado'] ?? $_POST['estado'];
    $where[] = "r.estado = :estado";
    $params[':estado'] = $estado;
}


$condiciones = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "
    SELECT 
        r.id_reserva,
        c.documento_cliente,
        CONCAT(c.nombres_cliente, ' ', c.apellidos_cliente) AS nombre_cliente,
        h.nombre_habitacion,
        th.nombre_tipo_habitacion,
        tf.tipo_tarifa,
        tf.precio_tarifa,
        r.fecha_llegada,
        r.fecha_salida,
        r.cantidad_personas,
        m.nombre_metodo_pago,
        r.monto_total,
        r.estado_reserva,
        r.origen_reserva,
        r.observaciones_reserva
    FROM reservas r
    INNER JOIN clientes c ON r.documento_cliente = c.documento_cliente
    INNER JOIN habitaciones h ON r.id_habitacion = h.id_habitacion
    INNER JOIN tipo_habitaciones th ON h.id_tipo_habitacion = th.id_tipo_habitacion
    INNER JOIN tarifas tf ON r.id_tarifa = tf.id_tarifa
    INNER JOIN metodos_de_pago m ON r.id_metodo_pago = m.id_metodo_pago
    $condiciones
    ORDER BY r.fecha_llegada DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al obtener las reservas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Reservas</title>
    <link rel="stylesheet" href="../public/css/datatables.min.css">
    <link rel="stylesheet" href="../public/css/styletabla.css">
    <link rel="stylesheet" href="../public/css/all.css"> <!-- Ruta a tu archivo CSS local -->
</head>
<body>





<div class="container-fluid mt-4">
    <h2 class="text-center mb-4">Reporte de Reservas</h2>
    <div class="table-responsive">
        <table id="clientesTable" class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Documento</th>
                    <th>Cliente</th>
                    <th>Hab.</th>
                    <th>Tipo Habitación</th>
                    <th>Tipo Tarifa</th>
                    <th>Precio Tarifa</th>
                    <th>Fecha Llegada</th>
                    <th>Fecha Salida</th>
                    <th>Personas</th>
                    <th>Método Pago</th>
                    <th>Monto Total</th>
                    <th>Estado</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?= $reserva['id_reserva'] ?></td>
                        <td><?= htmlspecialchars($reserva['documento_cliente']) ?></td>
                        <td><?= htmlspecialchars($reserva['nombre_cliente']) ?></td>
                        <td><?= htmlspecialchars($reserva['nombre_habitacion']) ?></td>
                        <td><?= htmlspecialchars($reserva['nombre_tipo_habitacion']) ?></td>
                        <td><?= htmlspecialchars($reserva['tipo_tarifa']) ?></td>
                        <td>$<?= number_format($reserva['precio_tarifa'], 2) ?></td>
<td>
    <?= date('d-m-Y h:i A', strtotime($reserva['fecha_llegada'])) ?>
</td>
<td>
    <?= date('d-m-Y h:i A', strtotime($reserva['fecha_salida'])) ?>
</td>
                        <td><?= $reserva['cantidad_personas'] ?></td>
                        <td><?= htmlspecialchars($reserva['nombre_metodo_pago']) ?></td>
                        <td>$<?= number_format($reserva['monto_total'], 2) ?></td>
                        <td><?= $reserva['estado_reserva'] ?></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="container mt-4">

    <div class="row g-3 justify-content-center align-items-end text-center mb-4">
        <div class="col-md-3">
            <label for="desde" class="form-label">Desde:</label>
            <form method="GET" action="reportes.php" id="filtrosForm">
                <input type="date" id="desde" name="desde" class="form-control" value="<?= $_GET['desde'] ?? '' ?>">
        </div>

        <div class="col-md-3">
            <label for="hasta" class="form-label">Hasta:</label>
            <input type="date" id="hasta" name="hasta" class="form-control" value="<?= $_GET['hasta'] ?? '' ?>">
        </div>

        <div class="col-md-3">
            <label for="estado" class="form-label">Estado:</label>
            <select id="estado" name="estado" class="form-select">
                <option value="">Todos</option>
                <option value="Confirmada" <?= ($_GET['estado'] ?? '') == 'Confirmada' ? 'selected' : '' ?>>Confirmada</option>
                <option value="Finalizada" <?= ($_GET['estado'] ?? '') == 'Finalizada' ? 'selected' : '' ?>>Finalizada</option>
            </select>
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            <button type="button" class="btn btn-secondary w-100" onclick="window.location.href='reportes.php'">Restablecer</button>
            </form>
        </div>

        <div class="col-md-3">
            <form method="POST" action="../controllers/exportar_pdf.php" target="_blank">
                <input type="hidden" name="desde" value="<?= $_GET['desde'] ?? '' ?>">
                <input type="hidden" name="hasta" value="<?= $_GET['hasta'] ?? '' ?>">
                <input type="hidden" name="estado" value="<?= $_GET['estado'] ?? '' ?>">
                <button type="submit" class="btn btn-danger w-100">
                    Exportar PDF <i class="fa fa-file-pdf"></i>
                </button>
            </form>
        </div>
    </div>
</div>


    <!-- Aquí sigue tu tabla -->
</body>
<!-- jQuery y DataTables -->
<script src="../public/js/jquery-3.7.1.min.js"></script>
<script src="../public/js/datatables.min.js"></script>

<script>
    $(document).ready(function () {
        $('#clientesTable').DataTable({
            language: {
                url: '../public/js/es-ES.json'
            },
            responsive: true
        });
    });
</script>

</html>