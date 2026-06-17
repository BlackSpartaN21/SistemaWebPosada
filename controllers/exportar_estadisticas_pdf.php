<?php
// controllers/exportar_estadisticas_pdf.php
declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../views/login.php');
    exit;
}

$rolSesion = $_SESSION['rol'] ?? $_SESSION['rol_usuario'] ?? '';
if ($rolSesion !== 'Administrador') {
    header('Location: ../views/recepcion.php?error=Acceso%20denegado');
    exit;
}

require_once '../config/db.php';
require_once '../config/bitacora.php';
require_once '../public/tcpdf/tcpdf.php';

date_default_timezone_set('America/Caracas');

$nombre   = $_SESSION['nombre_usuario']   ?? $_SESSION['nombre']   ?? '';
$apellido = $_SESSION['apellido_usuario'] ?? $_SESSION['apellido'] ?? '';
$rol      = $_SESSION['rol_usuario']      ?? $_SESSION['rol']      ?? '';
$correo   = $_SESSION['correo_usuario']   ?? $_SESSION['correo']   ?? '';

$exportUser = trim($nombre . ' ' . $apellido);
if ($exportUser === '') {
    $exportUser = $correo ?: 'Usuario';
}
$exportRole     = $rol ?: 'Sin rol';
$exportDatetime = date('d-m-Y H:i:s');

$tipoFiltro = $_POST['tipo'] ?? $_GET['tipo'] ?? 'mes';
$permitidos = ['dia', 'mes', 'anio'];
if (!in_array($tipoFiltro, $permitidos, true)) {
    $tipoFiltro = 'mes';
}

$fechaDia  = $_POST['fecha'] ?? $_GET['fecha'] ?? date('Y-m-d');
$fechaMes  = $_POST['mes']   ?? $_GET['mes']   ?? date('Y-m');
$fechaAnio = $_POST['anio']  ?? $_GET['anio']  ?? date('Y');

$where = ["r.estado_reserva <> 'Cancelada'"];
$params = [];
$tituloPeriodo = '';
$subtituloFiltro = '';
$anioComparacion = date('Y');

switch ($tipoFiltro) {
    case 'dia':
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaDia)) {
            $fechaDia = date('Y-m-d');
        }
        $where[] = 'DATE(r.fecha_llegada) = :fecha_dia';
        $params[':fecha_dia'] = $fechaDia;
        $tituloPeriodo = date('d-m-Y', strtotime($fechaDia));
        $subtituloFiltro = 'Día: ' . $tituloPeriodo;
        $anioComparacion = date('Y', strtotime($fechaDia));
        break;

    case 'anio':
        if (!preg_match('/^\d{4}$/', $fechaAnio)) {
            $fechaAnio = date('Y');
        }
        $where[] = 'YEAR(r.fecha_llegada) = :fecha_anio';
        $params[':fecha_anio'] = (int)$fechaAnio;
        $tituloPeriodo = $fechaAnio;
        $subtituloFiltro = 'Año: ' . $tituloPeriodo;
        $anioComparacion = $fechaAnio;
        break;

    case 'mes':
    default:
        if (!preg_match('/^\d{4}-\d{2}$/', $fechaMes)) {
            $fechaMes = date('Y-m');
        }
        $where[] = "DATE_FORMAT(r.fecha_llegada, '%Y-%m') = :fecha_mes";
        $params[':fecha_mes'] = $fechaMes;
        $tituloPeriodo = date('m-Y', strtotime($fechaMes . '-01'));
        $subtituloFiltro = 'Mes: ' . $tituloPeriodo;
        $anioComparacion = date('Y', strtotime($fechaMes . '-01'));
        break;
}

$condiciones = 'WHERE ' . implode(' AND ', $where);

$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

class PDF extends TCPDF {
    public $brandRGB = [186, 59, 10];
    public $logoPath = '../public/img/LogoPosada.jpg';
    public $titulo   = 'Estadística de Habitaciones Alquiladas - Posada Las Mandarinas';

    public $exportUser = 'Usuario';
    public $exportRole = 'Sin rol';
    public $exportDatetime = '';

