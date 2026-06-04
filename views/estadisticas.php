<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

if (($_SESSION['rol'] ?? $_SESSION['rol_usuario'] ?? '') !== 'Administrador') {
    header('Location: recepcion.php?error=Acceso%20denegado');
    exit;
}

require_once '../config/db.php';
include '../views/header.php';

$tipoFiltro = $_GET['tipo'] ?? 'mes';
$tiposPermitidos = ['dia', 'mes', 'anio'];
if (!in_array($tipoFiltro, $tiposPermitidos, true)) {
    $tipoFiltro = 'mes';
}

$fechaDia = $_GET['fecha'] ?? date('Y-m-d');
$fechaMes = $_GET['mes'] ?? date('Y-m');
$fechaAnio = $_GET['anio'] ?? date('Y');

$where = ["r.estado_reserva <> 'Cancelada'"];
$params = [];
$tituloPeriodo = '';

switch ($tipoFiltro) {
    case 'dia':
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaDia)) {
            $fechaDia = date('Y-m-d');
        }
        $where[] = 'DATE(r.fecha_llegada) = :fecha_dia';
        $params[':fecha_dia'] = $fechaDia;
        $tituloPeriodo = date('d-m-Y', strtotime($fechaDia));
        break;

    case 'anio':
        if (!preg_match('/^\d{4}$/', $fechaAnio)) {
            $fechaAnio = date('Y');
        }
        $where[] = 'YEAR(r.fecha_llegada) = :fecha_anio';
        $params[':fecha_anio'] = (int)$fechaAnio;
        $tituloPeriodo = $fechaAnio;
        break;

    case 'mes':
    default:
        if (!preg_match('/^\d{4}-\d{2}$/', $fechaMes)) {
            $fechaMes = date('Y-m');
        }
        $where[] = "DATE_FORMAT(r.fecha_llegada, '%Y-%m') = :fecha_mes";
        $params[':fecha_mes'] = $fechaMes;
        $tituloPeriodo = date('m-Y', strtotime($fechaMes . '-01'));
        break;
}

$condiciones = 'WHERE ' . implode(' AND ', $where);

try {
    $stmtResumen = $pdo->prepare("\n        SELECT\n            COUNT(*) AS total_reservas,\n            COALESCE(SUM(r.monto_total), 0) AS total_ventas,\n            COALESCE(AVG(r.monto_total), 0) AS promedio_venta,\n            COUNT(DISTINCT r.id_habitacion) AS habitaciones_vendidas\n        FROM reservas r\n        $condiciones\n    ");
    $stmtResumen->execute($params);
    $resumen = $stmtResumen->fetch(PDO::FETCH_ASSOC) ?: [];

    $stmtHabitaciones = $pdo->prepare("\n        SELECT\n            h.id_habitacion,\n            h.nombre_habitacion,\n            th.nombre_tipo_habitacion,\n            COUNT(r.id_reserva) AS cantidad_reservas,\n            COALESCE(SUM(r.monto_total), 0) AS total_ventas\n        FROM reservas r\n        INNER JOIN habitaciones h ON r.id_habitacion = h.id_habitacion\n        INNER JOIN tipo_habitaciones th ON h.id_tipo_habitacion = th.id_tipo_habitacion\n        $condiciones\n        GROUP BY h.id_habitacion, h.nombre_habitacion, th.nombre_tipo_habitacion\n        ORDER BY total_ventas DESC, cantidad_reservas DESC, h.nombre_habitacion ASC\n    ");
    $stmtHabitaciones->execute($params);
    $ventasHabitaciones = $stmtHabitaciones->fetchAll(PDO::FETCH_ASSOC);

    $grupoTiempo = $tipoFiltro === 'dia'
        ? "DATE_FORMAT(r.fecha_llegada, '%H:00')"
        : ($tipoFiltro === 'anio' ? "DATE_FORMAT(r.fecha_llegada, '%m-%Y')" : "DATE_FORMAT(r.fecha_llegada, '%d-%m')");

    $ordenTiempo = $tipoFiltro === 'dia'
        ? 'HOUR(r.fecha_llegada)'
        : ($tipoFiltro === 'anio' ? 'MONTH(r.fecha_llegada)' : 'DAY(r.fecha_llegada)');

    $stmtTiempo = $pdo->prepare("\n        SELECT\n            $grupoTiempo AS etiqueta,\n            COUNT(r.id_reserva) AS cantidad_reservas,\n            COALESCE(SUM(r.monto_total), 0) AS total_ventas\n        FROM reservas r\n        $condiciones\n        GROUP BY etiqueta, $ordenTiempo\n        ORDER BY $ordenTiempo ASC\n    ");
    $stmtTiempo->execute($params);
    $ventasTiempo = $stmtTiempo->fetchAll(PDO::FETCH_ASSOC);

    $stmtDetalle = $pdo->prepare("\n        SELECT\n            r.id_reserva,\n            h.nombre_habitacion,\n            th.nombre_tipo_habitacion,\n            tf.tipo_tarifa,\n            r.fecha_llegada,\n            r.fecha_salida,\n            r.monto_total,\n            r.estado_reserva,\n            m.nombre_metodo_pago,\n            CONCAT(c.nombres_cliente, ' ', c.apellidos_cliente) AS nombre_cliente\n        FROM reservas r\n        INNER JOIN habitaciones h ON r.id_habitacion = h.id_habitacion\n        INNER JOIN tipo_habitaciones th ON h.id_tipo_habitacion = th.id_tipo_habitacion\n        INNER JOIN tarifas tf ON r.id_tarifa = tf.id_tarifa\n        INNER JOIN metodos_de_pago m ON r.id_metodo_pago = m.id_metodo_pago\n        INNER JOIN clientes c ON r.documento_cliente = c.documento_cliente\n        $condiciones\n        ORDER BY r.fecha_llegada DESC\n    ");
    $stmtDetalle->execute($params);
    $detalleVentas = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error al obtener las estadísticas: ' . $e->getMessage());
}

