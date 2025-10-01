<?php
// Este archivo asume que ya tienes disponibles: $habitaciones y $tiposHabitacion
?>

<!-- Botón para agregar nueva habitación -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Listado de Habitaciones</h4>
  <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregarHabitacion">
    <i class="fas fa-plus-circle me-1"></i>Agregar Habitación
  </button>
</div>

<!-- Tabla de habitaciones -->
<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Descripción</th>
      <th>Tipo</th>
      <th>Estado</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($habitaciones as $hab): ?>
      <tr>
        <td><?php echo $hab['id_habitacion']; ?></td>
        <td><?php echo htmlspecialchars($hab['nombre_habitacion']); ?></td>
        <td><?php echo htmlspecialchars($hab['descripcion_habitacion']); ?></td>
        <td><?php echo htmlspecialchars($hab['nombre_tipo_habitacion']); ?></td>
        <td><?php echo $hab['estado_habitacion'] ? 'Disponible' : 'Ocupada'; ?></td>
        <td>
          <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarHabitacion<?php echo $hab['id_habitacion']; ?>">
            <i class="fas fa-edit"></i>
          </button>
          <button class="btn btn-danger btn-sm btn-eliminar-habitacion" data-id="<?php echo $hab['id_habitacion']; ?>">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>




    <?php endforeach; ?>
  </tbody>
</table>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditarHabitacion<?php echo $hab['id_habitacion']; ?>" tabindex="-1" aria-labelledby="modalEditarHabitacionLabel<?php echo $hab['id_habitacion']; ?>" aria-hidden="true">
  <div class="modal-dialog">
    <form action="../controllers/editar_habitacion.php" method="POST" class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="modalEditarHabitacionLabel<?php echo $hab['id_habitacion']; ?>">
          <i class="fas fa-edit me-2"></i>Editar Habitación <?php echo htmlspecialchars($hab['nombre_habitacion']); ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_habitacion" value="<?php echo $hab['id_habitacion']; ?>">

        <div class="mb-3">
          <label for="nombre_habitacion_<?php echo $hab['id_habitacion']; ?>" class="form-label">Nombre</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-door-closed"></i></span>
            <input
              type="text"
              id="nombre_habitacion_<?php echo $hab['id_habitacion']; ?>"
              name="nombre_habitacion"
              class="form-control"
              maxlength="2"
              value="<?php echo htmlspecialchars($hab['nombre_habitacion']); ?>"
              required
            >
          </div>
        </div>

        <div class="mb-3">
          <label for="descripcion_habitacion_<?php echo $hab['id_habitacion']; ?>" class="form-label">Descripción</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-align-left"></i></span>
            <textarea
              id="descripcion_habitacion_<?php echo $hab['id_habitacion']; ?>"
              name="descripcion_habitacion"
              class="form-control"
              maxlength="255"
              rows="3"
            ><?php echo htmlspecialchars($hab['descripcion_habitacion']); ?></textarea>
          </div>
        </div>

        <div class="mb-3">
          <label for="id_tipo_habitacion_<?php echo $hab['id_habitacion']; ?>" class="form-label">Tipo de habitación</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-bed"></i></span>
            <select
              id="id_tipo_habitacion_<?php echo $hab['id_habitacion']; ?>"
              name="id_tipo_habitacion"
              class="form-select tipo-select"
              required
            >
              <?php foreach ($tiposHabitacion as $tipo): ?>
                <option
                  value="<?php echo $tipo['id_tipo_habitacion']; ?>"
                  <?php echo ($tipo['id_tipo_habitacion'] == $hab['id_tipo_habitacion']) ? 'selected' : ''; ?>
                >
                  <?php echo htmlspecialchars($tipo['nombre_tipo_habitacion']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-check">
          <input
            class="form-check-input"
            type="checkbox"
            id="estado_habitacion_<?php echo $hab['id_habitacion']; ?>"
            name="estado_habitacion"
            value="1"
            <?php echo ($hab['estado_habitacion']) ? 'checked' : ''; ?>
          >
          <label class="form-check-label" for="estado_habitacion_<?php echo $hab['id_habitacion']; ?>">
            Disponible
          </label>
        </div>

      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save me-1"></i>Guardar Cambios
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times-circle me-1"></i>Cancelar
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Agregar Habitación -->
<div class="modal fade" id="modalAgregarHabitacion" tabindex="-1" aria-labelledby="modalAgregarHabitacionLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="../controllers/agregar_habitacion.php" method="POST" class="modal-content" id="formAgregarHabitacion">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAgregarHabitacionLabel">
          <i class="fas fa-plus-circle me-2"></i>Agregar Habitación
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <!-- Nombre habitación -->
        <div class="mb-3">
          <label for="nombre_habitacion" class="form-label">Nombre</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-door-closed"></i></span>
            <input type="text" class="form-control" id="nombre_habitacion" name="nombre_habitacion" maxlength="2" required>
          </div>
        </div>

   <!-- Descripción -->
<div class="mb-3">
  <label for="descripcion_habitacion" class="form-label">Descripción</label>
  <div class="input-group">
    <span class="input-group-text"><i class="fas fa-align-left"></i></span>
    <input type="text" class="form-control" id="descripcion_habitacion" name="descripcion_habitacion" maxlength="255">
  </div>
</div>


        <!-- Tipo de habitación -->
        <div class="mb-3">
          <label for="id_tipo_habitacion" class="form-label">Tipo de habitación</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-bed"></i></span>
            <select class="form-select" id="id_tipo_habitacion" name="id_tipo_habitacion" required>
              <option value="" selected disabled>Seleccionar tipo</option>
              <?php foreach ($tiposHabitacion as $tipo): ?>
                <option value="<?= htmlspecialchars($tipo['id_tipo_habitacion']) ?>">
                  <?= htmlspecialchars($tipo['nombre_tipo_habitacion']) ?> (<?= $tipo['capacidad_tipo_habitacion'] ?> personas)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times-circle me-1"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save me-1"></i>Guardar
        </button>
      </div>
    </form>
  </div>
</div>




<!-- JS para Select2 -->
<script>
  $(document).ready(function () {
    $('.tipo-select').select2({
      dropdownParent: $('#modalAgregarHabitacion')
    });

    <?php foreach ($habitaciones as $hab): ?>
    $('#modalEditarHabitacion<?php echo $hab['id_habitacion']; ?> .tipo-select').select2({
      dropdownParent: $('#modalEditarHabitacion<?php echo $hab['id_habitacion']; ?>')
    });
    <?php endforeach; ?>
  });

  // Eliminar habitación
  $('.btn-eliminar-habitacion').click(function () {
    const id = $(this).data('id');
    Swal.fire({
      title: '¿Eliminar habitación?',
      text: "Esta acción no se puede deshacer",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = '../controllers/eliminar_habitacion.php?id=' + id;
      }
    });
  });
</script>


