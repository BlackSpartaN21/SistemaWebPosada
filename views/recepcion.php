<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

include '../config/db.php'; // Conexión a la base de datos
include '../views/header.php';

// Configuración de paginación
$habitacionesPorPagina = 6;
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($paginaActual - 1) * $habitacionesPorPagina;

$query = "
SELECT 
  h.id_habitacion, 
  h.nombre_habitacion, 
  t.nombre_tipo_habitacion, 
  h.estado_habitacion,
  c.nombres_cliente,
  c.apellidos_cliente,
  c.documento_cliente,
  r.fecha_llegada,
  r.fecha_salida,
  r.monto_total,
  r.cantidad_personas,
  m.nombre_metodo_pago
FROM habitaciones h
INNER JOIN tipo_habitaciones t ON h.id_tipo_habitacion = t.id_tipo_habitacion
LEFT JOIN reservas r ON h.id_habitacion = r.id_habitacion AND r.estado_reserva = 'Confirmada'
LEFT JOIN clientes c ON r.documento_cliente = c.documento_cliente
LEFT JOIN metodos_de_pago m ON r.id_metodo_pago = m.id_metodo_pago
-- LIMIT :inicio, :limite
WHERE h.estado_habitacion <> 3
ORDER BY h.id_habitacion ASC
";


try {
    // Obtener el total de habitaciones
    $queryTotal = "SELECT COUNT(*) as total FROM habitaciones";
    $stmtTotal = $pdo->prepare($queryTotal);
    $stmtTotal->execute();
    $stmtClientes = $pdo->prepare("SELECT documento_cliente, nombres_cliente, apellidos_cliente FROM clientes");
    $stmtClientes->execute();
    $clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);
    $totalHabitaciones = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPaginas = ceil($totalHabitaciones / $habitacionesPorPagina);


    
    $stmt = $pdo->prepare($query);
    //$stmt->bindParam(':inicio', $inicio, PDO::PARAM_INT);
    //$stmt->bindParam(':limite', $habitacionesPorPagina, PDO::PARAM_INT);
    $stmt->execute();
    $habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener métodos de pago
$stmtMetodosPago = $pdo->prepare("SELECT id_metodo_pago, nombre_metodo_pago FROM metodos_de_pago");
$stmtMetodosPago->execute();
$metodosPago = $stmtMetodosPago->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al obtener habitaciones: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Recepción - Habitaciones</title>
      <!-- CSS de Select2 -->
  <link href="../public/css/select2.min.css" rel="stylesheet" />
  <link href="../public/css/sweetalert2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../public/css/all.css">

      <style>

                .form-label i {
    margin-right: 6px;
    color:rgb(0, 0, 0);
}
.card .card-body {
  padding: 1rem 1.5rem;
}

.card .card-title {
  font-size: 1.1rem;
}

      a > .card:hover {
          cursor: pointer;
          box-shadow: 0 0 10px rgba(0,0,0,0.2);
          transform: scale(1.02);
          transition: 0.1s ease-in-out;
      }
      .ocupada-card {
    background-color: #f8d7da !important; /* rojo claro */
    transition: background-color 0.5s ease;
}
.vencida {
    background-color: #fff3cd !important; /* amarillo claro */
}

  </style>
  </head>
  <body>
      <div class="container mt-4">
        
          <div class="card mb-4 shadow-sm">
  <div class="card-body d-flex justify-content-between align-items-center">
    <div>
      <h5 class="card-title mb-0">
        <i class="fas fa-user me-2 text-primary"></i>
        Bienvenido, <?= htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']) ?>
      </h5>
      <small class="text-muted">Rol: <?= htmlspecialchars($_SESSION['rol']) ?></small>
    </div>
  </div>
</div>

          <h2 class="text-center">Listado de Habitaciones</h2><br>
          <div class="row">
          <?php foreach ($habitaciones as $habitacion): ?>
      <div class="col-md-3 mb-3">

          <a style="text-decoration: none; color: inherit;" 
   data-bs-toggle="modal" 
   data-bs-target="#modalHabitacion<?php echo $habitacion['id_habitacion']; ?>">
  <?php
  $esOcupada = !$habitacion['estado_habitacion'];
  $esVencida = false;
