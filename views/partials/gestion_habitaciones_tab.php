<?php
// Asegúrate de que $tiposHabitacion exista como arreglo
$tiposHabitacion = $tiposHabitacion ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="fas fa-bed me-2"></i>Habitaciones</h5>
  <button class="btn btn-primary" id="btnNuevaHabitacion">
    <i class="fas fa-plus-circle me-1"></i>Nueva habitación
  </button>
</div>

<table id="tablaHabitaciones" class="table table-striped table-bordered w-100">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Número</th>
      <th>Tipo</th>
      <th>Capacidad</th>
      <th>Descripción</th>
      <th>Estado</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>

<!-- Modal: Crear/Editar -->
<div class="modal fade" id="modalHabitacion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formHabitacion" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalHabitacionLabel">Nueva habitación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_habitacion" id="id_habitacion">
        <div class="mb-3">
          <label class="form-label" for="nombre_habitacion"><i class="fas fa-door-closed me-1"></i>Número</label>
          <input type="text" class="form-control" name="nombre_habitacion" id="nombre_habitacion" maxlength="2" required>
          <div class="form-text">Ej: 01, 7, 12</div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="descripcion_habitacion"><i class="fas fa-align-left me-1"></i>Descripción</label>
          <textarea class="form-control" name="descripcion_habitacion" id="descripcion_habitacion" rows="2" maxlength="255"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label" for="id_tipo_habitacion"><i class="fas fa-bed me-1"></i>Tipo</label>
          <select class="form-select" name="id_tipo_habitacion" id="id_tipo_habitacion" required>
            <?php foreach ($tiposHabitacion as $t) { ?>
              <option value="<?= (int)$t['id_tipo_habitacion'] ?>">
                <?= htmlspecialchars($t['nombre_tipo_habitacion']) ?>
              </option>
            <?php } ?>
          </select>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="estado_habitacion" name="estado_habitacion" value="1" checked>
          <label class="form-check-label" for="estado_habitacion">Disponible</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script>
