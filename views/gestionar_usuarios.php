<?php
// views/gestionar_usuarios.php — SOLO ADMIN
require_once '../config/auth.php';
require_admin();
require_once '../config/db.php';

$currentUserId = (int)($_SESSION['id_usuario'] ?? 0);

// Traer usuarios (incluye fecha_creacion)
$stmt = $pdo->query("
  SELECT id_usuario, nombre_usuario, apellido_usuario, correo_usuario, rol_usuario, fecha_creacion
  FROM usuarios
  ORDER BY fecha_creacion DESC
");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preparo dataset para DataTables (evita TN/4)
$rows = [];
foreach ($usuarios as $u) {
  $ts = strtotime($u['fecha_creacion'] ?: 'now');
  $rows[] = [
    'id'         => (int)$u['id_usuario'],
    'nombre'     => $u['nombre_usuario'],
    'apellido'   => $u['apellido_usuario'],
    'correo'     => $u['correo_usuario'],
    'rol'        => $u['rol_usuario'],             // 'Administrador' | 'Recepcionista'
    'fecha_iso'  => date('Y-m-d', $ts),            // para filtro rango
    'fecha_sort' => date('Y-m-d H:i:s', $ts),      // para orden
    'fecha_vis'  => date('d/m/Y H:i', $ts),        // visual
  ];
}
?>
<?php include 'header.php'; ?>

<!-- jQuery primero -->
<script src="../public/js/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" href="../public/css/datatables.min.css">
<script src="../public/js/datatables.min.js"></script>

<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="fas fa-users-cog me-2"></i>Gestionar Usuarios</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
      <i class="fas fa-user-plus me-1"></i>Nuevo usuario
    </button>
  </div>

  <?php
    $okFlag   = !empty($_GET['ok']);
    $errorMsg = $_GET['error'] ?? '';
  ?>
  <script src="../public/js/sweetalert2.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const ok = <?php echo $okFlag ? 'true' : 'false'; ?>;
      const errorMsg = <?php echo json_encode($errorMsg); ?>;
      const clearParams = () => {
        const url = new URL(window.location);
        url.searchParams.delete('ok'); url.searchParams.delete('error');
        window.history.replaceState({}, '', url);
      };
      if (ok) Swal.fire({icon:'success',title:'¡Listo!',text:'Operación realizada con éxito.'}).then(clearParams);
      else if (errorMsg) Swal.fire({icon:'error',title:'Ups…',text:errorMsg}).then(clearParams);
    });
  </script>

  <!-- Filtros (Rol y Rango de fechas) -->
  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-end">
        <div class="col-sm-4 col-md-3">
          <label class="form-label mb-1">Filtrar por rol</label>
          <select id="filtroRol" class="form-select form-select-sm">
            <option value="">Todos</option>
            <option value="Administrador">Administrador</option>
            <option value="Recepcionista">Recepcionista</option>
          </select>
        </div>
        <div class="col-sm-4 col-md-3">
          <label class="form-label mb-1">Desde (fecha creación)</label>
          <input id="filtroDesde" type="date" class="form-control form-control-sm">
        </div>
        <div class="col-sm-4 col-md-3">
          <label class="form-label mb-1">Hasta (fecha creación)</label>
          <input id="filtroHasta" type="date" class="form-control form-control-sm">
        </div>
        <div class="col-sm-12 col-md-3 text-sm-start text-md-end">
          <button id="btnLimpiarFiltros" class="btn btn-outline-secondary btn-sm">Limpiar filtros</button>
        </div>
      </div>
    </div>
  </div>

  <table id="tabla-usuarios" class="table table-striped table-bordered align-middle">
    <thead class="table-dark">
      <tr>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Correo</th>
        <th>Rol</th>
        <th>Fecha de creación</th>
        <th style="width:180px;">Acciones</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</div>

<!-- =========================
     MODALES GLOBALES
     ========================= -->