if ($esOcupada && isset($habitacion['fecha_salida'])) {
    $fechaSalida = new DateTime($habitacion['fecha_salida'], new DateTimeZone('America/Caracas')); // Ajusta si tu zona horaria es diferente
    $ahora = new DateTime('now', new DateTimeZone('America/Caracas'));
    $esVencida = $fechaSalida < $ahora;
}

  $claseCard = $esVencida ? 'vencida' : ($esOcupada ? 'ocupada-card' : '');
?>
<div class="card h-100 <?php echo $claseCard; ?>"

     id="cardHabitacion<?php echo $habitacion['id_habitacion']; ?>">
    <img src="../public/img/hablogo.jpg" class="card-img-top" alt="Habitación">
    <div class="card-body">
      <h5 class="card-title">
  Habitación <?php echo htmlspecialchars($habitacion['nombre_habitacion']); ?>
  <?php if ($esVencida): ?>
    <span class="badge bg-danger ms-2">Esperando Vaciar</span>
  <?php endif; ?>
</h5>

      <p class="card-text"><strong>Tipo:</strong> <?php echo htmlspecialchars($habitacion['nombre_tipo_habitacion']); ?></p>
      <p class="card-text"><strong>Estado:</strong> 
        <?php echo $habitacion['estado_habitacion'] ? 'Disponible' : 'No disponible'; ?>
      </p>
    </div>
  </div>
</a>

      </div>


          
<?php if ($habitacion['estado_habitacion']): ?>
      <!-- Modal individual -->
      <div class="modal fade" id="modalHabitacion<?php echo $habitacion['id_habitacion']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalLabel<?php echo $habitacion['id_habitacion']; ?>">
                Habitación <?php echo htmlspecialchars($habitacion['nombre_habitacion']); ?>
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              <form method="POST" action="../controllers/procesar_reserva.php" id="formReserva<?php echo $habitacion['id_habitacion']; ?>">


<input type="hidden" name="id_habitacion" value="<?php echo $habitacion['id_habitacion']; ?>">
<input type="hidden" name="origen_reserva" value="Recepción"> <!-- o el valor que quieras -->

              <div class="mb-3">
    <label for="selectCliente<?php echo $habitacion['id_habitacion']; ?>" class="form-label"><i class="fas fa-user"></i> Seleccionar Cliente</label>
    <select class="form-select cliente-select" id="selectCliente<?php echo $habitacion['id_habitacion']; ?>" name="cedula_cliente" style="width: 100%">
      <option value="">-- Seleccione un cliente --</option>
      <?php foreach ($clientes as $cliente): ?>
          <option value="<?php echo htmlspecialchars($cliente['documento_cliente']); ?>">
              <?php echo htmlspecialchars($cliente['documento_cliente'] . ' - ' . $cliente['nombres_cliente'] . ' ' . $cliente['apellidos_cliente']); ?>
          </option>
      <?php endforeach; ?>
    </select>
  </div>

    <!-- Select de tipo de tarifa -->
<div class="mb-3">
  <label for="tipoTarifa<?php echo $habitacion['id_habitacion']; ?>" class="form-label"> <i class="fas fa-money-bill-wave"></i>Tipo de Tarifa</label>
<select class="form-select" name="tipo_tarifa" id="tipoTarifa<?php echo $habitacion['id_habitacion']; ?>">
  <option value=""selected>Seleccione una tarifa</option>
    <option value="24 Horas" >24 Horas</option>
    <option value="3 Horas" <?php echo ($habitacion['nombre_tipo_habitacion'] === 'Triple') ? 'disabled' : ''; ?>>
      3 Horas
    </option>
</select>

  <?php if ($habitacion['nombre_tipo_habitacion'] === 'Triple'): ?>
    <div class="form-text text-danger">Solo disponible por 24 horas para habitaciones triples.</div>
  <?php endif; ?>
</div>

<!-- Precio mostrado dinámicamente -->
<div class="mb-3">
  <label class="form-label"><i class="fas fa-tags"></i>Precio Tarifa:</label>
  <div id="precioResultado<?php echo $habitacion['id_habitacion']; ?>" class="form-control-plaintext text-primary fw-bold">
    * Selecciona una tarifa *
  </div>
</div>


