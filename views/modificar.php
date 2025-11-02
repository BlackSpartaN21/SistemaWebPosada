<?php
// views/modificar.php
session_start();
require_once '../config/auth.php';
require_login();
require_once '../config/db.php';

// CSRF para edición
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf'];

function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

// Cargar clientes
try {
  $stmt = $pdo->query("
    SELECT 
      tipo_documento_cliente,
      documento_cliente,
      nombres_cliente,
      apellidos_cliente,
      telefono_cliente,
      correo_cliente,
      descripcion_cliente,
      fecha_creacion_cliente
    FROM clientes
    ORDER BY fecha_creacion_cliente DESC
  ");
  $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $th) {
  $clientes = [];
}

// SweetAlert flash
$alert_type = $_GET['alert'] ?? ($_SESSION['flash_alert_type'] ?? null);
$alert_msg  = $_GET['msg']   ?? ($_SESSION['flash_alert_msg']   ?? null);
unset($_SESSION['flash_alert_type'], $_SESSION['flash_alert_msg']);

// Header (trae la misma modal #clienteModal)
include 'header.php';
?>

<!-- DataTables locales -->
<link rel="stylesheet" href="../public/css/datatables.min.css">
<script>
  if (!window.jQuery) {
    document.write('<script src="../public/js/jquery-3.7.1.min.js"><\/script>');
  }
</script>
<script src="../public/js/datatables.min.js"></script>

<!-- Ensanchar al 100% y sin card -->
<style>
  .container-fluid-wide { padding-left: .75rem; padding-right: .75rem; }
  @media (min-width: 768px) { .container-fluid-wide { padding-left: 1.5rem; padding-right: 1.5rem; } }
  /* Asegura ancho completo del wrapper y la tabla */
  div.dataTables_wrapper { width: 100%; }
  table.dataTable { width: 100% !important; }
</style>

<div class="container-fluid container-fluid-wide my-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="fas fa-users-cog me-2"></i>Modificar clientes</h4>
    <button type="button" class="btn btn-success" id="btnNuevoCliente" data-bs-toggle="modal" data-bs-target="#clienteModal">
      <i class="fas fa-user-plus"></i> Nuevo cliente
    </button>
  </div>

  <!-- SIN card, SIN .table-responsive -->
  <table id="tablaClientes" class="table table-striped table-bordered table-hover align-middle mb-0 w-100">
    <thead class="table-dark">
      <tr>
        <th>Tipo</th>
        <th>Documento</th>
        <th>Nombres</th>
        <th>Apellidos</th>
        <th>Teléfono</th>
        <th>Correo</th>
        <th>Descripción</th>
        <th>Fecha Registro</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($clientes as $c):
      $ts   = strtotime($c['fecha_creacion_cliente']);
      $iso  = $ts ? date('Y-m-d H:i:s', $ts) : '';
      $disp = $ts ? date('d-m-Y h:i A', $ts) : '';
    ?>
      <tr>
        <td class="text-center"><?= e($c['tipo_documento_cliente']) ?></td>
        <td class="text-center"><?= e($c['documento_cliente']) ?></td>
        <td class="text-center"><?= e($c['nombres_cliente']) ?></td>
        <td class="text-center"><?= e($c['apellidos_cliente']) ?></td>
        <td class="text-center"><?= e($c['telefono_cliente']) ?></td>
        <td class="text-center"><?= e($c['correo_cliente']) ?></td>
        <td class="text-center"><?= e($c['descripcion_cliente']) ?></td>
        <td class="text-center" data-order="<?= e($iso) ?>" data-search="<?= e($iso) ?>">
          <?= e($disp) ?>
        </td>
        <td class="text-center">
          <button
            type="button"
            class="btn btn-sm btn-warning btn-editar-cliente"
            data-bs-toggle="modal"
            data-bs-target="#clienteModal"
            data-tipo="<?= e($c['tipo_documento_cliente']) ?>"
            data-documento="<?= e($c['documento_cliente']) ?>"
            data-nombres="<?= e($c['nombres_cliente']) ?>"
            data-apellidos="<?= e($c['apellidos_cliente']) ?>"
            data-telefono="<?= e($c['telefono_cliente']) ?>"
            data-correo="<?= e($c['correo_cliente']) ?>"
            data-descripcion="<?= e($c['descripcion_cliente']) ?>"
          >
            <i class="fas fa-edit"></i> Editar
          </button>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- SweetAlert2 local (si ya está en header.php, puedes quitar) -->
<script src="../public/js/sweetalert2.min.js"></script>

<script>
// ===== Helpers de validación =====
function soloNumeros(v){ return /^[0-9]+$/.test(v); }
function soloLetrasEspacios(v){ return /^[A-Za-zÁÉÍÓÚÑáéíóúñ ]+$/.test(v); }
function validarEmail(v){ return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); }
function resetValidacion(form){
  form.querySelectorAll('.is-invalid,.is-valid').forEach(el=>el.classList.remove('is-invalid','is-valid'));
}