$totalVentas = (float)($resumen['total_ventas'] ?? 0);
$maxHabitacion = 0;
foreach ($ventasHabitaciones as $habitacion) {
    $maxHabitacion = max($maxHabitacion, (float)$habitacion['total_ventas']);
}

$maxTiempo = 0;
foreach ($ventasTiempo as $tiempo) {
    $maxTiempo = max($maxTiempo, (float)$tiempo['total_ventas']);
}
?>

<link rel="stylesheet" href="../public/css/datatables.min.css">
<link rel="stylesheet" href="../public/css/styletabla.css">

<style>
    .estadisticas-wrapper {
        background: rgba(255, 255, 255, .94);
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
    }

    .stat-card {
        border: 0;
        border-left: 5px solid #BA3B0A;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .10);
    }

    .stat-card .icono {
        width: 46px;
        height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #FFE6E0;
        color: #BA3B0A;
        font-size: 20px;
    }

    .chart-box {
        min-height: 340px;
        border: 1px solid #eee;
        border-radius: 16px;
        padding: 18px;
        background: #fff;
    }

    .bar-row {
        display: grid;
        grid-template-columns: 120px 1fr 100px;
        align-items: center;
        gap: 12px;
        margin-bottom: 13px;
    }

    .bar-track {
        width: 100%;
        height: 24px;
        background: #f1f1f1;
        border-radius: 999px;
        overflow: hidden;
    }

    .bar-fill {
        height: 100%;
        min-width: 3px;
        background: linear-gradient(90deg, #BA3B0A, #f58a4b);
        border-radius: 999px;
    }

    .vertical-chart {
        height: 250px;
        display: flex;
        align-items: end;
        gap: 12px;
        border-bottom: 1px solid #ddd;
        padding: 14px 8px 0;
        overflow-x: auto;
    }

    .vertical-item {
        min-width: 58px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: end;
        height: 100%;
    }

    .vertical-bar {
        width: 34px;
        min-height: 3px;
        border-radius: 10px 10px 0 0;
        background: linear-gradient(180deg, #BA3B0A, #f58a4b);
    }

    .vertical-label {
        margin-top: 8px;
        font-size: 12px;
        white-space: nowrap;
    }

    .empty-state {
        min-height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
    }

    @media print {
        nav, .no-print, .dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate {
            display: none !important;
        }

        body {
            background: #fff !important;
        }

        .estadisticas-wrapper {
            box-shadow: none !important;
            padding: 0 !important;
        }

        .chart-box, .stat-card {
            break-inside: avoid;
            box-shadow: none !important;
        }

        table {
            font-size: 11px;
            
        }
    }
</style>

<div class="container-fluid mt-4 mb-5">
    <div class="estadisticas-wrapper">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h2 class="mb-1"><i class="fas fa-chart-line me-2"></i>Estadística de Habitaciones Alquiladas</h2>
                <p class="text-muted mb-0">Ventas de habitaciones filtradas por día, mes o año. Periodo: <strong><?= htmlspecialchars($tituloPeriodo) ?></strong></p>
            </div>
            <form method="POST" action="../controllers/exportar_estadisticas_pdf.php" target="_blank" class="no-print m-0">
                <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipoFiltro) ?>">
                <input type="hidden" name="fecha" value="<?= htmlspecialchars($fechaDia) ?>">
                <input type="hidden" name="mes" value="<?= htmlspecialchars($fechaMes) ?>">
                <input type="hidden" name="anio" value="<?= htmlspecialchars($fechaAnio) ?>">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-file-pdf me-1"></i> Imprimir PDF
                </button>
            </form>
        </div>

        <form method="GET" action="estadisticas.php" class="row g-3 align-items-end mb-4 no-print">
            <div class="col-md-3">
                <label for="tipo" class="form-label">Filtrar por:</label>
                <select name="tipo" id="tipo" class="form-select">
                    <option value="dia" <?= $tipoFiltro === 'dia' ? 'selected' : '' ?>>Día</option>
                    <option value="mes" <?= $tipoFiltro === 'mes' ? 'selected' : '' ?>>Mes</option>
                    <option value="anio" <?= $tipoFiltro === 'anio' ? 'selected' : '' ?>>Año</option>
                </select>
            </div>
            <div class="col-md-3 filtro-campo filtro-dia">
                <label for="fecha" class="form-label">Día:</label>
                <input type="date" name="fecha" id="fecha" class="form-control" value="<?= htmlspecialchars($fechaDia) ?>">
            </div>
            <div class="col-md-3 filtro-campo filtro-mes">
                <label for="mes" class="form-label">Mes:</label>
                <input type="month" name="mes" id="mes" class="form-control" value="<?= htmlspecialchars($fechaMes) ?>">
            </div>
            <div class="col-md-3 filtro-campo filtro-anio">
                <label for="anio" class="form-label">Año:</label>
                <input type="number" name="anio" id="anio" class="form-control" min="2000" max="2100" value="<?= htmlspecialchars($fechaAnio) ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filtrar
                </button>
                <a href="estadisticas.php" class="btn btn-secondary w-100">Restablecer</a>
            </div>
        </form>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total ventas</p>
                            <h4 class="mb-0">$<?= number_format($totalVentas, 2) ?></h4>
                        </div>
                        <span class="icono"><i class="fas fa-dollar-sign"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Reservas</p>
                            <h4 class="mb-0"><?= (int)($resumen['total_reservas'] ?? 0) ?></h4>
                        </div>
                        <span class="icono"><i class="fas fa-calendar-check"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Promedio</p>
                            <h4 class="mb-0">$<?= number_format((float)($resumen['promedio_venta'] ?? 0), 2) ?></h4>
                        </div>
                        <span class="icono"><i class="fas fa-chart-simple"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Habitaciones Alquiladas</p>
                            <h4 class="mb-0"><?= (int)($resumen['habitaciones_vendidas'] ?? 0) ?></h4>
                        </div>
                        <span class="icono"><i class="fas fa-bed"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="chart-box">
                    <h5 class="mb-3"><i class="fas fa-bed me-2"></i>Ventas por habitación</h5>
                    <?php if (empty($ventasHabitaciones)): ?>
                        <div class="empty-state">No hay ventas registradas para el periodo seleccionado.</div>
                    <?php else: ?>
                        <?php foreach ($ventasHabitaciones as $habitacion):
                            $valor = (float)$habitacion['total_ventas'];
                            $porcentaje = $maxHabitacion > 0 ? max(2, ($valor / $maxHabitacion) * 100) : 0;
                        ?>
                            <div class="bar-row">
                                <div>
                                    <strong>Hab. <?= htmlspecialchars($habitacion['nombre_habitacion']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($habitacion['nombre_tipo_habitacion']) ?></small>
                                </div>
                                <div class="bar-track" title="$<?= number_format($valor, 2) ?>">
                                    <div class="bar-fill" style="width: <?= number_format($porcentaje, 2, '.', '') ?>%;"></div>
                                </div>
                                <div class="text-end">
                                    <strong>$<?= number_format($valor, 2) ?></strong><br>
                                    <small><?= (int)$habitacion['cantidad_reservas'] ?> venta(s)</small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-box">
                    <h5 class="mb-3"><i class="fas fa-chart-column me-2"></i>Comportamiento del periodo</h5>
                    <?php if (empty($ventasTiempo)): ?>
                        <div class="empty-state">No hay datos para graficar en el periodo seleccionado.</div>
                    <?php else: ?>
                        <div class="vertical-chart">
                            <?php foreach ($ventasTiempo as $tiempo):
                                $valor = (float)$tiempo['total_ventas'];
                                $alto = $maxTiempo > 0 ? max(4, ($valor / $maxTiempo) * 210) : 0;
                            ?>
                                <div class="vertical-item" title="$<?= number_format($valor, 2) ?> / <?= (int)$tiempo['cantidad_reservas'] ?> venta(s)">
                                    <small>$<?= number_format($valor, 0) ?></small>
                                    <div class="vertical-bar" style="height: <?= number_format($alto, 2, '.', '') ?>px;"></div>
                                    <span class="vertical-label"><?= htmlspecialchars($tiempo['etiqueta']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <h5 class="mb-3"><i class="fas fa-table me-2"></i>Detalle de ventas</h5>
            <table id="tablaEstadisticas" class="table table-bordered table-hover table-striped" data-page-length="25">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Hab.</th>
                        <th>Tipo Hab.</th>
                        <th>Tarifa</th>
                        <th>Llegada</th>
                        <th>Salida</th>
                        <th>Método</th>
                        <th>Estado</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalleVentas as $venta):
                        $tsLlegada = strtotime($venta['fecha_llegada']);
                        $tsSalida = strtotime($venta['fecha_salida']);
                    ?>
                        <tr>
                            <td><?= (int)$venta['id_reserva'] ?></td>
                            <td><?= htmlspecialchars($venta['nombre_cliente']) ?></td>
                            <td><?= htmlspecialchars($venta['nombre_habitacion']) ?></td>
                            <td><?= htmlspecialchars($venta['nombre_tipo_habitacion']) ?></td>
                            <td><?= htmlspecialchars($venta['tipo_tarifa']) ?></td>
                            <td data-order="<?= date('Y-m-d H:i:s', $tsLlegada) ?>"><?= date('d-m-Y h:i A', $tsLlegada) ?></td>
                            <td data-order="<?= date('Y-m-d H:i:s', $tsSalida) ?>"><?= date('d-m-Y h:i A', $tsSalida) ?></td>
                            <td><?= htmlspecialchars($venta['nombre_metodo_pago']) ?></td>
                            <td><?= htmlspecialchars($venta['estado_reserva']) ?></td>
                            <td>$<?= number_format((float)$venta['monto_total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../public/js/jquery-3.7.1.min.js"></script>
<script src="../public/js/datatables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#tablaEstadisticas').DataTable({
            language: { url: '../public/js/es-ES.json' },
            responsive: true,
            order: [[5, 'desc']]
        });

        function mostrarCampoFiltro() {
            const tipo = $('#tipo').val();
            $('.filtro-campo').hide();
            $('.filtro-' + tipo).show();
        }

        $('#tipo').on('change', mostrarCampoFiltro);
        mostrarCampoFiltro();
    });
</script>