<!-- Check para personalizar fechas -->
<div class="form-check mb-3">
  <input class="form-check-input toggle-fechas" type="checkbox" 
         id="checkFechas<?php echo $habitacion['id_habitacion']; ?>">
  <label class="form-check-label" for="checkFechas<?php echo $habitacion['id_habitacion']; ?>">
    Asignar fechas manualmente
  </label>
</div>

<!-- Campos de fecha -->
<div class="row mb-3 grupo-fechas" id="grupoFechas<?php echo $habitacion['id_habitacion']; ?>" style="display: none;">
  <div class="col">
    <label for="fechaLlegada<?php echo $habitacion['id_habitacion']; ?>" class="form-label"><i class="fas fa-sign-in-alt"></i>Fecha de Llegada</label>
    <input type="datetime-local" class="form-control" name="fecha_llegada" 
           id="fechaLlegada<?php echo $habitacion['id_habitacion']; ?>">
  </div>
<!-- Campo para días de estadía, solo para tarifa 24 horas -->
<div class="mb-3" id="diasEstadiaInput<?php echo $habitacion['id_habitacion']; ?>" style="display:none;">
  <label for="diasEstadia<?php echo $habitacion['id_habitacion']; ?>" class="form-label">
    <i class="fas fa-calendar-days"></i> Días de estadía
  </label>
  <input type="number" min="1" value="1" class="form-control" name="dias_estadia" id="diasEstadia<?php echo $habitacion['id_habitacion']; ?>">
  <!-- Fecha de salida calculada dinámicamente -->
<div id="fechaSalidaTexto<?php echo $habitacion['id_habitacion']; ?>" class="form-text text-primary mt-1" style="display: none;">
  Salida estimada: <span></span>
</div>


</div>

  <!-- Mostrar cantidad de días -->
<div class="col-12 mt-2">
  <div id="diasEstadia<?php echo $habitacion['id_habitacion']; ?>" class="alert alert-info p-2 d-none">
    Días de estadía: <span class="num-dias">0</span>
  </div>
</div>
</div>




<!-- Cantidad de Personas -->
<div class="mb-3">
  <label for="cantidad_personas<?php echo $habitacion['id_habitacion']; ?>" class="form-label"><i class="fas fa-users"></i>Cantidad de personas</label>
  <select class="form-select" id="cantidad_personas<?php echo $habitacion['id_habitacion']; ?>" name="cantidad_personas" required>
    <option value="1">1 persona</option>
    <option value="2">2 personas</option>
    <option value="3" <?php echo ($habitacion['nombre_tipo_habitacion'] === 'Matrimonial') ? 'disabled' : ''; ?>>3 personas</option>
  </select>
  <?php if ($habitacion['nombre_tipo_habitacion'] === 'Matrimonial'): ?>
    <div class="form-text text-danger">Máximo 2 personas permitidas para habitaciones matrimoniales.</div>
  <?php endif; ?>
</div>

<!-- Métodos de Pago -->
<div class="mb-3">
  <label for="metodo_pago<?php echo $habitacion['id_habitacion']; ?>" class="form-label"><i class="fas fa-credit-card"></i>Método de Pago</label>
  <select class="form-select" id="metodo_pago<?php echo $habitacion['id_habitacion']; ?>" name="metodo_pago" required>
    <option value="">-- Seleccione un método --</option>
    <?php foreach ($metodosPago as $metodo): ?>
      <option value="<?php echo $metodo['id_metodo_pago']; ?>">
        <?php echo htmlspecialchars($metodo['nombre_metodo_pago']); ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>

<!-- Monto total mostrado dinámicamente (centrado y más grande) -->
<div class="mb-3 text-center">
  <label class="form-label d-block"><i class="fas fa-dollar-sign"></i>Monto Total:</label>
  <div id="montoTotal<?php echo $habitacion['id_habitacion']; ?>"
       class="fs-4 text-success fw-bold">
    ...
  </div>
</div>


<div class="d-flex justify-content-end gap-2">
  <button type="submit" class="btn btn-success">
    <i class="fas fa-floppy-disk me-1"></i> Guardar
  </button>

  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
    <i class="fas fa-times me-1"></i> Cancelar
  </button>
</div>
    </form>





            </div>
          </div>
        </div>
      </div>

<?php else: ?>
  <!-- Modal de Información de Cliente para habitación ocupada -->
  <div class="modal fade" id="modalHabitacion<?php echo $habitacion['id_habitacion']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title">Habitación Ocupada</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
