<?php
// views/backup.php
session_start();

// Solo requiere sesión (sin filtro de rol)
if (!isset($_SESSION['id_usuario'])) {
  header('Location: login.php?error=Inicia%20sesion');
  exit;
}
?>
<?php include 'header.php'; ?>

<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">Copia de seguridad</h3>
    <div class="d-flex gap-2">
      <button id="btnCrear" class="btn btn-primary">
        <i class="fa fa-database me-1"></i> Crear copia ahora
      </button>
      <label class="btn btn-outline-secondary mb-0">
        <i class="fa fa-upload me-1"></i> Restaurar desde .sql
        <input type="file" id="fileRestore" accept=".sql" hidden>
      </label>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle mb-0" id="tablaBackups">
          <thead class="table-dark">
            <tr>
              <th>Archivo</th>
              <th>Tamaño</th>
              <th>Fecha</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<script>
(function () {
  // Ruta al controlador
  const RUTA_CTRL = '../controllers/backup.php';

  // Helpers UI (SweetAlert si está, si no fallback)
  const UI = {
    ok(t, m){ if (window.Swal) Swal.fire(t, m, 'success'); else alert((t||'OK') + '\n' + (m||'')); },
    err(t, m){ if (window.Swal) Swal.fire(t||'Error', m||'Error', 'error'); else alert('Error\n' + (m||'')); },
    ask(opts){
      if (window.Swal) {
        return Swal.fire(Object.assign({
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Aceptar',
          cancelButtonText: 'Cancelar'
        }, opts));
      }
      const ok = confirm((opts.title||'Confirmar') + (opts.text?('\n\n'+opts.text):''));
      return Promise.resolve({ isConfirmed: ok });
    },
    loading(on){
      if (window.Swal) {
        if (on) Swal.showLoading();
        else Swal.close();
      }
    }
  };

  // Helpers formato
  function bytesToHuman(bytes){
    if (bytes < 1024) return bytes + ' B';
    const u = ['KB', 'MB', 'GB', 'TB'];
    let i = -1;
    do { bytes /= 1024; i++; } while (bytes >= 1024 && i < u.length - 1);
    return bytes.toFixed(2) + ' ' + u[i];
  }
  function fechaHumana(ts){
    const d = new Date(ts * 1000);
    return d.toLocaleString();
  }

  // Peticiones (fetch JSON con manejo básico de errores)
  async function getJSON(url){
    const r = await fetch(url, { credentials: 'same-origin' });
    const text = await r.text();
    try {
      const json = JSON.parse(text);
      if (!r.ok || json.ok === false) throw new Error(json.msg || 'Error');
      return json;
    } catch (e) {
      throw new Error(text || e.message);
    }
  }

  async function postForm(url, formData){
    const r = await fetch(url, { method: 'POST', body: formData, credentials: 'same-origin' });
    const text = await r.text();
    try {
      const json = JSON.parse(text);
      if (!r.ok || json.ok === false) throw new Error(json.msg || 'Error');
      return json;
    } catch (e) {
      throw new Error(text || e.message);
    }
  }

  // Render tabla
  function renderLista(files){
    const tbody = document.querySelector('#tablaBackups tbody');
    tbody.innerHTML = '';
    if (!files || !files.length){
      tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Sin copias aún</td></tr>';
      return;
    }
    files.forEach(f => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><i class="fa fa-file-code me-1"></i>${f.name}</td>
        <td>${bytesToHuman(f.size)}</td>
        <td>${fechaHumana(f.mtime)}</td>
        <td class="text-center">
          <div class="btn-group btn-group-sm" role="group">
            <a class="btn btn-outline-primary" href="${RUTA_CTRL}?action=download&file=${encodeURIComponent(f.name)}" title="Descargar">
              <i class="fa fa-download"></i>
            </a>
            <button class="btn btn-outline-success btn-restore" data-file="${f.name}" title="Restaurar">
              <i class="fa fa-rotate-left"></i>
            </button>
            <button class="btn btn-outline-danger btn-del" data-file="${f.name}" title="Eliminar">
              <i class="fa fa-trash"></i>
            </button>
          </div>
        </td>
      `;
      tbody.appendChild(tr);
    });
  }

  async function cargarLista(){
    try {
      const r = await getJSON(RUTA_CTRL + '?action=list');
      renderLista(r.files || []);
    } catch (e) {
      UI.err('Error al listar', e.message?.slice(0, 500));
    }
  }

  // Eventos
  document.addEventListener('click', async (ev) => {
    const del = ev.target.closest('.btn-del');
    const restore = ev.target.closest('.btn-restore');
    if (del) {
      const file = del.getAttribute('data-file');
      const conf = await UI.ask({ title: 'Eliminar copia', text: file });
      if (!conf.isConfirmed) return;
      try {
        const fd = new FormData();
        fd.append('file', file);
        const r = await postForm(RUTA_CTRL + '?action=delete', fd);
        UI.ok('Eliminado', r.msg || 'Listo');
        cargarLista();
      } catch (e) {
        UI.err('Error', e.message?.slice(0, 500));
      }
    }
    if (restore) {
      const file = restore.getAttribute('data-file');
      const conf = await UI.ask({ title: 'Restaurar desde copia', text: `${file}\nEsto puede sobrescribir datos existentes.` });
      if (!conf.isConfirmed) return;
      try {
        // Descarga el .sql y lo reenvía como FormData
        const resp = await fetch(RUTA_CTRL + '?action=download&file=' + encodeURIComponent(file), { credentials: 'same-origin' });
        if (!resp.ok) throw new Error('No se pudo descargar el archivo');
        const blob = await resp.blob();
        const fd = new FormData();
        fd.append('sql_file', blob, file);
        const r2 = await postForm(RUTA_CTRL + '?action=restore', fd);
        UI.ok('¡Restaurado!', r2.msg || 'Restauración completada');
      } catch (e) {
        UI.err('Error', e.message?.slice(0, 500));
      }
    }
  });

  // Botón crear
  const btnCrear = document.getElementById('btnCrear');
  if (btnCrear) {
    btnCrear.addEventListener('click', async () => {
      const conf = await UI.ask({
        title: 'Crear copia de seguridad',
        text: 'Se exportará la base de datos completa.'
      });
      if (!conf.isConfirmed) return;
      try {
        UI.loading(true);
        const r = await getJSON(RUTA_CTRL + '?action=create');
        UI.loading(false);
        UI.ok('¡Listo!', 'Copia creada: ' + r.file + (r.path ? ('\n' + r.path) : ''));
        cargarLista();
      } catch (e) {
        UI.loading(false);
        UI.err('Error', e.message?.slice(0, 500));
      }
    });
  }

  // Input restaurar archivo manual
  const fileRestore = document.getElementById('fileRestore');
  if (fileRestore) {
    fileRestore.addEventListener('change', async function(){
      const file = this.files && this.files[0];
      if (!file) return;
      const conf = await UI.ask({
        title: 'Restaurar base de datos',
        text: 'Esto puede sobrescribir datos existentes. ¿Deseas continuar?'
      });
      if (!conf.isConfirmed) { this.value = ''; return; }
      try {
        UI.loading(true);
        const fd = new FormData();
        fd.append('sql_file', file, file.name);
        const r = await postForm(RUTA_CTRL + '?action=restore', fd);
        UI.loading(false);
        UI.ok('¡Restaurado!', r.msg || 'Restauración completada');
      } catch (e) {
        UI.loading(false);
        UI.err('Error', e.message?.slice(0, 600));
      } finally {
        this.value = '';
      }
    });
  }

  // Cargar al entrar
  document.addEventListener('DOMContentLoaded', cargarLista);
})();
</script>


