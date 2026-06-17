<?php
// controllers/exportar_pdf.php
declare(strict_types=1);

// Silenciar notices en producción (TCPDF es sensible a cualquier salida previa)
ini_set('display_errors', '0');
error_reporting(E_ALL);

session_start();

require_once '../config/db.php';               // $pdo
require_once '../config/reservas_conversion.php'; // Columnas de conversión Bs
require_once '../config/bitacora.php';         // Bitácora
require_once '../public/tcpdf/tcpdf.php';      // TCPDF

// ---------- ZONA HORARIA ----------
date_default_timezone_set('America/Caracas');

// ---------- USUARIO DE SESIÓN PARA FOOTER ----------
$nombre   = $_SESSION['nombre_usuario']   ?? '';
$apellido = $_SESSION['apellido_usuario'] ?? '';
$rol      = $_SESSION['rol_usuario']      ?? '';
$correo   = $_SESSION['correo_usuario']   ?? '';

$exportUser     = trim($nombre . ' ' . $apellido);
if ($exportUser === '') {
  $exportUser = $correo ?: 'Usuario';
}
$exportRole     = $rol ?: 'Sin rol';
$exportDatetime = date('d-m-Y H:i:s');

// ---------- LECTURA DE FILTROS ----------
$desde  = $_POST['desde']  ?? ($_GET['desde']  ?? '');
$hasta  = $_POST['hasta']  ?? ($_GET['hasta']  ?? '');
$estado = $_POST['estado'] ?? ($_GET['estado'] ?? '');

$permitidos = ['Confirmada', 'Finalizada'];

$where  = [];
$params = [];

// Filtrado por fecha usando fecha_llegada
if ($desde !== '') {
  $where[] = "DATE(r.fecha_llegada) >= :desde";
  $params[':desde'] = $desde;
}
if ($hasta !== '') {
  $where[] = "DATE(r.fecha_llegada) <= :hasta";
  $params[':hasta'] = $hasta;
}

// Estado correcto es 'estado_reserva'
if ($estado !== '' && in_array($estado, $permitidos, true)) {
  $where[] = "r.estado_reserva = :estado";
  $params[':estado'] = $estado;
}

// ---------- CONSULTA ----------
$tieneColumnasConversion = reservas_tiene_columnas_conversion($pdo);
$selectConversion = $tieneColumnasConversion
  ? "r.monto_total_bs, r.tasa_conversion, r.modo_tasa_conversion,"
  : "NULL AS monto_total_bs, NULL AS tasa_conversion, NULL AS modo_tasa_conversion,";

$sql = "
  SELECT
    r.id_reserva,
    r.fecha_llegada,
    r.fecha_salida,
    r.cantidad_personas,
    r.monto_total,
    $selectConversion
    r.estado_reserva,
    r.origen_reserva,
    m.nombre_metodo_pago,
    h.nombre_habitacion,
    c.nombres_cliente,
    c.apellidos_cliente
  FROM reservas r
  JOIN metodos_de_pago m ON m.id_metodo_pago = r.id_metodo_pago
  JOIN habitaciones   h  ON h.id_habitacion = r.id_habitacion
  JOIN clientes       c  ON c.documento_cliente = r.documento_cliente