// ===== Modal: modo CREAR =====
function setModalCrear(){
  const form  = document.getElementById('clienteForm');
  const label = document.getElementById('clienteModalLabel');
  const btn   = form?.querySelector('button[type="submit"]');
  if (!form) return;

  label.textContent = 'Datos del Cliente';
  form.setAttribute('action', '../controllers/registrar.php');

  form.querySelector('input[name="documento_original"]')?.remove();
  form.querySelector('input[name="action"]')?.remove();

  if (btn) btn.innerHTML = '<i class="fas fa-save"></i> Guardar';
  form.reset();
  resetValidacion(form);
}

// ===== Modal: modo EDICIÓN =====
function setModalEdicion(data){
  const form  = document.getElementById('clienteForm');
  const label = document.getElementById('clienteModalLabel');
  const btn   = form?.querySelector('button[type="submit"]');
  if (!form) return;

  form.setAttribute('action', '../controllers/cliente.php');

  let hAction = form.querySelector('input[name="action"]');
  if (!hAction){
    hAction = document.createElement('input');
    hAction.type = 'hidden';
    hAction.name = 'action';
    form.appendChild(hAction);
  }
  hAction.value = 'update';

  let hOrig = form.querySelector('input[name="documento_original"]');
  if (!hOrig){
    hOrig = document.createElement('input');
    hOrig.type = 'hidden';
    hOrig.name = 'documento_original';
    form.appendChild(hOrig);
  }
  hOrig.value = data.documento || '';

  let hCsrf = form.querySelector('input[name="csrf"]');
  if (!hCsrf){
    hCsrf = document.createElement('input');
    hCsrf.type = 'hidden';
    hCsrf.name = 'csrf';
    form.appendChild(hCsrf);
  }
  hCsrf.value = '<?= e($csrf) ?>';

  label.textContent = 'Editar Cliente';
  if (btn) btn.innerHTML = '<i class="fas fa-sync-alt"></i> Actualizar';

  document.getElementById('tipo_documento_cliente').value = data.tipo || 'V';
  document.getElementById('documento_cliente').value      = data.documento || '';
  document.getElementById('nombres_cliente').value        = data.nombres || '';
  document.getElementById('apellidos_cliente').value      = data.apellidos || '';
  document.getElementById('telefono_cliente').value       = data.telefono || '';
  document.getElementById('correo_cliente').value         = data.correo || '';
  document.getElementById('descripcion_cliente').value    = data.descripcion || '';

  resetValidacion(form);
}

