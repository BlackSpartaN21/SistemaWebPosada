<?php
// controllers/exportar_estadisticas_pdf.php
declare(strict_types=1);

// TCPDF no debe recibir salida previa
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
require_once '../public/tcpdf/tcpdf.php';

// La zona horaria debe coincidir con el PDF de reportes
date_default_timezone_set('America/Caracas');

// ---------- USUARIO DE SESIÓN PARA FOOTER ----------
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

// ---------- LECTURA DE FILTROS ----------
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

switch ($tipoFiltro) {
  case 'dia':
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaDia)) {
      $fechaDia = date('Y-m-d');
    }
    $where[] = 'DATE(r.fecha_llegada) = :fecha_dia';
    $params[':fecha_dia'] = $fechaDia;
    $tituloPeriodo = date('d-m-Y', strtotime($fechaDia));
    $subtituloFiltro = 'Día: ' . $tituloPeriodo;
    break;

  case 'anio':
    if (!preg_match('/^\d{4}$/', $fechaAnio)) {
      $fechaAnio = date('Y');
    }
    $where[] = 'YEAR(r.fecha_llegada) = :fecha_anio';
    $params[':fecha_anio'] = (int)$fechaAnio;
    $tituloPeriodo = $fechaAnio;
    $subtituloFiltro = 'Año: ' . $tituloPeriodo;
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
    break;
}

$condiciones = 'WHERE ' . implode(' AND ', $where);

// ---------- TCPDF EXTENDIDA: MISMO ENCABEZADO Y PIE QUE REPORTES.PHP ----------
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

