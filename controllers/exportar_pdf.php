<?php
require_once '../public/tcpdf/tcpdf.php';
require_once '../config/db.php';

$desde = !empty($_POST['desde']) ? $_POST['desde'] . " 00:00:00" : null;
$hasta = !empty($_POST['hasta']) ? $_POST['hasta'] . " 23:59:59" : null;
$estado = $_POST['estado'] ?? '';


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
    SELECT r.id_reserva, c.nombres_cliente, h.nombre_habitacion, tf.tipo_tarifa, r.fecha_llegada, r.fecha_salida, r.monto_total, r.estado_reserva
    FROM reservas r
    INNER JOIN clientes c ON r.documento_cliente = c.documento_cliente
    INNER JOIN habitaciones h ON r.id_habitacion = h.id_habitacion
    INNER JOIN tarifas tf ON r.id_tarifa = tf.id_tarifa
    $condiciones
    ORDER BY r.fecha_llegada DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear PDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);
$html = '<h2>Reporte de Reservas</h2><table border="1" cellpadding="4"><thead><tr>
            <th>ID</th><th>Cliente</th><th>Hab.</th><th>Tarifa</th>
            <th>F. Llegada</th><th>F. Salida</th><th>Monto</th><th>Estado</th>
        </tr></thead><tbody>';

foreach ($datos as $r) {
    $html .= "<tr>
        <td>{$r['id_reserva']}</td>
        <td>{$r['nombres_cliente']}</td>
        <td>{$r['nombre_habitacion']}</td>
        <td>{$r['tipo_tarifa']}</td>
        <td>{$r['fecha_llegada']}</td>
        <td>{$r['fecha_salida']}</td>
        <td>$" . number_format($r['monto_total'], 2) . "</td>
        <td>{$r['estado_reserva']}</td>
    </tr>";
}
$html .= '</tbody></table>';

$pdf->writeHTML($html);
$pdf->Output('reporte_reservas.pdf', 'I');
