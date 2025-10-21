<?php
// Conexión PDO
  require_once '../config/auth.php';
  require_admin();
  require_once '../config/db.php';
  include '../config/db.php';
  
$pdo = null;
$paths = [__DIR__ . '/../config/db.php', __DIR__ . '/../db.php'];
foreach ($paths as $p) { if (file_exists($p)) { require_once $p; break; } }

// Tipos para el otro tab (habitaciones) – este tab de tipos no los necesita, pero no estorba tenerlos
$tiposHabitacion = [];
if (isset($pdo) && $pdo) {
  $stmtTipos = $pdo->query('SELECT id_tipo_habitacion, nombre_tipo_habitacion, capacidad_tipo_habitacion FROM tipo_habitaciones ORDER BY id_tipo_habitacion');
  $tiposHabitacion = $stmtTipos->fetchAll(PDO::FETCH_ASSOC);
}

// Header
$headerPath = __DIR__ . '/header.php';
if (file_exists($headerPath)) include $headerPath;
?>

<div class="container mt-4">
  <h2 class="mb-3">Gestión de Habitaciones</h2>

  <ul class="nav nav-tabs" id="tabsGestion" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="habitaciones-tab" data-bs-toggle="tab" data-bs-target="#habitaciones" type="button" role="tab">Habitaciones</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tipos-tab" data-bs-toggle="tab" data-bs-target="#tipos" type="button" role="tab">Tipos de Habitaciones</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tarifas-tab" data-bs-toggle="tab" data-bs-target="#tarifas" type="button" role="tab">Tarifas</button>
    </li>
  </ul>

  <div class="tab-content mt-3">
    <div class="tab-pane fade show active" id="habitaciones" role="tabpanel">
      <?php include __DIR__ . '/partials/gestion_habitaciones_tab.php'; ?>
    </div>
    <div class="tab-pane fade" id="tipos" role="tabpanel">
      <?php include __DIR__ . '/partials/gestion_tipos_de_habitaciones_tab.php'; ?>
    </div>
    <div class="tab-pane fade" id="tarifas" role="tabpanel">
      <?php include __DIR__ . '/partials/gestion_tarifas_tab.php'; ?>
    </div>
    <div class="tab-pane fade" id="precios" role="tabpanel">
      <!-- Próximamente -->
    </div>
  </div>
</div>

<!-- Assets -->
<link rel="stylesheet" href="../public/css/datatables.min.css">
<link rel="stylesheet" href="../public/css/select2.min.css">
<script src="../public/js/jquery-3.7.1.min.js"></script>
<script src="../public/js/datatables.min.js"></script>
<script src="../public/js/sweetalert2.min.js"></script>
<script src="../public/js/select2.min.js"></script>