window.addEventListener('load', function() {
  const $table = $('#tablaHabitaciones');
  const controller = '../controllers/habitaciones_controller.php';
  const modalEl = document.getElementById('modalHabitacion');
  const modal = new bootstrap.Modal(modalEl);

  const dt = $table.DataTable({
    ajax: {
      url: controller,
      data: { action: 'list' },
      dataSrc: res => res && res.ok ? res.data : []
    },
    language: { url: '../public/js/es-ES.json' },
    pageLength: 25,
    lengthMenu: [[10, 25, 50, 100, -1],[10, 25, 50, 100, 'Todos']],
    dom:
  "<'row align-items-center mb-2'<'col-12 col-md-6'l><'col-12 col-md-6 text-md-end'f>>" +
  "t" +
  "<'row align-items-center mt-2'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
  columnDefs: [
    { targets: 0, visible: false, searchable: false } // Oculta la col ID (primera)
  ],
  order: [[1, 'asc']], // Ordena por "Número"

  columnDefs: [
  { targets: 0, visible: false, searchable: false },        // ID oculto
  { targets: [1,2,5,6], className: 'text-center' },          // Número, Tipo, Descripción, Estado
  { targets: [3], className: 'text-center' },               // Capacidad
  { targets: [4], className: 'text-center' }                // Acciones
],


    columns: [
      { data: 'id_habitacion' },
      { data: 'nombre_habitacion' },
      { data: 'nombre_tipo_habitacion' },
      { data: 'capacidad_tipo_habitacion' },
      { data: 'descripcion_habitacion', defaultContent: '' },
      {
        data: 'estado_habitacion',
        render: e => {
          e = Number(e);
          if (e === 1) return '<span class="badge bg-success">Disponible</span>';
          if (e === 0) return '<span class="badge bg-warning text-dark">No disponible</span>';
          if (e === 3) return '<span class="badge bg-secondary">Deshabilitada</span>';
          return '<span class="badge bg-light text-dark">N/D</span>';
        }
      },
      {
        data: null,
        orderable: false,
        render: (row) => {
          const estado = Number(row.estado_habitacion);
          const isDisabled = estado === 3;
          const notToggleable = estado === 0; // No disponible => no se puede deshabilitar
          const toggleIsToDisable = !isDisabled; // 1 -> 3 | 3 -> 1 (cuando es posible)
          return `
            <div class="btn-group btn-group-sm" role="group">
              <button class="btn btn-outline-primary btn-edit" title="Editar"><i class="fas fa-pen"></i></button>
              <button class="btn ${notToggleable ? 'btn-secondary' : (toggleIsToDisable ? 'btn-warning' : 'btn-success')} btn-toggle"
                      ${notToggleable ? 'disabled' : ''} 
                      title="${notToggleable ? 'No se puede deshabilitar: habitación no disponible/ocupada' : (toggleIsToDisable ? 'Deshabilitar' : 'Habilitar')}">
                <i class="fas ${notToggleable ? 'fa-ban' : (toggleIsToDisable ? 'fa-toggle-off' : 'fa-toggle-on')}"></i>
              </button>
              <button class="btn btn-outline-danger btn-del" ${isDisabled ? '' : 'disabled'} title="Eliminar">
                <i class="fas fa-trash"></i>
              </button>
            </div>`;
        }
      }
    ]
  });

  // Cargar tipos por AJAX si el select está vacío
  const $tipo = $('#id_tipo_habitacion');
  if ($tipo.children().length === 0) {
    $.getJSON(controller, { action: 'tipos' }, function(res) {
      if (res && res.ok) {
        res.data.forEach(t => $tipo.append(`<option value="${t.id_tipo_habitacion}">${t.nombre_tipo_habitacion}</option>`));
      }
    });
  }

  // Crear
  $('#btnNuevaHabitacion').on('click', () => {
    $('#modalHabitacionLabel').text('Nueva habitación');
    $('#formHabitacion')[0].reset();
    $('#id_habitacion').val('');
    $('#estado_habitacion').prop('checked', true);
    modal.show();
  });

  // Editar
  $table.on('click', '.btn-edit', function() {
    const data = dt.row($(this).closest('tr')).data();
    $('#modalHabitacionLabel').text(`Editar habitación #${data.id_habitacion}`);
    $('#id_habitacion').val(data.id_habitacion);
    $('#nombre_habitacion').val(data.nombre_habitacion);
    $('#descripcion_habitacion').val(data.descripcion_habitacion);
    $('#id_tipo_habitacion').val(data.id_tipo_habitacion);
    $('#estado_habitacion').prop('checked', Number(data.estado_habitacion) === 1);
    modal.show();
  });

  // Guardar (create/update)
  $('#formHabitacion').on('submit', function(e) {
    e.preventDefault();
    const formData = $(this).serializeArray().reduce((acc, it) => { acc[it.name] = it.value; return acc; }, {});
    const isEdit = !!formData.id_habitacion;
    formData.action = isEdit ? 'update' : 'create';
    formData.estado_habitacion = $('#estado_habitacion').is(':checked') ? 1 : 0;

    $.post(controller, formData, function(res) {
      if (res && res.ok) {
        modal.hide();
        dt.ajax.reload(null, false);
        Swal.fire({ icon: 'success', title: 'Guardado', text: isEdit ? 'Habitación actualizada.' : 'Habitación creada.' });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: (res && res.error) || 'No se pudo guardar.' });
      }
    }, 'json').fail(xhr => {
      const res = xhr.responseJSON || {}; 
      Swal.fire({ icon: 'error', title: 'Error', text: res.error || 'Error del servidor.' });
    });
  });

  // Toggle estado (1/3) – no permitir cuando current = 0
  $table.on('click', '.btn-toggle', function() {
    const row = dt.row($(this).closest('tr')).data();
    const current = Number(row.estado_habitacion);
    if (current === 0) {
      Swal.fire({ icon: 'info', title: 'No permitido', text: 'No puedes deshabilitar una habitación no disponible/ocupada.' });
      return;
    }
    const nuevo = (current === 3) ? 1 : 3; // 3 -> 1 (habilitar), otros -> 3 (deshabilitar)
    const accionTxt = nuevo === 3 ? 'deshabilitar' : 'habilitar';

    Swal.fire({
      icon: 'question', title: '¿Confirmar?', text: `Vas a ${accionTxt} la habitación ${row.nombre_habitacion}.`, showCancelButton: true,
      confirmButtonText: 'Sí', cancelButtonText: 'Cancelar'
    }).then(r => {
      if (!r.isConfirmed) return;
      $.post(controller, { action: 'toggle', id_habitacion: row.id_habitacion, estado_habitacion: nuevo }, function(res) {
        if (res && res.ok) {
          dt.ajax.reload(null, false);
          Swal.fire({ icon: 'success', title: 'Listo', text: `Habitación ${accionTxt}a.` });
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: (res && res.error) || 'No se pudo cambiar el estado.' });
        }
      }, 'json');
    });
  });

  // Eliminar (solo si está deshabilitada = 3)
  $table.on('click', '.btn-del', function() {
    const row = dt.row($(this).closest('tr')).data();
    if (Number(row.estado_habitacion) !== 3) return; // seguridad

    Swal.fire({
      icon: 'warning', title: 'Eliminar definitivamente',
      html: `Se eliminará la habitación <b>${row.nombre_habitacion}</b> y todas sus reservas asociadas.`,
      showCancelButton: true, confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar'
    }).then(r => {
      if (!r.isConfirmed) return;
      $.post(controller, { action: 'delete', id_habitacion: row.id_habitacion }, function(res) {
        if (res && res.ok) {
          dt.ajax.reload(null, false);
          Swal.fire({ icon: 'success', title: 'Eliminada', text: 'Operación completada.' });
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: (res && res.error) || 'No se pudo eliminar.' });
        }
      }, 'json');
    });
  });
});
</script>