<!-- Nuevo -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="../controllers/guardar_usuario.php" method="POST" class="modal-content bg-white">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Nuevo usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Apellido</label>
            <input type="text" class="form-control" name="apellido" required>
          </div>
        </div>
        <div class="mt-2">
          <label class="form-label">Correo</label>
          <input type="email" class="form-control" name="correo" required>
        </div>
        <div class="mt-2">
          <label class="form-label">Rol</label>
          <select class="form-select" name="rol" required>
            <option value="" disabled selected>Seleccione</option>
            <option value="Administrador">Administrador</option>
            <option value="Recepcionista">Recepcionista</option>
          </select>
        </div>
        <div class="mt-2">
          <label class="form-label">Contraseña</label>
          <input type="password" class="form-control" name="contrasena" minlength="6" required>
          <div class="form-text">Mínimo 6 caracteres.</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- Editar -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="../controllers/guardar_usuario.php" method="POST" class="modal-content bg-white">
      <input type="hidden" name="id_usuario" id="edit_id_usuario">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Editar usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Apellido</label>
            <input type="text" class="form-control" name="apellido" id="edit_apellido" required>
          </div>
        </div>
        <div class="mt-2">
          <label class="form-label">Correo</label>
          <input type="email" class="form-control" name="correo" id="edit_correo" required>
        </div>
        <div class="mt-2">
          <label class="form-label">Rol</label>
          <select class="form-select" name="rol" id="edit_rol" required>
            <option value="Administrador">Administrador</option>
            <option value="Recepcionista">Recepcionista</option>
          </select>
          <div id="edit_rol_hint" class="form-text d-none">
            No puedes cambiar tu propio rol mientras estás conectado.
          </div>
        </div>
        <div class="form-text mt-2">
          La contraseña no se modifica aquí. Usa <strong><i class="fas fa-key"></i> Restablecer contraseña</strong>.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

<!-- Reset pass -->
<div class="modal fade" id="modalPasswordUsuario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="../controllers/reset_password_usuario.php" method="POST" class="modal-content bg-white">
      <input type="hidden" name="id_usuario" id="pwd_id_usuario">
      <div class="modal-header bg-warning">
        <h5 class="modal-title"><i class="fas fa-key me-2"></i>Restablecer contraseña</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
          <span class="small text-muted" id="pwd_usuario_nombre"></span>
        </div>
        <label class="form-label">Nueva contraseña</label>
        <input type="password" class="form-control" name="nueva_contrasena" minlength="6" required>
        <div class="form-text">Mínimo 6 caracteres.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- Eliminar -->
<div class="modal fade" id="modalEliminarUsuario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="../controllers/eliminar_usuario.php" method="POST" class="modal-content bg-white">
      <input type="hidden" name="id_usuario" id="del_id_usuario">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirmar eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p>Vas a eliminar al siguiente usuario:</p>
        <ul class="mb-0" style="list-style:none;padding-left:0;">
          <li><strong>Nombre:</strong> <span id="del_nombre"></span></li>
          <li><strong>Correo:</strong> <span id="del_correo"></span></li>
          <li><strong>Rol:</strong> <span id="del_rol"></span></li>
        </ul>
        <div class="alert alert-warning mt-3">
          <i class="fas fa-info-circle me-2"></i>Esta acción es permanente.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>Eliminar</button>
      </div>
    </form>
  </div>
</div>