<?php if ($habitacion['nombres_cliente']): ?>
    <p><strong><i class="fas fa-user me-2"></i>Cliente:</strong> <?php echo htmlspecialchars($habitacion['nombres_cliente'] . ' ' . $habitacion['apellidos_cliente']); ?></p>
    <p><strong><i class="fas fa-id-card me-2"></i>Documento:</strong> <?php echo htmlspecialchars($habitacion['documento_cliente']); ?></p>
<?php else: ?>
    <p><strong><i class="fas fa-user me-2"></i>Cliente:</strong> No registrado</p>
    <p><strong><i class="fas fa-id-card me-2"></i>Documento:</strong> N/A</p>
<?php endif; ?>

          <p><strong><i class="fas fa-door-closed me-2"></i>Estado:</strong> Ocupada</p>
          <?php if ($habitacion['fecha_llegada'] && $habitacion['fecha_salida']): ?>
<p><strong><i class="fas fa-sign-in-alt me-2"></i>Fecha de llegada:</strong> <?php echo date('d/m/Y h:i A', strtotime($habitacion['fecha_llegada'])); ?></p>
<p><strong><i class="fas fa-sign-out-alt me-2"></i>Fecha de salida:</strong> <?php echo date('d/m/Y h:i A', strtotime($habitacion['fecha_salida'])); ?></p>
<?php endif; ?>

<?php if ($habitacion['monto_total']): ?>
  <p><strong><i class="fas fa-dollar-sign me-3"></i></i>Monto total:</strong> <?php echo number_format($habitacion['monto_total'], 2) . ' $'; ?></p>
<?php endif; ?>

<?php if ($habitacion['cantidad_personas']): ?>
  <p><strong><i class="fas fa-users me-2"></i>Cantidad de personas:</strong> <?php echo (int)$habitacion['cantidad_personas']; ?></p>
<?php endif; ?>

<?php if ($habitacion['nombre_metodo_pago']): ?>
  <p><strong><i class="fas fa-credit-card me-2"></i>Método de pago:</strong> <?php echo htmlspecialchars($habitacion['nombre_metodo_pago']); ?></p>
<?php endif; ?>
<?php if ($habitacion['fecha_salida']): ?>
  <div class="mt-3">
    <p><strong><i class="fas fa-clock me-2"></i>Tiempo restante:</strong> <span id="reloj<?php echo $habitacion['id_habitacion']; ?>" class="text-danger fw-bold">Calculando...</span></p>
  </div>
