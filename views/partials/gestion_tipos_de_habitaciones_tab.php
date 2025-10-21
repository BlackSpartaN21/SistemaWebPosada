<?php
// Este tab replica la UI del CRUD de habitaciones, adaptado a tipo_habitaciones.
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Tipos de habitaciones</h5>
  <button class="btn btn-success" id="btnNuevoTipo">
    <i class="fas fa-plus-circle me-1"></i>Nuevo tipo de Habitación
  </button>
</div>

<table id="tablaTipos" class="table table-striped table-bordered w-100">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Capacidad</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>

<!-- Modal: Crear/Editar Tipo -->
<div class="modal fade" id="modalTipo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formTipo" class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalTipoLabel">Nuevo tipo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_tipo_habitacion" id="id_tipo_habitacion">
        <div class="mb-3">
          <label class="form-label" for="nombre_tipo_habitacion"><i class="fas fa-tag me-1"></i>Nombre</label>
          <input type="text" class="form-control" name="nombre_tipo_habitacion" id="nombre_tipo_habitacion" maxlength="20" required>
          <div class="form-text">Ej: Matrimonial, Doble, Triple...</div>
        </div>
<div class="mb-3">
  <label class="form-label" for="capacidad_tipo_habitacion"><i class="fas fa-users me-1"></i>Capacidad</label>
  <input
    type="number"
    class="form-control"
    name="capacidad_tipo_habitacion"
    id="capacidad_tipo_habitacion"
    min="1"
    max="99"
    step="1"
    required
    inputmode="numeric"
    pattern="[0-9]{1,2}"
  >
  <div class="form-text">Número de personas (1–99)</div>
</div>

      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Guardar</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script>
window.addEventListener('load', function() {
  const $table = $('#tablaTipos');
  const controller = '../controllers/tipos_habitacion_controller.php';
  const modalEl = document.getElementById('modalTipo');
  const modal = new bootstrap.Modal(modalEl);

  // (opcional, pero ayuda a evitar stacking raro)
  $('#modalTipo').appendTo('body');

  const dt = $table.DataTable({
    ajax: { url: controller, data: { action: 'list' }, dataSrc: res => res && res.ok ? res.data : [] },
    language: { url: '../public/js/es-ES.json' },
    pageLength: 25,
    lengthMenu: [[10, 25, 50, 100, -1],[10, 25, 50, 100, 'Todos']],
    dom:
  "<'row align-items-center mb-2'<'col-12 col-md-6'l><'col-12 col-md-6 text-md-end'f>>" +
  "t" +
  "<'row align-items-center mt-2'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
columnDefs: [
  { targets: 0, visible: false, searchable: false } // Oculta la col ID
],
order: [[1, 'asc']], // Ordena por "Nombre"
columnDefs: [
  { targets: 0, visible: false, searchable: false },        // ID oculto
  { targets: [1], className: 'text-center' },                // Nombre
  { targets: [2], className: 'text-center' },               // Capacidad
  { targets: [3], className: 'text-center' }                // Acciones
],

    columns: [
      { data: 'id_tipo_habitacion' },
      { data: 'nombre_tipo_habitacion' },
      { data: 'capacidad_tipo_habitacion' },
      {
        data: null,
        orderable: false,
        render: (row) => `
          <div class="btn-group btn-group-sm" role="group">
            <button class="btn btn-outline-primary btn-edit" title="Editar"><i class="fas fa-pen-to-square"></i></button>
            <button class="btn btn-outline-danger btn-del" title="Eliminar"><i class="fas fa-trash"></i></button>
          </div>`
      }
    ]
  });

  // NUEVO: variable segura para recordar el id en modo edición
  let editId = 0;

  // Crear
  $('#btnNuevoTipo').on('click', () => {
    editId = 0;
    $('#modalTipoLabel').text('Nuevo tipo');
    $('#formTipo')[0].reset();
    // Aseguramos que el hidden esté vacío y habilitado
    $('#id_tipo_habitacion').prop('disabled', false).val('');
    modal.show();
  });

  // Editar (asegura que el hidden tenga el id y no esté disabled)
  $table.on('click', '.btn-edit', function() {
    const data = dt.row($(this).closest('tr')).data();
    editId = Number(data.id_tipo_habitacion) || 0;

    $('#modalTipoLabel').text(`Editar tipo #${editId}`);
    $('#id_tipo_habitacion').prop('disabled', false).val(editId);
    $('#nombre_tipo_habitacion').val(data.nombre_tipo_habitacion);
    $('#capacidad_tipo_habitacion').val(data.capacidad_tipo_habitacion);

    modal.show();
  });

  // Guardar (create/update) — robusto: fuerza acción según ID
  $('#formTipo').on('submit', function(e) {
    e.preventDefault();

    // Construye objeto desde el form sin perder campos
    const fd = Object.fromEntries(new FormData(this).entries());

    // Toma el id desde el hidden o desde editId, y fuerzalo a número
    const id = Number(fd.id_tipo_habitacion || editId || 0);

    // Forzar el id en fd (por si el hidden no vino)
    fd.id_tipo_habitacion = id > 0 ? id : '';

    // Acción decidida por id
    fd.action = id > 0 ? 'update' : 'create';
     // ✅ Validación 1–99
  const cap = parseInt(fd.capacidad_tipo_habitacion, 10);
  if (!Number.isInteger(cap) || cap < 1 || cap > 99) {
    Swal.fire({ icon: 'error', title: 'Capacidad inválida', text: 'La capacidad debe estar entre 1 y 99.' });
    return;
  }

    $.post(controller, fd, function(res) {
      if (res && res.ok) {
        modal.hide();
        dt.ajax.reload(null, false);
        Swal.fire({ icon: 'success', title: 'Guardado', text: id > 0 ? 'Tipo actualizado.' : 'Tipo creado.' });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: (res && res.error) || 'No se pudo guardar.' });
      }
    }, 'json').fail(xhr => {
      const res = xhr.responseJSON || {};
      Swal.fire({ icon: 'error', title: 'Error', text: res.error || 'Error del servidor.' });
    });
  });

  // Eliminar (con fail para ver el mensaje 409 cuando está en uso)
  $table.on('click', '.btn-del', function() {
    const row = dt.row($(this).closest('tr')).data();
    Swal.fire({
      icon: 'warning', title: 'Eliminar tipo',
      html: `Se eliminará el tipo <b>${row.nombre_tipo_habitacion}</b>.`,
      showCancelButton: true, confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar'
    }).then(r => {
      if (!r.isConfirmed) return;
      $.post(controller, { action: 'delete', id_tipo_habitacion: row.id_tipo_habitacion }, function(res) {
        if (res && res.ok) {
          dt.ajax.reload(null, false);
          Swal.fire({ icon: 'success', title: 'Eliminado', text: 'Operación completada.' });
        } else {
          Swal.fire({ icon: 'error', title: 'No se pudo eliminar', text: (res && res.error) || 'Error desconocido.' });
        }
      }, 'json').fail(xhr => {
        const res = xhr.responseJSON || {};
        const msg = res.error || xhr.responseText || 'Error del servidor.';
        Swal.fire({ icon: 'error', title: 'No se pudo eliminar', text: msg });
      });
    });
  });
  // Forzar 1–99 mientras escribe
const $cap = $('#capacidad_tipo_habitacion');
$cap.on('input', function() {
  let v = this.value.replace(/[^0-9]/g, '');
  if (v.length > 2) v = v.slice(0, 2);
  if (v !== '') {
    const n = Math.max(1, Math.min(99, parseInt(v, 10)));
    this.value = String(n);
  } else {
    this.value = '';
  }
});

});
</script>

