<?php
// Tab: Gestión de Tarifas
// Requiere: ../controllers/tarifas_controller.php (endpoints: list, create, update, delete, tipos)
// Dependencias en la página contenedora: jQuery, Bootstrap (bundle), DataTables, SweetAlert2,
// y el archivo de idioma ../public/js/es-ES.json
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Tarifas</h5>
  <button class="btn btn-success" id="btnNuevaTarifa">
    <i class="fas fa-plus-circle me-1"></i>Nueva tarifa
  </button>
</div>

<table id="tablaTarifas" class="table table-striped table-bordered w-100">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Tipo de habitación</th>
      <th>Tipo de tarifa</th>
      <th>Precio</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>

<!-- Modal: Crear/Editar Tarifa -->
<div class="modal fade" id="modalTarifa" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formTarifa" class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalTarifaLabel">Nueva tarifa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_tarifa" id="id_tarifa">

        <div class="mb-3">
          <label class="form-label" for="id_tipo_habitacion"><i class="fas fa-bed me-1"></i>Tipo de habitación</label>
          <select class="form-select" name="id_tipo_habitacion" id="id_tipo_habitacion" required></select>
        </div>

        <div class="mb-3">
          <label class="form-label" for="tipo_tarifa"><i class="fas fa-clock me-1"></i>Tipo de tarifa</label>
          <select class="form-select" name="tipo_tarifa" id="tipo_tarifa" required>
            <option value="3 Horas">3 Horas</option>
            <option value="24 Horas">24 Horas</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label" for="precio_tarifa"><i class="fas fa-money-bill-wave me-1"></i>Precio</label>
          <input type="number" class="form-control" name="precio_tarifa" id="precio_tarifa" min="0" step="0.01" required inputmode="decimal">
          <div class="form-text">Usa punto o coma. Se guardará con 2 decimales.</div>
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
  const $table = $('#tablaTarifas');
  const controller = '../controllers/tarifas_controller.php';
  const $modal = $('#modalTarifa');
  const modal = new bootstrap.Modal($modal[0]);

  // Evitar stacking raro (mueve el modal al <body>)
  $modal.appendTo('body');

  // Referencia SIEMPRE al select dentro del modal de tarifas (evita colisión con otros modales)
  const $selTipo = () => $modal.find('#id_tipo_habitacion');

  // Cargar tipos desde el backend (con selección opcional)
  function cargarTipos(selectedVal) {
    const $sel = $selTipo();
    $sel.empty().append('<option value="">Cargando...</option>');
    return $.getJSON(controller, { action: 'tipos' })
      .done(res => {
        $sel.empty();
        if (res && res.ok && Array.isArray(res.data) && res.data.length) {
          res.data.forEach(t => {
            $sel.append(
              $('<option>', { value: t.id_tipo_habitacion })
                .text(`${t.nombre_tipo_habitacion} (cap: ${t.capacidad_tipo_habitacion})`)
            );
          });
          if (selectedVal != null) $sel.val(String(selectedVal));
        } else {
          $sel.append('<option value="">No hay tipos disponibles</option>');
        }
      })
      .fail(() => {
        $sel.empty().append('<option value="">Error cargando tipos</option>');
      });
  }

  const dt = $table.DataTable({
    ajax: { url: controller, data: { action: 'list' }, dataSrc: res => res && res.ok ? res.data : [] },
    language: { url: '../public/js/es-ES.json' },
    pageLength: 25,
    lengthMenu: [[10, 25, 50, 100, -1],[10, 25, 50, 100, 'Todos']],
    dom:
      "<'row align-items-center mb-2'<'col-12 col-md-6'l><'col-12 col-md-6 text-md-end'f>>" +
      "t" +
      "<'row align-items-center mt-2'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
    columns: [
      { data: 'id_tarifa' },
      { data: 'nombre_tipo_habitacion' },
      { data: 'tipo_tarifa' },
      { data: 'precio_tarifa',
        render: v => {
          const n = Number(String(v).replace(',', '.'));
          return Number.isFinite(n) ? n.toFixed(2) : (v ?? '');
        }
      },
      {
        data: null,
        orderable: false,
        render: (row) => `
          <div class="btn-group btn-group-sm" role="group">
            <button class="btn btn-outline-primary btn-edit" title="Editar"><i class="fas fa-pen-to-square"></i></button>
            <button class="btn btn-outline-danger btn-del" title="Eliminar"><i class="fas fa-trash"></i></button>
          </div>`
      }
    ],
    columnDefs: [
      { targets: 0, visible: false, searchable: false },     // oculta ID
      { targets: [1,2], className: 'text-center' },           // texto a la izq
      { targets: [3], className: 'text-center' },               // precio a la der
      { targets: [4], className: 'text-center' }             // acciones centradas
    ],
    order: [[1, 'asc']]
  });

  let editId = 0;

  // Nuevo
  $('#btnNuevaTarifa').on('click', () => {
    editId = 0;
    $('#modalTarifaLabel').text('Nueva tarifa');
    $('#formTarifa')[0].reset();
    $('#id_tarifa').val('');
    cargarTipos().then(() => {
      modal.show();
    });
  });

  // Editar
  $table.on('click', '.btn-edit', function() {
    const data = dt.row($(this).closest('tr')).data();
    editId = Number(data.id_tarifa) || 0;

    $('#modalTarifaLabel').text(`Editar tarifa #${editId}`);
    $('#id_tarifa').val(editId);
    $('#tipo_tarifa').val(data.tipo_tarifa);
    $('#precio_tarifa').val(Number(data.precio_tarifa).toFixed(2));

    cargarTipos(data.id_tipo_habitacion).then(() => {
      modal.show();
    });
  });

  // Guardar (create/update)
  $('#formTarifa').on('submit', function(e) {
    e.preventDefault();

    const fd = Object.fromEntries(new FormData(this).entries());
    const id = Number(fd.id_tarifa || editId || 0);
    fd.id_tarifa = id > 0 ? id : '';
    fd.action = id > 0 ? 'update' : 'create';

    // Validación de precio
    let precio = String(fd.precio_tarifa || '').replace(',', '.');
    if (precio === '' || isNaN(precio)) {
      Swal.fire({ icon: 'error', title: 'Precio inválido', text: 'El precio debe ser numérico.' });
      return;
    }
    const n = Math.round(parseFloat(precio) * 100) / 100;
    if (!Number.isFinite(n) || n < 0) {
      Swal.fire({ icon: 'error', title: 'Precio inválido', text: 'El precio no puede ser negativo.' });
      return;
    }
    fd.precio_tarifa = n.toFixed(2);

    $.post(controller, fd, function(res) {
      if (res && res.ok) {
        modal.hide();
        dt.ajax.reload(null, false);
        Swal.fire({ icon: 'success', title: 'Guardado', text: id > 0 ? 'Tarifa actualizada.' : 'Tarifa creada.' });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: (res && res.error) || 'No se pudo guardar.' });
      }
    }, 'json').fail(xhr => {
      const res = xhr.responseJSON || {};
      Swal.fire({ icon: 'error', title: 'Error', text: res.error || 'Error del servidor.' });
    });
  });

  // Eliminar
  $table.on('click', '.btn-del', function() {
    const row = dt.row($(this).closest('tr')).data();
    Swal.fire({
      icon: 'warning', title: 'Eliminar tarifa',
      html: `Se eliminará la tarifa <b>${row.tipo_tarifa}</b> del tipo <b>${row.nombre_tipo_habitacion}</b>.`,
      showCancelButton: true, confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar'
    }).then(r => {
      if (!r.isConfirmed) return;

      $.post(controller, { action: 'delete', id_tarifa: row.id_tarifa }, function(res) {
        if (res && res.ok) {
          dt.ajax.reload(null, false);
          Swal.fire({ icon: 'success', title: 'Eliminada', text: 'Operación completada.' });
        } else {
          Swal.fire({ icon: 'error', title: 'No se pudo eliminar', text: (res && res.error) || 'Error desconocido.' });
        }
      }, 'json').fail(xhr => {
        const res = xhr.responseJSON || {};
        Swal.fire({ icon: 'error', title: 'No se pudo eliminar', text: res.error || 'Error del servidor.' });
      });
    });
  });
});
</script>