";
if ($where) {
  $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY r.fecha_llegada DESC";

// ---------- TCPDF EXTENDIDA ----------
class PDF extends TCPDF {
  public $brandRGB = [186, 59, 10];
  public $logoPath = '../public/img/LogoPosada.jpg';
  public $titulo   = 'Reporte de Reservas - Posada Las Mandarinas';

  // Variables recibidas para el footer:
  public $exportUser = 'Usuario';
  public $exportRole = 'Sin rol';
  public $exportDatetime = '';

  public function Header() {
    // Logo
    if (is_file($this->logoPath)) {
      $this->Image($this->logoPath, 10, 8, 24);
      $this->SetFont('helvetica', 'B', 7.5);
      $this->SetTextColor(80, 80, 80);
      $this->SetXY(10, 19);
      $this->Cell(24, 4, 'RIF: V-13577225-5', 0, 0, 'C');
    }
    // Título
    $this->SetXY(10, 9);
    $this->SetFont('helvetica', 'B', 13);
    $this->SetTextColor(0, 0, 0);
    $this->Cell(0, 7, $this->titulo, 0, 1, 'C');

    // Línea de color corporativo
    $this->SetDrawColor($this->brandRGB[0], $this->brandRGB[1], $this->brandRGB[2]);
    $this->SetLineWidth(0.8);
    $this->Line(10, 26, $this->getPageWidth() - 10, 26);
    $this->Ln(7);
  }

  public function Footer() {
    $this->SetY(-18);
    $this->SetFont('helvetica', '', 8);
    $this->SetTextColor(120, 120, 120);
    // Línea 1: paginación
    $this->Cell(0, 5, 'Generado por Sistema Web - Posada Las Mandarinas, Ejido, Estado Mérida | Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, 1, 'C');
    // Línea 2: fecha/hora y usuario
    $this->SetFont('helvetica', '', 7.8);
    $this->Cell(0, 5, 'Fecha y hora: '.$this->exportDatetime.'  |  Usuario: '.$this->exportUser.'  ('.$this->exportRole.')', 0, 0, 'C');
  }
}

try {
  // Ejecutar consulta
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // ---------- TOTALES ----------
  $totalRegistros = count($rows);
  $totalMonto = 0.00;
  $totalMontoBs = 0.00;
  $tieneMontoBs = false;
  foreach ($rows as $r) {
    $totalMonto += (float)$r['monto_total'];
    if ($r['monto_total_bs'] !== null) {
      $totalMontoBs += (float)$r['monto_total_bs'];
      $tieneMontoBs = true;
    }
  }

  // ---------- CONFIG TCPDF ----------
  $pdf = new PDF('P', 'mm', 'A4');
  $pdf->SetCreator('Sistema Web Posada Las Mandarinas');
  $pdf->SetAuthor('Posada Las Mandarinas');
  $pdf->SetTitle('Reporte de Reservas');
  $pdf->SetMargins(8, 32, 8);
  $pdf->SetAutoPageBreak(true, 18);

  // Pasar variables de footer a la instancia
  $pdf->exportUser     = $exportUser;
  $pdf->exportRole     = $exportRole;
  $pdf->exportDatetime = $exportDatetime;

  $pdf->AddPage();

  // Colores corporativos
  list($brandR, $brandG, $brandB) = [186, 59, 10];

  // ---------- CABECERA DE FILTROS ----------
  $chip = function(string $label, string $value) use ($brandR, $brandG, $brandB) {
    if ($value === '') return '';
    $valueEsc = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    $labelEsc = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
    return '
      <span style="
        display:inline-block;
        border:1px solid rgb('.$brandR.','.$brandG.','.$brandB.');
        color:rgb('.$brandR.','.$brandG.','.$brandB.');
        border-radius:12px;
        padding:2px 8px;
        font-size:10px;
        margin-right:6px;
        margin-bottom:4px;">
        <strong>'.$labelEsc.':</strong> '.$valueEsc.'
      </span>
    ';
  };

  // Subtítulo visible (antes estaba indefinido)
  $subtitulo = 'Reservas';
  if ($estado !== '' && in_array($estado, $permitidos, true)) {
    $subtitulo .= ' - Estado: '.$estado;
  }

  // Chips de fecha
  $chipsHtml = $chip('Desde', $desde) . $chip('Hasta', $hasta);

  $pdf->SetFont('helvetica', 'B', 11);
  $pdf->Write(6, $subtitulo);
  $pdf->Ln(7);

  $pdf->SetFont('helvetica', '', 10);
  $pdf->writeHTML('<div>'.$chipsHtml.'</div>', true, false, true, false, '');

  // ---------- TABLA (SIN #, SIN Estado, SIN Origen) ----------
  $theadBg = 'background-color:#ba3b0a; color:#ffffff;';
  $thStyle = 'padding:4px; font-size:7.6px; '.$theadBg.' text-align:center;';
  $tdBaseStyle = 'padding:3.5px; font-size:7.4px; border-bottom:1px solid #dddddd;';

  $html = '
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <thead>
      <tr>
        <th width="8%" style="'.$thStyle.'">Hab.</th>
        <th width="21%" style="'.$thStyle.'">Cliente</th>
        <th width="15%" style="'.$thStyle.'">Llegada</th>
        <th width="15%" style="'.$thStyle.'">Salida</th>
        <th width="8%" style="'.$thStyle.'">Pers.</th>
        <th width="12%" style="'.$thStyle.'">Método</th>
        <th width="10%" style="'.$thStyle.'">Monto Total</th>
        <th width="11%" style="'.$thStyle.'">Monto Total Bs.</th>
      </tr>
    </thead>
    <tbody>
  ';

  if (!$rows) {
    $html .= '
      <tr>
        <td colspan="8" style="padding:10px; text-align:center; font-size:10px;">
          No hay resultados para los filtros seleccionados.
        </td>
      </tr>
    ';
  } else {
    foreach ($rows as $i => $r) {
      $cli = htmlspecialchars(trim(($r['nombres_cliente'] ?? '').' '.($r['apellidos_cliente'] ?? '')), ENT_QUOTES, 'UTF-8');
      $hab = htmlspecialchars($r['nombre_habitacion'] ?? '', ENT_QUOTES, 'UTF-8');
      $met = htmlspecialchars($r['nombre_metodo_pago'] ?? '', ENT_QUOTES, 'UTF-8');

      $tsL = strtotime($r['fecha_llegada'] ?? '');
      $tsS = strtotime($r['fecha_salida'] ?? '');
      $llegada = $tsL ? date('d-m-Y H:i', $tsL) : '';
      $salida  = $tsS ? date('d-m-Y H:i', $tsS) : '';

      $cant  = (int)($r['cantidad_personas'] ?? 0);
      $monto = number_format((float)($r['monto_total'] ?? 0), 2, ',', '.');
      $montoBs = $r['monto_total_bs'] !== null
        ? 'Bs. ' . number_format((float)$r['monto_total_bs'], 2, ',', '.')
        : '—';
      $rowBg = ($i % 2 === 0) ? '#ffffff' : '#f4f4f4';
      $tdStyle = $tdBaseStyle . ' background-color:'.$rowBg.';';

      $html .= '
        <tr>
          <td width="8%" style="'.$tdStyle.' text-align:center;">'.$hab.'</td>
          <td width="21%" style="'.$tdStyle.'">'.$cli.'</td>
          <td width="15%" style="'.$tdStyle.' text-align:center;">'.$llegada.'</td>
          <td width="15%" style="'.$tdStyle.' text-align:center;">'.$salida.'</td>
          <td width="8%" style="'.$tdStyle.' text-align:center;">'.$cant.'</td>
          <td width="12%" style="'.$tdStyle.' text-align:center;">'.$met.'</td>
          <td width="10%" style="'.$tdStyle.' text-align:right;"><strong>$ '.$monto.'</strong></td>
          <td width="11%" style="'.$tdStyle.' text-align:right;"><strong>'.$montoBs.'</strong></td>
        </tr>
      ';
    }
  }

  $html .= '
    </tbody>
  </table>
  ';

  $pdf->Ln(2);
  $pdf->writeHTML($html, true, false, true, false, '');

  // ---------- RESUMEN ----------
  $pdf->Ln(3);
  $badgeStyle = '
    display:inline-block;
    padding:5px 10px;
    border-radius:6px;
    border:1px solid #ba3b0a;
    color:#ba3b0a;
    font-size:10px;
    margin-right:8px;
  ';
  $resumenHtml = '
    <div>
      <span style="'.$badgeStyle.'"><strong>Total de reservas:</strong> '.$totalRegistros.'</span>
      <span style="'.$badgeStyle.'"><strong>Acumulado USD:</strong> $ '.number_format($totalMonto, 2, ',', '.').'</span>
      <span style="'.$badgeStyle.'"><strong>Acumulado Bs:</strong> '.($tieneMontoBs ? 'Bs. '.number_format($totalMontoBs, 2, ',', '.') : '—').'</span>
    </div>
  ';
  $pdf->writeHTML($resumenHtml, true, false, true, false, '');

  // ---------- BITÁCORA (OK) ----------
  bitacora_log($pdo, 'Reportes', 'exportar', [
    'seccion' => 'reservas',
    'filtros' => ['desde' => $desde, 'hasta' => $hasta, 'estado' => $estado],
    'total'    => $totalRegistros,
    'monto'    => round($totalMonto, 2),
    'monto_bs' => $tieneMontoBs ? round($totalMontoBs, 2) : null
  ], 'OK');

  // ---------- SALIDA ----------
  $filename = 'Reporte_Reservas_' . date('Ymd_His') . '.pdf';
  // 'I' muestra en el navegador; 'D' fuerza descarga
  $pdf->Output($filename, 'I');

} catch (Throwable $e) {
  // ---------- BITÁCORA (ERROR) ----------
  try {
    bitacora_log($pdo, 'Reportes', 'exportar', [
      'seccion' => 'reservas',
      'filtros' => ['desde' => $desde, 'hasta' => $hasta, 'estado' => $estado],
      'ex'      => $e->getMessage()
    ], 'ERROR');
  } catch (Throwable $ignored) {
    // no romper por bitácora
  }

  http_response_code(500);
  header('Content-Type: text/plain; charset=utf-8');
  echo 'Error al generar el PDF.';
}