// ===== Validaciones en vivo =====
(function attachValidations(){
  const f    = document.getElementById('clienteForm');
  if (!f) return;
  const doc  = document.getElementById('documento_cliente');
  const tel  = document.getElementById('telefono_cliente');
  const nom  = document.getElementById('nombres_cliente');
  const ape  = document.getElementById('apellidos_cliente');
  const mail = document.getElementById('correo_cliente');
  const des  = document.getElementById('descripcion_cliente');

  doc?.addEventListener('input', ()=>{ const ok = soloNumeros(doc.value) && doc.value.length <= 10; doc.classList.toggle('is-invalid', !ok); doc.classList.toggle('is-valid', ok); });
  tel?.addEventListener('input', ()=>{ const ok = soloNumeros(tel.value) && tel.value.length === 11; tel.classList.toggle('is-invalid', !ok); tel.classList.toggle('is-valid', ok); });
  nom?.addEventListener('input', ()=>{ const ok = nom.value.length <= 50 && soloLetrasEspacios(nom.value); nom.classList.toggle('is-invalid', !ok); nom.classList.toggle('is-valid', ok); });
  ape?.addEventListener('input', ()=>{ const ok = ape.value.length <= 50 && soloLetrasEspacios(ape.value); ape.classList.toggle('is-invalid', !ok); ape.classList.toggle('is-valid', ok); });
  mail?.addEventListener('input',()=>{ const ok = validarEmail(mail.value); mail.classList.toggle('is-invalid', !ok); mail.classList.toggle('is-valid', ok); });
  des?.addEventListener('input', ()=>{ const ok = des.value.length <= 100; des.classList.toggle('is-invalid', !ok); des.classList.toggle('is-valid', ok); });

  f.addEventListener('submit', (ev)=>{
    let ok = true;
    if (doc){ const v = soloNumeros(doc.value) && doc.value.length <= 10; if(!v){ok=false; doc.classList.add('is-invalid');} }
    if (tel){ const v = soloNumeros(tel.value) && tel.value.length === 11; if(!v){ok=false; tel.classList.add('is-invalid');} }
    if (nom){ const v = nom.value.length <= 50 && soloLetrasEspacios(nom.value); if(!v){ok=false; nom.classList.add('is-invalid');} }
    if (ape){ const v = ape.value.length <= 50 && soloLetrasEspacios(ape.value); if(!v){ok=false; ape.classList.add('is-invalid');} }
    if (mail){ const v = validarEmail(mail.value); if(!v){ok=false; mail.classList.add('is-invalid');} }
    if (des){ const v = des.value.length <= 100; if(!v){ok=false; des.classList.add('is-invalid');} }

    if (!ok) {
      ev.preventDefault();
      Swal.fire({ icon:'error', title:'Datos inválidos', text:'Revisa los campos marcados en rojo.' });
    }
  });
})();

// ===== Botones: Editar / Nuevo =====
document.addEventListener('click', (e)=>{
  const btn = e.target.closest('.btn-editar-cliente');
  if (btn){
    const data = {
      tipo:        btn.getAttribute('data-tipo'),
      documento:   btn.getAttribute('data-documento'),
      nombres:     btn.getAttribute('data-nombres'),
      apellidos:   btn.getAttribute('data-apellidos'),
      telefono:    btn.getAttribute('data-telefono'),
      correo:      btn.getAttribute('data-correo'),
      descripcion: btn.getAttribute('data-descripcion')
    };
    setModalEdicion(data);
  }
});
document.getElementById('btnNuevoCliente')?.addEventListener('click', ()=> setModalCrear());
document.getElementById('clienteModal')?.addEventListener('hidden.bs.modal', ()=> setModalCrear());

// ===== DataTables (ancho completo, sin scroll X) =====
$(function(){
  if ($.fn.DataTable) {
    $('#tablaClientes').DataTable({
      order: [[7,'desc']],
      pageLength: 10,
      lengthMenu: [5,10,25,50,100],
      language: { url: '../public/js/es-ES.json' },
      autoWidth: false,
      scrollX: false,
      columnDefs: [{ targets: '_all', className: 'align-middle text-center' }]
    });
  } else {
    console.warn('DataTables no está disponible. Verifica ../public/js/datatables.min.js');
  }
});

// ===== SweetAlert (flash) =====
(function(){
  const type = '<?= e($alert_type) ?>';
  const msg  = '<?= e($alert_msg) ?>';
  if (!type || !msg) return;
  const icon = (type === 'success' ? 'success' :
               (type === 'error'   ? 'error'   :
               (type === 'warning' ? 'warning' : 'info')));
  Swal.fire({ icon, title: msg, timer: 2200, showConfirmButton: false });
})();
</script>