    public function Header() {
        if (is_file($this->logoPath)) {
            $this->Image($this->logoPath, 10, 8, 24);
        }

        $this->SetFont('helvetica', 'B', 14);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 7, $this->titulo, 0, 1, 'C');

        $this->SetDrawColor($this->brandRGB[0], $this->brandRGB[1], $this->brandRGB[2]);
        $this->SetLineWidth(0.8);
        $this->Line(10, 22, 200, 22);
        $this->Ln(4);
    }

    public function Footer() {
        $this->SetY(-18);
        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 5, 'Generado por Sistema Web - Posada Las Mandarinas | Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, 1, 'C');
        $this->SetFont('helvetica', '', 8.5);
        $this->Cell(0, 5, 'Fecha y hora: '.$this->exportDatetime.'  |  Usuario: '.$this->exportUser.'  ('.$this->exportRole.')', 0, 0, 'C');
    }
}

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function money($value): string {
    return '$ ' . number_format((float)$value, 2, ',', '.');
}

try {
    $stmtResumen = $pdo->prepare("
        SELECT
            COUNT(*) AS total_reservas,
            COALESCE(SUM(r.monto_total), 0) AS total_ventas,
            COALESCE(AVG(r.monto_total), 0) AS promedio_venta,
            COUNT(DISTINCT r.id_habitacion) AS habitaciones_vendidas
        FROM reservas r
        $condiciones
    ");
    $stmtResumen->execute($params);
    $resumen = $stmtResumen->fetch(PDO::FETCH_ASSOC) ?: [];

    $stmtHabitaciones = $pdo->prepare("
        SELECT
            h.id_habitacion,
            h.nombre_habitacion,
            th.nombre_tipo_habitacion,
            COUNT(r.id_reserva) AS cantidad_reservas,
            COALESCE(SUM(r.monto_total), 0) AS total_ventas
        FROM reservas r
        INNER JOIN habitaciones h ON r.id_habitacion = h.id_habitacion
        INNER JOIN tipo_habitaciones th ON h.id_tipo_habitacion = th.id_tipo_habitacion
        $condiciones
        GROUP BY h.id_habitacion, h.nombre_habitacion, th.nombre_tipo_habitacion
        ORDER BY total_ventas DESC, cantidad_reservas DESC, h.nombre_habitacion ASC
    ");
    $stmtHabitaciones->execute($params);
    $ventasHabitaciones = $stmtHabitaciones->fetchAll(PDO::FETCH_ASSOC);

    $stmtHabitacionMasVendida = $pdo->prepare("
        SELECT h.nombre_habitacion, th.nombre_tipo_habitacion, COUNT(r.id_reserva) AS cantidad_reservas, COALESCE(SUM(r.monto_total), 0) AS total_ventas
        FROM reservas r
        INNER JOIN habitaciones h ON r.id_habitacion = h.id_habitacion
        INNER JOIN tipo_habitaciones th ON h.id_tipo_habitacion = th.id_tipo_habitacion
        $condiciones
        GROUP BY h.id_habitacion, h.nombre_habitacion, th.nombre_tipo_habitacion
        ORDER BY cantidad_reservas DESC, total_ventas DESC, h.nombre_habitacion ASC
        LIMIT 1
    ");
    $stmtHabitacionMasVendida->execute($params);
    $habitacionMasVendida = $stmtHabitacionMasVendida->fetch(PDO::FETCH_ASSOC) ?: null;

    $stmtHabitacionMayorIngreso = $pdo->prepare("
        SELECT h.nombre_habitacion, th.nombre_tipo_habitacion, COUNT(r.id_reserva) AS cantidad_reservas, COALESCE(SUM(r.monto_total), 0) AS total_ventas
        FROM reservas r
        INNER JOIN habitaciones h ON r.id_habitacion = h.id_habitacion
        INNER JOIN tipo_habitaciones th ON h.id_tipo_habitacion = th.id_tipo_habitacion
        $condiciones
        GROUP BY h.id_habitacion, h.nombre_habitacion, th.nombre_tipo_habitacion
        ORDER BY total_ventas DESC, cantidad_reservas DESC, h.nombre_habitacion ASC
        LIMIT 1
    ");
    $stmtHabitacionMayorIngreso->execute($params);
    $habitacionMayorIngreso = $stmtHabitacionMayorIngreso->fetch(PDO::FETCH_ASSOC) ?: null;

    $stmtTarifaMasUsada = $pdo->prepare("
        SELECT tf.tipo_tarifa, COUNT(r.id_reserva) AS cantidad_reservas, COALESCE(SUM(r.monto_total), 0) AS total_ventas
        FROM reservas r
        INNER JOIN tarifas tf ON r.id_tarifa = tf.id_tarifa
        $condiciones
        GROUP BY tf.tipo_tarifa
        ORDER BY cantidad_reservas DESC, total_ventas DESC
        LIMIT 1
    ");
    $stmtTarifaMasUsada->execute($params);
    $tarifaMasUsada = $stmtTarifaMasUsada->fetch(PDO::FETCH_ASSOC) ?: null;

    $stmtMetodoMasUsado = $pdo->prepare("
        SELECT m.nombre_metodo_pago, COUNT(r.id_reserva) AS cantidad_reservas, COALESCE(SUM(r.monto_total), 0) AS total_ventas
        FROM reservas r
        INNER JOIN metodos_de_pago m ON r.id_metodo_pago = m.id_metodo_pago
        $condiciones
        GROUP BY m.id_metodo_pago, m.nombre_metodo_pago
        ORDER BY cantidad_reservas DESC, total_ventas DESC
        LIMIT 1
    ");
    $stmtMetodoMasUsado->execute($params);
    $metodoMasUsado = $stmtMetodoMasUsado->fetch(PDO::FETCH_ASSOC) ?: null;

    $grupoTiempo = $tipoFiltro === 'dia'
        ? "DATE_FORMAT(r.fecha_llegada, '%H:00')"
        : ($tipoFiltro === 'anio' ? "DATE_FORMAT(r.fecha_llegada, '%m-%Y')" : "DATE_FORMAT(r.fecha_llegada, '%d-%m')");
    $ordenTiempo = $tipoFiltro === 'dia'
        ? 'HOUR(r.fecha_llegada)'
        : ($tipoFiltro === 'anio' ? 'MONTH(r.fecha_llegada)' : 'DAY(r.fecha_llegada)');

    $stmtTiempo = $pdo->prepare("
        SELECT $grupoTiempo AS etiqueta, COUNT(r.id_reserva) AS cantidad_reservas, COALESCE(SUM(r.monto_total), 0) AS total_ventas
        FROM reservas r
        $condiciones
        GROUP BY etiqueta, $ordenTiempo
        ORDER BY $ordenTiempo ASC
    ");
    $stmtTiempo->execute($params);
    $ventasTiempo = $stmtTiempo->fetchAll(PDO::FETCH_ASSOC);

    $stmtComparacionMensual = $pdo->prepare("
        SELECT MONTH(r.fecha_llegada) AS numero_mes, COUNT(r.id_reserva) AS cantidad_reservas, COALESCE(SUM(r.monto_total), 0) AS total_ventas
        FROM reservas r
        WHERE r.estado_reserva <> 'Cancelada' AND YEAR(r.fecha_llegada) = :anio_comparacion
        GROUP BY MONTH(r.fecha_llegada)
        ORDER BY MONTH(r.fecha_llegada) ASC
    ");
    $stmtComparacionMensual->execute([':anio_comparacion' => (int)$anioComparacion]);
    $comparacionRaw = $stmtComparacionMensual->fetchAll(PDO::FETCH_ASSOC);
    $comparacionMensual = [];
    foreach ($meses as $num => $nombreMes) {
        $comparacionMensual[$num] = ['numero_mes' => $num, 'mes' => $nombreMes, 'cantidad_reservas' => 0, 'total_ventas' => 0.0];
    }
    foreach ($comparacionRaw as $row) {
        $num = (int)$row['numero_mes'];
        if (isset($comparacionMensual[$num])) {
            $comparacionMensual[$num]['cantidad_reservas'] = (int)$row['cantidad_reservas'];
            $comparacionMensual[$num]['total_ventas'] = (float)$row['total_ventas'];
        }
    }

    $stmtTopClientes = $pdo->prepare("
        SELECT c.documento_cliente, CONCAT(c.nombres_cliente, ' ', c.apellidos_cliente) AS nombre_cliente,
               COUNT(r.id_reserva) AS cantidad_reservas, COALESCE(SUM(r.monto_total), 0) AS total_ventas, MAX(r.fecha_llegada) AS ultima_visita
        FROM reservas r
        INNER JOIN clientes c ON r.documento_cliente = c.documento_cliente
        $condiciones
        GROUP BY c.documento_cliente, c.nombres_cliente, c.apellidos_cliente
        ORDER BY cantidad_reservas DESC, total_ventas DESC, ultima_visita DESC
        LIMIT 5
    ");
    $stmtTopClientes->execute($params);
    $topClientes = $stmtTopClientes->fetchAll(PDO::FETCH_ASSOC);

    $stmtDiasMovimiento = $pdo->prepare("
        SELECT
            DAYOFWEEK(r.fecha_llegada) AS numero_dia,
            CASE DAYOFWEEK(r.fecha_llegada)
                WHEN 1 THEN 'Domingo'
                WHEN 2 THEN 'Lunes'
                WHEN 3 THEN 'Martes'
                WHEN 4 THEN 'Miércoles'
                WHEN 5 THEN 'Jueves'
                WHEN 6 THEN 'Viernes'
                WHEN 7 THEN 'Sábado'
            END AS nombre_dia,
            COUNT(r.id_reserva) AS cantidad_reservas,
            COALESCE(SUM(r.monto_total), 0) AS total_ventas
        FROM reservas r
        $condiciones
        GROUP BY numero_dia, nombre_dia
        ORDER BY cantidad_reservas DESC, total_ventas DESC, numero_dia ASC
    ");
    $stmtDiasMovimiento->execute($params);
    $diasMovimiento = $stmtDiasMovimiento->fetchAll(PDO::FETCH_ASSOC);

    $stmtDetalle = $pdo->prepare("
        SELECT
            r.id_reserva, h.nombre_habitacion, th.nombre_tipo_habitacion, tf.tipo_tarifa,
            r.fecha_llegada, r.fecha_salida, r.monto_total, r.estado_reserva,
            m.nombre_metodo_pago, CONCAT(c.nombres_cliente, ' ', c.apellidos_cliente) AS nombre_cliente
        FROM reservas r
        INNER JOIN habitaciones h ON r.id_habitacion = h.id_habitacion
        INNER JOIN tipo_habitaciones th ON h.id_tipo_habitacion = th.id_tipo_habitacion
        INNER JOIN tarifas tf ON r.id_tarifa = tf.id_tarifa
        INNER JOIN metodos_de_pago m ON r.id_metodo_pago = m.id_metodo_pago
        INNER JOIN clientes c ON r.documento_cliente = c.documento_cliente
        $condiciones
        ORDER BY r.fecha_llegada DESC
    ");
    $stmtDetalle->execute($params);
    $detalleVentas = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->SetCreator('Sistema Web Posada Las Mandarinas');
    $pdf->SetAuthor('Posada Las Mandarinas');
    $pdf->SetTitle('Estadística de Ventas');
    $pdf->SetMargins(10, 28, 10);
    $pdf->SetAutoPageBreak(true, 18);
    $pdf->exportUser = $exportUser;
    $pdf->exportRole = $exportRole;
    $pdf->exportDatetime = $exportDatetime;
    $pdf->AddPage();

    $brand = '#ba3b0a';
    $thStyle = 'padding:6px; font-size:9.5px; background-color:'.$brand.'; color:#ffffff; text-align:center;';
    $tdStyle = 'padding:5px; font-size:8.7px; border-bottom:1px solid #dddddd;';

    $totalVentas = (float)($resumen['total_ventas'] ?? 0);
    $totalReservas = (int)($resumen['total_reservas'] ?? 0);
    $promedioVenta = (float)($resumen['promedio_venta'] ?? 0);
    $habitacionesVendidas = (int)($resumen['habitaciones_vendidas'] ?? 0);

    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Write(6, 'Resumen estadístico');
    $pdf->Ln(7);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->writeHTML('<span style="border:1px solid '.$brand.'; color:'.$brand.'; padding:3px 8px; font-size:10px;"><strong>Filtro aplicado:</strong> '.e($subtituloFiltro).'</span>', true, false, true, false, '');

    $cardStyle = 'border:1px solid #dddddd; padding:7px; font-size:10px;';
    $cardTitle = 'color:#666666; font-size:9px;';
    $cardValue = 'color:#000000; font-size:13px; font-weight:bold;';

    $htmlResumen = '
        <table width="100%" cellpadding="5" cellspacing="0">
          <tr>
            <td width="25%" style="'.$cardStyle.'"><span style="'.$cardTitle.'">Total ventas</span><br><span style="'.$cardValue.'">'.money($totalVentas).'</span></td>
            <td width="25%" style="'.$cardStyle.'"><span style="'.$cardTitle.'">Reservas</span><br><span style="'.$cardValue.'">'.$totalReservas.'</span></td>
            <td width="25%" style="'.$cardStyle.'"><span style="'.$cardTitle.'">Promedio</span><br><span style="'.$cardValue.'">'.money($promedioVenta).'</span></td>
            <td width="25%" style="'.$cardStyle.'"><span style="'.$cardTitle.'">Habitaciones alquiladas</span><br><span style="'.$cardValue.'">'.$habitacionesVendidas.'</span></td>
          </tr>
        </table>
    ';
    $pdf->Ln(2);
    $pdf->writeHTML($htmlResumen, true, false, true, false, '');

    $hmv = $habitacionMasVendida ? ('Hab. '.e($habitacionMasVendida['nombre_habitacion']).' - '.(int)$habitacionMasVendida['cantidad_reservas'].' venta(s)') : 'Sin datos';
    $hmi = $habitacionMayorIngreso ? ('Hab. '.e($habitacionMayorIngreso['nombre_habitacion']).' - '.money($habitacionMayorIngreso['total_ventas'])) : 'Sin datos';
    $tmu = $tarifaMasUsada ? (e($tarifaMasUsada['tipo_tarifa']).' - '.(int)$tarifaMasUsada['cantidad_reservas'].' venta(s)') : 'Sin datos';
    $mmu = $metodoMasUsado ? (e($metodoMasUsado['nombre_metodo_pago']).' - '.(int)$metodoMasUsado['cantidad_reservas'].' pago(s)') : 'Sin datos';

    $pdf->Ln(1);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Write(6, 'Indicadores administrativos');
    $pdf->Ln(7);
    $htmlIndicadores = '
        <table width="100%" cellpadding="5" cellspacing="0">
          <tr>
            <td width="50%" style="'.$cardStyle.'"><strong>Habitación más vendida:</strong><br>'.$hmv.'</td>
            <td width="50%" style="'.$cardStyle.'"><strong>Habitación con mayor ingreso:</strong><br>'.$hmi.'</td>
          </tr>
          <tr>
            <td width="50%" style="'.$cardStyle.'"><strong>Tipo de tarifa más usado:</strong><br>'.$tmu.'</td>
            <td width="50%" style="'.$cardStyle.'"><strong>Método de pago más usado:</strong><br>'.$mmu.'</td>
          </tr>
        </table>
    ';
    $pdf->writeHTML($htmlIndicadores, true, false, true, false, '');

    $pdf->Ln(2);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Write(6, 'Comparación mensual del año '.$anioComparacion);
    $pdf->Ln(7);
    $htmlMensual = '<table width="100%" cellpadding="0" cellspacing="0"><thead><tr>
        <th style="'.$thStyle.'" width="35%">Mes</th>
        <th style="'.$thStyle.'" width="30%">Reservas</th>
        <th style="'.$thStyle.'" width="35%">Total vendido</th>
      </tr></thead><tbody>';
    foreach ($comparacionMensual as $m) {
        $htmlMensual .= '<tr>
            <td style="'.$tdStyle.'">'.e($m['mes']).'</td>
            <td style="'.$tdStyle.' text-align:center;">'.(int)$m['cantidad_reservas'].'</td>
            <td style="'.$tdStyle.' text-align:right;"><strong>'.money($m['total_ventas']).'</strong></td>
          </tr>';
    }
    $htmlMensual .= '</tbody></table>';
    $pdf->writeHTML($htmlMensual, true, false, true, false, '');

    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Write(6, 'Top 5 clientes frecuentes');
    $pdf->Ln(7);
    $htmlClientes = '<table width="100%" cellpadding="0" cellspacing="0"><thead><tr>
        <th style="'.$thStyle.'" width="8%">#</th>
        <th style="'.$thStyle.'" width="36%">Cliente</th>
        <th style="'.$thStyle.'" width="18%">Documento</th>
        <th style="'.$thStyle.'" width="18%">Reservas</th>
        <th style="'.$thStyle.'" width="20%">Total</th>
      </tr></thead><tbody>';
    if (!$topClientes) {
        $htmlClientes .= '<tr><td colspan="5" style="'.$tdStyle.' text-align:center;">No hay clientes frecuentes para el periodo seleccionado.</td></tr>';
    } else {
        foreach ($topClientes as $i => $c) {
            $htmlClientes .= '<tr>
                <td style="'.$tdStyle.' text-align:center;">'.($i + 1).'</td>
                <td style="'.$tdStyle.'">'.e($c['nombre_cliente']).'</td>
                <td style="'.$tdStyle.' text-align:center;">'.e($c['documento_cliente']).'</td>
                <td style="'.$tdStyle.' text-align:center;">'.(int)$c['cantidad_reservas'].'</td>
                <td style="'.$tdStyle.' text-align:right;"><strong>'.money($c['total_ventas']).'</strong></td>
              </tr>';
        }
    }
    $htmlClientes .= '</tbody></table>';
    $pdf->writeHTML($htmlClientes, true, false, true, false, '');

    $pdf->Ln(2);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Write(6, 'Días con mayor movimiento');
    $pdf->Ln(7);
    $htmlDias = '<table width="100%" cellpadding="0" cellspacing="0"><thead><tr>
        <th style="'.$thStyle.'" width="40%">Día</th>
        <th style="'.$thStyle.'" width="25%">Reservas</th>
        <th style="'.$thStyle.'" width="35%">Total vendido</th>
      </tr></thead><tbody>';
    if (!$diasMovimiento) {
        $htmlDias .= '<tr><td colspan="3" style="'.$tdStyle.' text-align:center;">No hay movimiento para el periodo seleccionado.</td></tr>';
    } else {
        foreach ($diasMovimiento as $d) {
            $htmlDias .= '<tr>
                <td style="'.$tdStyle.'">'.e($d['nombre_dia']).'</td>
                <td style="'.$tdStyle.' text-align:center;">'.(int)$d['cantidad_reservas'].'</td>
                <td style="'.$tdStyle.' text-align:right;"><strong>'.money($d['total_ventas']).'</strong></td>
              </tr>';
        }
    }
    $htmlDias .= '</tbody></table>';
    $pdf->writeHTML($htmlDias, true, false, true, false, '');

    $pdf->Ln(2);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Write(6, 'Ventas por habitación');
    $pdf->Ln(7);
    $htmlHabitaciones = '<table width="100%" cellpadding="0" cellspacing="0"><thead><tr>
        <th style="'.$thStyle.'" width="20%">Habitación</th>
        <th style="'.$thStyle.'" width="30%">Tipo</th>
        <th style="'.$thStyle.'" width="25%">Cantidad</th>
        <th style="'.$thStyle.'" width="25%">Total</th>
      </tr></thead><tbody>';
    if (!$ventasHabitaciones) {
        $htmlHabitaciones .= '<tr><td colspan="4" style="'.$tdStyle.' text-align:center;">No hay ventas por habitación para el periodo seleccionado.</td></tr>';
    } else {
        foreach ($ventasHabitaciones as $h) {
            $htmlHabitaciones .= '<tr>
                <td style="'.$tdStyle.' text-align:center;">Hab. '.e($h['nombre_habitacion']).'</td>
                <td style="'.$tdStyle.'">'.e($h['nombre_tipo_habitacion']).'</td>
                <td style="'.$tdStyle.' text-align:center;">'.(int)$h['cantidad_reservas'].'</td>
                <td style="'.$tdStyle.' text-align:right;"><strong>'.money($h['total_ventas']).'</strong></td>
              </tr>';
        }
    }
    $htmlHabitaciones .= '</tbody></table>';
    $pdf->writeHTML($htmlHabitaciones, true, false, true, false, '');

    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Write(6, 'Comportamiento del periodo');
    $pdf->Ln(7);
    $htmlTiempo = '<table width="100%" cellpadding="0" cellspacing="0"><thead><tr>
        <th style="'.$thStyle.'" width="35%">Periodo</th>
        <th style="'.$thStyle.'" width="30%">Cantidad</th>
        <th style="'.$thStyle.'" width="35%">Total</th>
      </tr></thead><tbody>';
    if (!$ventasTiempo) {
        $htmlTiempo .= '<tr><td colspan="3" style="'.$tdStyle.' text-align:center;">No hay comportamiento para el periodo seleccionado.</td></tr>';
    } else {
        foreach ($ventasTiempo as $t) {
            $htmlTiempo .= '<tr>
                <td style="'.$tdStyle.' text-align:center;">'.e($t['etiqueta']).'</td>
                <td style="'.$tdStyle.' text-align:center;">'.(int)$t['cantidad_reservas'].'</td>
                <td style="'.$tdStyle.' text-align:right;"><strong>'.money($t['total_ventas']).'</strong></td>
              </tr>';
        }
    }
    $htmlTiempo .= '</tbody></table>';
    $pdf->writeHTML($htmlTiempo, true, false, true, false, '');

    $pdf->Ln(2);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Write(6, 'Detalle de ventas');
    $pdf->Ln(7);
    $htmlDetalle = '<table width="100%" cellpadding="0" cellspacing="0"><thead><tr>
        <th style="'.$thStyle.'" width="7%">ID</th>
        <th style="'.$thStyle.'" width="22%">Cliente</th>
        <th style="'.$thStyle.'" width="9%">Hab.</th>
        <th style="'.$thStyle.'" width="12%">Tarifa</th>
        <th style="'.$thStyle.'" width="18%">Llegada</th>
        <th style="'.$thStyle.'" width="14%">Método</th>
        <th style="'.$thStyle.'" width="18%">Monto</th>
      </tr></thead><tbody>';
    if (!$detalleVentas) {
        $htmlDetalle .= '<tr><td colspan="7" style="'.$tdStyle.' text-align:center;">No hay ventas para mostrar.</td></tr>';
    } else {
        foreach ($detalleVentas as $v) {
            $ts = strtotime((string)$v['fecha_llegada']);
            $llegada = $ts ? date('d-m-Y H:i', $ts) : '';
            $htmlDetalle .= '<tr>
                <td style="'.$tdStyle.' text-align:center;">'.(int)$v['id_reserva'].'</td>
                <td style="'.$tdStyle.'">'.e($v['nombre_cliente']).'</td>
                <td style="'.$tdStyle.' text-align:center;">'.e($v['nombre_habitacion']).'</td>
                <td style="'.$tdStyle.' text-align:center;">'.e($v['tipo_tarifa']).'</td>
                <td style="'.$tdStyle.' text-align:center;">'.e($llegada).'</td>
                <td style="'.$tdStyle.' text-align:center;">'.e($v['nombre_metodo_pago']).'</td>
                <td style="'.$tdStyle.' text-align:right;"><strong>'.money($v['monto_total']).'</strong></td>
              </tr>';
        }
    }
    $htmlDetalle .= '</tbody></table>';
    $pdf->writeHTML($htmlDetalle, true, false, true, false, '');

    bitacora_log($pdo, 'Estadísticas', 'exportar_pdf', [
        'filtro' => $tipoFiltro,
        'periodo' => $tituloPeriodo,
        'anio_comparacion' => $anioComparacion,
        'total_reservas' => $totalReservas,
        'monto' => round($totalVentas, 2),
        'top_clientes' => count($topClientes)
    ], 'OK');

    $nombreArchivo = 'estadistica_ventas_' . date('Ymd_His') . '.pdf';
    $pdf->Output($nombreArchivo, 'I');
    exit;
} catch (Throwable $e) {
    try {
        bitacora_log($pdo, 'Estadísticas', 'exportar_pdf', [
            'filtro' => $tipoFiltro,
            'fecha' => $fechaDia,
            'mes' => $fechaMes,
            'anio' => $fechaAnio,
            'ex' => $e->getMessage()
        ], 'ERROR');
    } catch (Throwable $ignored) {}

    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Error al generar el PDF de estadísticas.';
}