<script>
  const salida<?php echo $habitacion['id_habitacion']; ?> = new Date("<?php echo date('Y-m-d H:i:s', strtotime($habitacion['fecha_salida'])); ?>").getTime();
  const reloj<?php echo $habitacion['id_habitacion']; ?> = document.getElementById("reloj<?php echo $habitacion['id_habitacion']; ?>");
  const card<?php echo $habitacion['id_habitacion']; ?> = document.getElementById("cardHabitacion<?php echo $habitacion['id_habitacion']; ?>");

  function actualizarReloj<?php echo $habitacion['id_habitacion']; ?>() {
    const ahora = new Date().getTime();
    const diferencia = salida<?php echo $habitacion['id_habitacion']; ?> - ahora;

    if (diferencia > 0) {
      const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
      const horas = Math.floor((diferencia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
      const segundos = Math.floor((diferencia % (1000 * 60)) / 1000);

      let texto = '';
      if (dias > 0) texto += dias + 'd ';
      texto += horas + 'h ' + minutos + 'm ' + segundos + 's';

      reloj<?php echo $habitacion['id_habitacion']; ?>.innerText = texto;
    } else {
      reloj<?php echo $habitacion['id_habitacion']; ?>.innerText = "Tiempo agotado";
      // Cambiar fondo de la tarjeta a amarillo claro
      if (card<?php echo $habitacion['id_habitacion']; ?>) {
        card<?php echo $habitacion['id_habitacion']; ?>.classList.remove("ocupada-card");
        card<?php echo $habitacion['id_habitacion']; ?>.classList.add("vencida");
      }
    }
  }

  setInterval(actualizarReloj<?php echo $habitacion['id_habitacion']; ?>, 1000);
</script>


<?php endif; ?>


        </div>
<div class="modal-footer">
<button class="btn btn-danger btn-vaciar" data-id="<?php echo $habitacion['id_habitacion']; ?>">
  Vaciar habitación
</button>
  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>
      </div>
    </div>
  </div>
<?php endif; ?>
<?php endforeach; ?>

          
  <!-- Paginación -->
          <!--<nav class="mt-4">
              <ul class="pagination justify-content-center">
                  <?php if ($paginaActual > 1): ?>
                      <li class="page-item">
                          <a class="page-link" href="?pagina=<?php echo $paginaActual - 1; ?>" 
                          style="background-color: #ba3b0a; border-color: #ba3b0a; color: white;">
                          Anterior
                          </a>
                      </li>
                  <?php endif; ?>

                  <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                      <li class="page-item <?php echo ($i == $paginaActual) ? 'active' : ''; ?>">
                          <a class="page-link" href="?pagina=<?php echo $i; ?>" 
                          style="background-color: <?php echo ($i == $paginaActual) ? '#ba3b0a' : 'white'; ?>; 
                                  border-color: #ba3b0a; 
                                  color: <?php echo ($i == $paginaActual) ? 'white' : '#ba3b0a'; ?>;">
                          <?php echo $i; ?>
                          </a>
                      </li>
                  <?php endfor; ?>

                  <?php if ($paginaActual < $totalPaginas): ?>
                      <li class="page-item">
                          <a class="page-link" href="?pagina=<?php echo $paginaActual + 1; ?>" 
                          style="background-color: #ba3b0a; border-color: #ba3b0a; color: white;">
                          Siguiente
                          </a>
                      </li>
                  <?php endif; ?>
              </ul>
          </nav>
      </div>-->
  </body>

  <!-- jQuery (requerido por Select2) -->
  <script src="../public/js/jquery-3.7.1.min.js"></script>
  <!-- JS de Select2 -->
  <script src="../public/js/select2.min.js"></script>
  <script src="../public/js/sweetalert2.min.js"></script>
  <script src="../public/js/recepcion.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
<?php foreach ($habitaciones as $habitacion): 
  $id = $habitacion['id_habitacion'];
?>

function actualizarFechaSalidaTexto<?php echo $id; ?>() {
  const tipoTarifa = $('#tipoTarifa<?php echo $id; ?>').val();
  const llegadaStr = $('#fechaLlegada<?php echo $id; ?>').val();
  const dias = parseInt($('#diasEstadia<?php echo $id; ?>').val(), 10);
  const contenedorSalida = $('#fechaSalidaTexto<?php echo $id; ?>');

  if (tipoTarifa === '24 Horas' && llegadaStr && dias > 0) {
    const llegada = new Date(llegadaStr);
    llegada.setDate(llegada.getDate() + dias);

    const opcionesFormato = {
      weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
      hour: '2-digit', minute: '2-digit'
    };

    const salidaFormateada = llegada.toLocaleString('es-VE', opcionesFormato);
    contenedorSalida.find('span').text(salidaFormateada);
    contenedorSalida.show();
  } else {
    contenedorSalida.hide();
  }
}



  $('#tipoTarifa<?php echo $id; ?>').on('change', actualizarPrecioYTotal<?php echo $id; ?>);
  $('#fechaLlegada<?php echo $id; ?>').on('change', actualizarPrecioYTotal<?php echo $id; ?>);
  $('#fechaSalida<?php echo $id; ?>').on('change', actualizarPrecioYTotal<?php echo $id; ?>);

  $('#checkFechas<?php echo $id; ?>').on('change', function () {
    setTimeout(actualizarPrecioYTotal<?php echo $id; ?>, 100);
  });
function actualizarCamposSegunTarifa<?php echo $id; ?>() {
  const tipoTarifa = $('#tipoTarifa<?php echo $id; ?>').val();

  if (tipoTarifa === '3 Horas') {
    // Mostrar fecha llegada, ocultar fecha salida y días de estadía
    $('#fechaLlegada<?php echo $id; ?>').parent().show();
    $('#fechaSalida<?php echo $id; ?>').parent().hide();
    $('#diasEstadiaInput<?php echo $id; ?>').hide();
  } else if (tipoTarifa === '24 Horas') {
    // Mostrar fecha llegada y días de estadía, ocultar fecha salida
    $('#fechaLlegada<?php echo $id; ?>').parent().show();
    $('#fechaSalida<?php echo $id; ?>').parent().hide();
    $('#diasEstadiaInput<?php echo $id; ?>').show();
  } else {
    // Por defecto, mostrar ambas fechas y ocultar días de estadía
    $('#fechaLlegada<?php echo $id; ?>').parent().show();
    $('#fechaSalida<?php echo $id; ?>').parent().show();
    $('#diasEstadiaInput<?php echo $id; ?>').hide();
  }
}

// Actualizar monto total considerando tarifa y días o horas
function actualizarPrecioYTotal<?php echo $id; ?>() {
  const tipoTarifa = $('#tipoTarifa<?php echo $id; ?>').val();
  const idHabitacion = <?php echo $id; ?>;
  const precioDiv = $('#precioResultado<?php echo $id; ?>');
  const montoDiv = $('#montoTotal<?php echo $id; ?>');

  if (!tipoTarifa) {
    precioDiv.text('* Selecciona una tarifa *');
    montoDiv.text('...');
    return;
  }

  $.ajax({
    url: '../controllers/obtener_precio.php',
    type: 'POST',
    data: {
      id_habitacion: idHabitacion,
      tipo_tarifa: tipoTarifa
    },
    success: function (precio) {
      precio = parseFloat(precio);
      if (isNaN(precio)) {
        precioDiv.text('Error en precio');
        montoDiv.text('Error en monto');
        return;
      }

      precioDiv.text(precio + ' $');

      const llegadaStr = $('#fechaLlegada<?php echo $id; ?>').val();
      const diasEstadia = parseInt($('#diasEstadia<?php echo $id; ?>').val(), 10);

      if (tipoTarifa === '3 Horas') {
        // Para 3 Horas monto = precio fijo (no importa fechas)
        montoDiv.text(precio + ' $');
      } else if (tipoTarifa === '24 Horas') {
        // Para 24 Horas se usa días de estadía (por defecto 1 si no hay)
        const dias = (diasEstadia && diasEstadia > 0) ? diasEstadia : 1;
        montoDiv.text((dias * precio) + ' $');
      } else {
        // Para otras tarifas, si hay fechas calcular diferencia, sino precio fijo
        const salidaStr = $('#fechaSalida<?php echo $id; ?>').val();
        if (llegadaStr && salidaStr) {
          const llegada = new Date(llegadaStr);
          const salida = new Date(salidaStr);
          const diffTime = salida - llegada;
          const dias = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
          montoDiv.text((dias > 0 ? dias : 1) * precio + ' $');
        } else {
          montoDiv.text(precio + ' $');
        }
      }
    },
    error: function () {
      precioDiv.text('Error al obtener precio');
      montoDiv.text('Error al calcular monto');
    }
  });
}

$('#tipoTarifa<?php echo $id; ?>').on('change', actualizarPrecioYTotal<?php echo $id; ?>);
$('#fechaLlegada<?php echo $id; ?>').on('change input', actualizarPrecioYTotal<?php echo $id; ?>);
$('#diasEstadia<?php echo $id; ?>').on('change input', actualizarPrecioYTotal<?php echo $id; ?>);
$('#fechaSalida<?php echo $id; ?>').on('change input', actualizarPrecioYTotal<?php echo $id; ?>);
$('#checkFechas<?php echo $id; ?>').on('change', function () {
  setTimeout(actualizarPrecioYTotal<?php echo $id; ?>, 100);
});


// Eventos para actualizar UI y cálculo
$('#tipoTarifa<?php echo $id; ?>').on('change', function() {
  actualizarCamposSegunTarifa<?php echo $id; ?>();
  actualizarPrecioYTotal<?php echo $id; ?>();
});

$('#fechaLlegada<?php echo $id; ?>, #diasEstadia<?php echo $id; ?>').on('change input', function() {
  actualizarPrecioYTotal<?php echo $id; ?>();
  actualizarFechaSalidaTexto<?php echo $id; ?>();
});

// Inicializar al cargar
actualizarCamposSegunTarifa<?php echo $id; ?>();
actualizarPrecioYTotal<?php echo $id; ?>();

<?php endforeach; ?>
});
</script>


</html>