<script>
  $(function () {
    const CURRENT_USER_ID = <?php echo $currentUserId; ?>;
    const dataset = <?php echo json_encode($rows, JSON_UNESCAPED_UNICODE); ?>;

    // Registrar filtro de fechas (usa los datos del row, NO el DOM)
    const $desde = $('#filtroDesde');
    const $hasta = $('#filtroHasta');
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
      if (settings.nTable.id !== 'tabla-usuarios') return true;
      const api = new $.fn.dataTable.Api(settings);
      const row = api.row(dataIndex).data(); // <- nuestro objeto
      if (!row || !row.fecha_iso) return true;

      const d = $desde.val(), h = $hasta.val();
      if (!d && !h) return true;
      if (d && row.fecha_iso < d) return false;
      if (h && row.fecha_iso > h) return false;
      return true;
    });

    const table = $('#tabla-usuarios').DataTable({
      language: { url: '../public/js/es-ES.json' },
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      data: dataset,
      order: [[4, 'desc']],
      columns: [
        { data: 'nombre' },
        { data: 'apellido' },
        { data: 'correo' },
        {
          data: 'rol',
          render: function (data, type) {
            if (type === 'display') {
              return data === 'Administrador'
                ? '<span class="badge bg-primary">Administrador</span>'
                : '<span class="badge bg-secondary">Recepcionista</span>';
            }
            return data; // para orden/búsqueda interna
          }
        },
        {
          data: 'fecha_vis',
          render: function (data, type, row) {
            if (type === 'sort' || type === 'type') return row.fecha_sort;
            if (type === 'filter') return row.fecha_vis;
            return row.fecha_vis; // display
          }
        },
        {
          data: null,
          orderable: false,
          searchable: false,
          render: function (data, type, row) {
            // Botones con data-* para rellenar modales
            const fullName = (row.nombre || '') + ' ' + (row.apellido || '');
            return `
              <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                data-bs-target="#modalEditarUsuario"
                data-id="${row.id}"
                data-nombre="${$('<div>').text(row.nombre||'').html()}"
                data-apellido="${$('<div>').text(row.apellido||'').html()}"
                data-correo="${$('<div>').text(row.correo||'').html()}"
                data-rol="${$('<div>').text(row.rol||'').html()}"
                title="Editar"><i class="fas fa-edit"></i></button>

              <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                data-bs-target="#modalPasswordUsuario"
                data-id="${row.id}"
                data-nombre="${$('<div>').text(fullName).html()}"
                title="Restablecer contraseña"><i class="fas fa-key"></i></button>

              <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                data-bs-target="#modalEliminarUsuario"
                data-id="${row.id}"
                data-nombre="${$('<div>').text(fullName).html()}"
                data-correo="${$('<div>').text(row.correo||'').html()}"
                data-rol="${$('<div>').text(row.rol||'').html()}"
                title="Eliminar usuario"><i class="fas fa-trash"></i></button>
            `;
          }
        }
      ]
    });

    // Filtro por Rol (columna 3)
    $('#filtroRol').on('change', function () {
      const val = $(this).val();
      if (val) table.column(3).search('^' + val + '$', true, false).draw();
      else table.column(3).search('').draw();
    });

    // Refiltrar al cambiar fechas
    const reFiltrarFecha = () => table.draw();
    $desde.on('change', reFiltrarFecha);
    $hasta.on('change', reFiltrarFecha);

    // Limpiar filtros
    $('#btnLimpiarFiltros').on('click', function () {
      $('#filtroRol').val('');
      $desde.val('');
      $hasta.val('');
      table.column(3).search('');
      table.draw();
    });

    // --- Relleno de modales ---
    const modalEdit = document.getElementById('modalEditarUsuario');
    if (modalEdit) {
      modalEdit.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget; if (!btn) return;
        const id  = parseInt(btn.getAttribute('data-id'), 10);
        $('#edit_id_usuario').val(id);
        $('#edit_nombre').val(btn.getAttribute('data-nombre') || '');
        $('#edit_apellido').val(btn.getAttribute('data-apellido') || '');
        $('#edit_correo').val(btn.getAttribute('data-correo') || '');
        const $rolSelect = $('#edit_rol'); const $rolHint = $('#edit_rol_hint');
        $rolSelect.val(btn.getAttribute('data-rol') || 'Recepcionista');
        const isSelf = (id === <?php echo $currentUserId; ?>);
        $rolSelect.prop('disabled', isSelf);
        $rolHint.toggleClass('d-none', !isSelf);
      });
    }

    const modalPwd = document.getElementById('modalPasswordUsuario');
    if (modalPwd) {
      modalPwd.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget; if (!btn) return;
        $('#pwd_id_usuario').val(btn.getAttribute('data-id'));
        $('#pwd_usuario_nombre').text(btn.getAttribute('data-nombre') || '');
      });
    }

    const modalDel = document.getElementById('modalEliminarUsuario');
    if (modalDel) {
      modalDel.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget; if (!btn) return;
        $('#del_id_usuario').val(btn.getAttribute('data-id'));
        $('#del_nombre').text(btn.getAttribute('data-nombre') || '');
        $('#del_correo').text(btn.getAttribute('data-correo') || '');
        $('#del_rol').text(btn.getAttribute('data-rol') || '');
      });
    }
  });
</script>
