<?php
include '../config/db.php';
include '../views/header.php';

// Tipos de habitación
$stmtTipos = $pdo->query("SELECT * FROM tipo_habitaciones");
$tiposHabitacion = $stmtTipos->fetchAll(PDO::FETCH_ASSOC);

// Habitaciones
$stmtHabitaciones = $pdo->query("
  SELECT h.*, t.nombre_tipo_habitacion
  FROM habitaciones h
  JOIN tipo_habitaciones t ON h.id_tipo_habitacion = t.id_tipo_habitacion
  ORDER BY h.id_habitacion ASC
");
$habitaciones = $stmtHabitaciones->fetchAll(PDO::FETCH_ASSOC);

// Tarifas
$stmtTarifas = $pdo->query("
  SELECT ta.*, th.nombre_tipo_habitacion
  FROM tarifas ta
  JOIN tipo_habitaciones th ON ta.id_tipo_habitacion = th.id_tipo_habitacion
");
$tarifas = $stmtTarifas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Habitaciones</title>
  <link href="../public/css/sweetalert2.min.css" rel="stylesheet">
  <link href="../public/css/select2.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../public/css/datatables.min.css">

</head>
<body>
<div class="container mt-5">
  <h2 class="text-center mb-4">Gestión de Habitaciones, Tarifas y Precios</h2>

  <!-- Navegación de pestañas -->
  <ul class="nav nav-tabs" id="tabsGestion" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="habitaciones-tab" data-bs-toggle="tab" data-bs-target="#habitaciones" type="button" role="tab">Habitaciones</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tarifas-tab" data-bs-toggle="tab" data-bs-target="#tarifas" type="button" role="tab">Tarifas</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="precios-tab" data-bs-toggle="tab" data-bs-target="#precios" type="button" role="tab">Precios</button>
    </li>
  </ul>

  <!-- Contenido de las pestañas -->
  <div class="tab-content mt-4" id="tabsContentGestion">
    <div class="tab-pane fade show active" id="habitaciones" role="tabpanel">
      <?php include 'partials/gestion_habitaciones_tab.php'; ?>
    </div>
    <div class="tab-pane fade" id="tarifas" role="tabpanel">
      <?php include 'partials/gestion_tarifas_tab.php'; ?>
    </div>
    <div class="tab-pane fade" id="precios" role="tabpanel">
      <?php include 'partials/gestion_precios_tab.php'; ?>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="../public/js/jquery-3.7.1.min.js"></script>
<script src="../public/js/sweetalert2.min.js"></script>
<script src="../public/js/select2.min.js"></script>
<script src="../public/js/datatables.min.js"></script>

  
<script>
  $(document).ready(function () {
    $('table').DataTable({
      order: [[0, 'asc']],        // Ordenar por primera columna (ID)
      pageLength: 100,            // Mostrar 100 filas por defecto
      language: {
        url: '../public/js/es-ES.json'  // Ruta al archivo de idioma español
      }
    });
  });
</script>

<script>
$(document).ready(function () {
  $('#formAgregarHabitacion').submit(function (e) {
    e.preventDefault(); // Evita que el formulario se envíe de inmediato

    Swal.fire({
      title: '¿Agregar habitación?',
      text: "Se registrará una nueva habitación en el sistema.",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#198754',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, agregar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        // Si confirma, enviamos el formulario mediante AJAX
        $.ajax({
          url: '../controllers/agregar_habitacion.php',
          type: 'POST',
          data: $('#formAgregarHabitacion').serialize(),
          dataType: 'json',
          success: function (response) {
            if (response.success) {
              // Alerta de éxito
              Swal.fire({
                title: '¡Éxito!',
                text: response.message,
                icon: 'success',
                confirmButtonColor: '#198754'
              }).then(() => {
                // Redirigir o actualizar la página
                location.reload();
              });
            } else {
              // Alerta de error si el nombre ya existe
              Swal.fire({
                title: 'Error',
                text: response.message,
                icon: 'error',
                confirmButtonColor: '#dc3545'
              });
            }
          },
          error: function () {
            // Alerta de error en caso de fallo
            Swal.fire({
              title: 'Error',
              text: 'Hubo un problema al agregar la habitación. Inténtalo nuevamente.',
              icon: 'error',
              confirmButtonColor: '#dc3545'
            });
          }
        });
      }
    });
  });
});
</script>

<script>
$(document).ready(function() {
  $('.btn-eliminar-habitacion').click(function(e) {
    e.preventDefault();

    const id = $(this).data('id');

    Swal.fire({
      title: '¿Eliminar habitación?',
      text: "Esta acción no se puede deshacer.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        // Llamada AJAX para eliminar habitación
        $.ajax({
          url: '../controllers/eliminar_habitacion.php',
          type: 'GET',
          data: { id: id },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              Swal.fire({
                title: 'Eliminado',
                text: response.message,
                icon: 'success',
                confirmButtonColor: '#198754'
              }).then(() => {
                // Recargar la página o quitar la fila de la tabla
                location.reload();
              });
            } else {
              Swal.fire({
                title: 'Error',
                text: response.message,
                icon: 'error',
                confirmButtonColor: '#dc3545'
              });
            }
          },
          error: function() {
            Swal.fire({
              title: 'Error',
              text: 'No se pudo eliminar la habitación. Inténtalo nuevamente.',
              icon: 'error',
              confirmButtonColor: '#dc3545'
            });
          }
        });
      }
    });
  });
});
</script>

<script>
  $(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('error') === 'nombre_duplicado') {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'El nombre de habitación ya existe. Por favor, elige otro.',
      }).then(() => {
        // Quitar el parámetro de la URL para no mostrar el alert al refrescar
        window.history.replaceState({}, document.title, window.location.pathname);
      });
    }

    if (urlParams.get('success') === 'editado') {
      Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: 'Habitación actualizada correctamente.',
        timer: 2000,
        showConfirmButton: false
      }).then(() => {
        window.history.replaceState({}, document.title, window.location.pathname);
      });
    }
  });
</script>

</body>
</html>