try {
  // ---------- CONSULTAS ----------
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

  $grupoTiempo = $tipoFiltro === 'dia'
    ? "DATE_FORMAT(r.fecha_llegada, '%H:00')"
    : ($tipoFiltro === 'anio' ? "DATE_FORMAT(r.fecha_llegada, '%m-%Y')" : "DATE_FORMAT(r.fecha_llegada, '%d-%m')");

  $ordenTiempo = $tipoFiltro === 'dia'
    ? 'HOUR(r.fecha_llegada)'
    : ($tipoFiltro === 'anio' ? 'MONTH(r.fecha_llegada)' : 'DAY(r.fecha_llegada)');

  $stmtTiempo = $pdo->prepare("
    SELECT
      $grupoTiempo AS etiqueta,
      COUNT(r.id_reserva) AS cantidad_reservas,
      COALESCE(SUM(r.monto_total), 0) AS total_ventas
    FROM reservas r
    $condiciones
    GROUP BY etiqueta, $ordenTiempo
    ORDER BY $ordenTiempo ASC
  ");
  $stmtTiempo->execute($params);
  $ventasTiempo = $stmtTiempo->fetchAll(PDO::FETCH_ASSOC);

  $stmtDetalle = $pdo->prepare("
    SELECT
      r.id_reserva,
      h.nombre_habitacion,
      th.nombre_tipo_habitacion,
      tf.tipo_tarifa,
      r.fecha_llegada,
      r.fecha_salida,
      r.monto_total,
      r.estado_reserva,
      m.nombre_metodo_pago,
      CONCAT(c.nombres_cliente, ' ', c.apellidos_cliente) AS nombre_cliente
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

  // ---------- CONFIG PDF ----------
  $pdf = new PDF('P', 'mm', 'A4');
  $pdf->SetCreator('Sistema Web Posada Las Mandarinas');
  $pdf->SetAuthor('Posada Las Mandarinas');
  $pdf->SetTitle('Estadística de Ventas');
  $pdf->SetMargins(10, 28, 10);
  $pdf->SetAutoPageBreak(true, 18);

  $pdf->exportUser     = $exportUser;
  $pdf->exportRole     = $exportRole;
  $pdf->exportDatetime = $exportDatetime;

  $pdf->AddPage();

  $brand = '#ba3b0a';
  $totalVentas = (float)($resumen['total_ventas'] ?? 0);
  $totalReservas = (int)($resumen['total_reservas'] ?? 0);
  $promedioVenta = (float)($resumen['promedio_venta'] ?? 0);
  $habitacionesVendidas = (int)($resumen['habitaciones_vendidas'] ?? 0);

  $pdf->SetFont('helvetica', 'B', 11);
  $pdf->Write(6, 'Estadística de ventas de habitaciones');
  $pdf->Ln(7);

  $filtroEsc = htmlspecialchars($subtituloFiltro, ENT_QUOTES, 'UTF-8');
  $pdf->SetFont('helvetica', '', 10);
  $pdf->writeHTML('<span style="border:1px solid '.$brand.'; color:'.$brand.'; border-radius:12px; padding:3px 8px; font-size:10px;"><strong>Filtro aplicado:</strong> '.$filtroEsc.'</span>', true, false, true, false, '');

  // ---------- TARJETAS DE RESUMEN ----------
  $cardStyle = 'border:1px solid #dddddd; padding:7px; font-size:10px;';
  $cardTitle = 'color:#666666; font-size:9px;';
  $cardValue = 'color:#000000; font-size:14px; font-weight:bold;';

  $htmlResumen = '
    <table width="100%" cellpadding="5" cellspacing="0">
      <tr>
        <td width="25%" style="'.$cardStyle.'"><span style="'.$cardTitle.'">Total ventas</span><br><span style="'.$cardValue.'">$ '.number_format($totalVentas, 2, ',', '.').'</span></td>
        <td width="25%" style="'.$cardStyle.'"><span style="'.$cardTitle.'">Reservas</span><br><span style="'.$cardValue.'">'.$totalReservas.'</span></td>
        <td width="25%" style="'.$cardStyle.'"><span style="'.$cardTitle.'">Promedio</span><br><span style="'.$cardValue.'">$ '.number_format($promedioVenta, 2, ',', '.').'</span></td>
        <td width="25%" style="'.$cardStyle.'"><span style="'.$cardTitle.'">Habitaciones alquiladas</span><br><span style="'.$cardValue.'">'.$habitacionesVendidas.'</span></td>
      </tr>
    </table>
  ';
  $pdf->Ln(2);
  $pdf->writeHTML($htmlResumen, true, false, true, false, '');

  // ---------- TABLA VENTAS POR HABITACIÓN ----------
  $pdf->Ln(2);
  $pdf->SetFont('helvetica', 'B', 11);
  $pdf->Write(6, 'Ventas por habitación');
  $pdf->Ln(7);

  $thStyle = 'padding:6px; font-size:10px; background-color:'.$brand.'; color:#ffffff; text-align:center;';
  $tdStyle = 'padding:5px; font-size:9px; border-bottom:1px solid #dddddd;';

  $htmlHabitaciones = '
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <thead>
        <tr>
          <th style="'.$thStyle.'" width="22%">Habitación</th>
          <th style="'.$thStyle.'" width="28%">Tipo</th>
          <th style="'.$thStyle.'" width="25%">Cantidad ventas</th>
          <th style="'.$thStyle.'" width="25%">Total vendido</th>
        </tr>
      </thead>
      <tbody>
  ';

  if (!$ventasHabitaciones) {
    $htmlHabitaciones .= '<tr><td colspan="4" style="'.$tdStyle.' text-align:center;">No hay ventas registradas para el periodo seleccionado.</td></tr>';
  } else {
    foreach ($ventasHabitaciones as $h) {
      $hab = htmlspecialchars((string)$h['nombre_habitacion'], ENT_QUOTES, 'UTF-8');
      $tipo = htmlspecialchars((string)$h['nombre_tipo_habitacion'], ENT_QUOTES, 'UTF-8');
      $cant = (int)$h['cantidad_reservas'];
      $total = number_format((float)$h['total_ventas'], 2, ',', '.');
      $htmlHabitaciones .= '
        <tr>
          <td style="'.$tdStyle.' text-align:center;">Hab. '.$hab.'</td>
          <td style="'.$tdStyle.'">'.$tipo.'</td>
          <td style="'.$tdStyle.' text-align:center;">'.$cant.'</td>
          <td style="'.$tdStyle.' text-align:right;"><strong>$ '.$total.'</strong></td>
        </tr>
      ';
    }
  }
  $htmlHabitaciones .= '</tbody></table>';
  $pdf->writeHTML($htmlHabitaciones, true, false, true, false, '');

  // ---------- COMPORTAMIENTO DEL PERIODO ----------
  $pdf->Ln(2);
  $pdf->SetFont('helvetica', 'B', 11);
  $pdf->Write(6, 'Comportamiento del periodo');
  $pdf->Ln(7);

  $htmlTiempo = '
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <thead>
        <tr>
          <th style="'.$thStyle.'" width="35%">Periodo</th>
          <th style="'.$thStyle.'" width="30%">Cantidad ventas</th>
          <th style="'.$thStyle.'" width="35%">Total vendido</th>
        </tr>
      </thead>
      <tbody>
  ';

  if (!$ventasTiempo) {
    $htmlTiempo .= '<tr><td colspan="3" style="'.$tdStyle.' text-align:center;">No hay datos para el periodo seleccionado.</td></tr>';
  } else {
    foreach ($ventasTiempo as $t) {
      $etiqueta = htmlspecialchars((string)$t['etiqueta'], ENT_QUOTES, 'UTF-8');
      $cant = (int)$t['cantidad_reservas'];
      $total = number_format((float)$t['total_ventas'], 2, ',', '.');
      $htmlTiempo .= '
        <tr>
          <td style="'.$tdStyle.' text-align:center;">'.$etiqueta.'</td>
          <td style="'.$tdStyle.' text-align:center;">'.$cant.'</td>
          <td style="'.$tdStyle.' text-align:right;"><strong>$ '.$total.'</strong></td>
        </tr>
      ';
    }
  }
  $htmlTiempo .= '</tbody></table>';
  $pdf->writeHTML($htmlTiempo, true, false, true, false, '');

  // ---------- DETALLE DE VENTAS ----------
  $pdf->AddPage();
  $pdf->SetFont('helvetica', 'B', 11);
  $pdf->Write(6, 'Detalle de ventas');
  $pdf->Ln(7);

  $htmlDetalle = '
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <thead>
        <tr>
          <th style="'.$thStyle.'" width="8%">ID</th>
          <th style="'.$thStyle.'" width="22%">Cliente</th>
          <th style="'.$thStyle.'" width="10%">Hab.</th>
          <th style="'.$thStyle.'" width="13%">Tarifa</th>
          <th style="'.$thStyle.'" width="18%">Llegada</th>
          <th style="'.$thStyle.'" width="13%">Método</th>
          <th style="'.$thStyle.'" width="16%">Monto</th>
        </tr>
      </thead>
      <tbody>
  ';

  if (!$detalleVentas) {
    $htmlDetalle .= '<tr><td colspan="7" style="'.$tdStyle.' text-align:center;">No hay ventas para mostrar.</td></tr>';
  } else {
    foreach ($detalleVentas as $v) {
      $id = (int)$v['id_reserva'];
      $cliente = htmlspecialchars((string)$v['nombre_cliente'], ENT_QUOTES, 'UTF-8');
      $hab = htmlspecialchars((string)$v['nombre_habitacion'], ENT_QUOTES, 'UTF-8');
      $tarifa = htmlspecialchars((string)$v['tipo_tarifa'], ENT_QUOTES, 'UTF-8');
      $metodo = htmlspecialchars((string)$v['nombre_metodo_pago'], ENT_QUOTES, 'UTF-8');
      $ts = strtotime((string)$v['fecha_llegada']);
      $llegada = $ts ? date('d-m-Y H:i', $ts) : '';
      $monto = number_format((float)$v['monto_total'], 2, ',', '.');

      $htmlDetalle .= '
        <tr>
          <td style="'.$tdStyle.' text-align:center;">'.$id.'</td>
          <td style="'.$tdStyle.'">'.$cliente.'</td>
          <td style="'.$tdStyle.' text-align:center;">'.$hab.'</td>
          <td style="'.$tdStyle.' text-align:center;">'.$tarifa.'</td>
          <td style="'.$tdStyle.' text-align:center;">'.$llegada.'</td>
          <td style="'.$tdStyle.' text-align:center;">'.$metodo.'</td>
          <td style="'.$tdStyle.' text-align:right;"><strong>$ '.$monto.'</strong></td>
        </tr>
      ';
    }
  }

  $htmlDetalle .= '</tbody></table>';
  $pdf->writeHTML($htmlDetalle, true, false, true, false, '');

  $nombreArchivo = 'estadistica_ventas_' . date('Ymd_His') . '.pdf';
  $pdf->Output($nombreArchivo, 'I');
  exit;

} catch (Throwable $e) {
  http_response_code(500);
  echo 'Error al generar el PDF de estadísticas.';
}
