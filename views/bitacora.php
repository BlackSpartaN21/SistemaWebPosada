<?php
// views/bitacora.php (filtros simplificados)
declare(strict_types=1);
session_start();
if (!isset($_SESSION['id_usuario'])) { header('Location: login.php'); exit; }
if (($_SESSION['rol_usuario'] ?? '') !== 'Administrador') { header('Location: recepcion.php'); exit; }

require_once '../config/db.php';
require_once 'header.php';

// Usuarios para filtro
$usuarios = $pdo->query("
  SELECT id_usuario, CONCAT(nombre_usuario, ' ', apellido_usuario) AS nombre, rol_usuario
  FROM usuarios
  ORDER BY nombre_usuario ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container-fluid mt-4">
  <h3 class="mb-3"><i class="fa-solid fa-scroll me-2"></i>Bitácora del sistema</h3>

  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <!-- Atajos rápidos de fechas -->
      <div class="d-flex flex-wrap gap-2 mb-3">
        <button type="button" class="btn btn-sm btn-outline-primary preset" data-preset="hoy">Hoy</button>
        <button type="button" class="btn btn-sm btn-outline-primary preset" data-preset="ayer">Ayer</button>
        <button type="button" class="btn btn-sm btn-outline-primary preset" data-preset="7">Últimos 7 días</button>
        <button type="button" class="btn btn-sm btn-outline-primary preset" data-preset="30">Últimos 30 días</button>
        <button type="button" class="btn btn-sm btn-outline-primary preset" data-preset="mes">Este mes</button>
        <button type="button" class="btn btn-sm btn-outline-secondary preset" data-preset="todo">Todo</button>
      </div>

      <!-- Filtros simples -->
      <form id="filtros" class="row g-3">
        <div class="col-6 col-md-3">
          <label class="form-label">Desde</label>
          <input type="date" name="desde" class="form-control">
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Hasta</label>
          <input type="date" name="hasta" class="form-control">
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label">Usuario</label>
          <select name="id_usuario" class="form-select">
            <option value="">Todos</option>
            <?php foreach ($usuarios as $u): ?>
              <option value="<?= (int)$u['id_usuario'] ?>">
                <?= htmlspecialchars($u['nombre'].' ('.$u['rol_usuario'].')', ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label">Resultado</label>
          <select name="resultado" class="form-select">
            <option value="">Todos</option>
            <option value="OK">OK</option>
            <option value="ERROR">ERROR</option>
          </select>
        </div>
        <div class="col-12 d-flex align-items-end gap-2">
          <button type="reset" id="btnLimpiar" class="btn btn-secondary">
            <i class="fa-solid fa-eraser me-1"></i> Limpiar filtros
          </button>
          <small class="text-muted ms-auto">
            Consejo: usa el buscador global (arriba) para filtrar por <em>módulo / acción / detalle / usuario</em>.
          </small>
        </div>
      </form>
    </div>
  </div>

  <!-- Controles de columnas anchas -->
  <div class="d-flex justify-content-end gap-2 mb-2">
    <button type="button" id="toggleIp" class="btn btn-outline-secondary btn-sm">
      <i class="fa-solid fa-eye-slash me-1"></i> Mostrar IP
    </button>
    <button type="button" id="toggleUa" class="btn btn-outline-secondary btn-sm">
      <i class="fa-solid fa-eye-slash me-1"></i> Mostrar Agente
    </button>
  </div>

  <div class="table-responsive">
    <table id="tablaBitacora" class="table table-striped table-hover w-100 align-middle">
      <thead class="table-dark">
        <tr>
          <th>Fecha</th>
          <th>Usuario</th>
          <th>Rol</th>
          <th>Módulo</th>
          <th>Acción</th>
          <th>Detalle</th>
          <th>Resultado</th>
          <th>IP</th>
          <th>Agente</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<!-- Modal Detalle (se mantiene por compatibilidad; no hay botón "Ver") -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa-solid fa-circle-info me-2"></i>Detalle del evento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <pre class="mb-0" id="detalleModalBody" style="white-space: pre-wrap; word-break: break-word;"></pre>
      </div>
    </div>
  </div>
</div>

<style>
  td.nowrap, th.nowrap { white-space: nowrap; }
  td.truncate { max-width: 360px; overflow: hidden; text-overflow: ellipsis; }
  @media (max-width: 1200px) { td.truncate { max-width: 260px; } }
  @media (max-width: 992px)  { td.truncate { max-width: 200px; } }
</style>

<script>
(function () {
  function ensureCSS(href) {
    if ([...document.styleSheets].some(s => s.href && s.href.includes(href))) return;
    const l = document.createElement('link'); l.rel = 'stylesheet'; l.href = href; document.head.appendChild(l);
  }
  function ensureScript(src) {
    return new Promise(resolve => {
      if ([...document.scripts].some(s => s.src && s.src.includes(src))) return resolve();
      const s = document.createElement('script'); s.src = src; s.onload = resolve; document.head.appendChild(s);
    });
  }

  const hasJQ = !!window.jQuery;
  const hasDT = hasJQ && !!$.fn.dataTable;

  const init = () => {
    const $ = window.jQuery;
    const colIdx = { fecha:0, usuario:1, rol:2, modulo:3, accion:4, detalle:5, resultado:6, ip:7, ua:8 };
    const langUrl = '../public/js/es-ES.json';

    const $tabla = $('#tablaBitacora').DataTable({
      processing: true,
      serverSide: true,
      deferRender: true,
      autoWidth: false,
      ajax: {
        url: '../controllers/bitacora_list.php',
        type: 'POST',
        data: function (d) {
          const f = Object.fromEntries(new FormData(document.getElementById('filtros')).entries());
          return Object.assign(d, f);
        }
      },
      order: [[colIdx.fecha, 'desc']],
      columns: [
        { data: 'fecha',
          className: 'nowrap',
          render: function (data, type) {
            if (type !== 'display' || !data) return data;
            const parts = String(data).split(' ');
            return parts.length === 2 ? (parts[0] + '<br><small class="text-muted">' + parts[1] + '</small>') : data;
          }
        },
        { data: 'usuario', className: 'truncate' },
        { data: 'rol',
          className: 'nowrap',
          render: function (data, type) {
            if (type !== 'display') return data;
            const t = (data || '').toString();
            const color = t === 'Administrador' ? 'bg-danger' : 'bg-secondary';
            return '<span class="badge '+color+'">'+ $('<div>').text(t).html() +'</span>';
          }
        },
        { data: 'modulo', className: 'nowrap' },
        { data: 'accion', className: 'nowrap' },
        { data: 'detalle',
          className: 'truncate',
          render: function (data, type) {
            if (type !== 'display') return data;
            if (!data || data === '—') return '—';
            let text = data;
            try { if (typeof data === 'string' && data.trim().startsWith('{')) { text = JSON.stringify(JSON.parse(data)); } } catch (e) {}
            const safe = $('<div>').text(text).html();
            const short = safe.length > 120 ? safe.slice(0, 120) + '…' : safe;
            return '<span title="'+safe+'">'+short+'</span>';
          }
        },
        { data: 'resultado',
          className: 'nowrap',
          render: function (data, type) {
            if (type !== 'display') return data;
            const ok = (data || '') === 'OK';
            return '<span class="badge '+ (ok ? 'bg-success' : 'bg-warning text-dark') +'">'+ (ok?'OK':'ERROR') +'</span>';
          }
        },
        { data: 'ip', className: 'nowrap' },
        { data: 'ua',
          className: 'truncate',
          render: function (data, type) {
            if (type !== 'display') return data;
            const safe = $('<div>').text(data || '').html();
            return safe.length > 60 ? ('<span title="'+safe+'">'+safe.slice(0,60)+'…</span>') : safe;
          }
        }
      ],
      columnDefs: [
        { targets: [colIdx.ip, colIdx.ua], visible: false },
        { targets: [colIdx.usuario, colIdx.detalle, colIdx.ua], width: '30%' },
        { targets: [colIdx.fecha, colIdx.rol, colIdx.modulo, colIdx.accion, colIdx.resultado, colIdx.ip], width: '1%' }
      ],
      pageLength: 25,
      language: { url: langUrl }
    });

    // Placeholder al buscador global de DataTables
    $('#tablaBitacora_filter input').attr('placeholder', 'Buscar módulo / acción / detalle / usuario');

    // Auto-reload al cambiar filtros simples
    $('#filtros').on('change', 'input, select', () => $tabla.ajax.reload());
    $('#btnLimpiar').on('click', () => setTimeout(() => $tabla.ajax.reload(), 0));

    // Toggles columnas
    $('#toggleIp').on('click', function(){
      const visible = $tabla.column(colIdx.ip).visible();
      $tabla.column(colIdx.ip).visible(!visible);
      $(this).html(visible
        ? '<i class="fa-solid fa-eye-slash me-1"></i> Mostrar IP'
        : '<i class="fa-solid fa-eye me-1"></i> Ocultar IP'
      );
    });
    $('#toggleUa').on('click', function(){
      const visible = $tabla.column(colIdx.ua).visible();
      $tabla.column(colIdx.ua).visible(!visible);
      $(this).html(visible
        ? '<i class="fa-solid fa-eye-slash me-1"></i> Mostrar Agente'
        : '<i class="fa-solid fa-eye me-1"></i> Ocultar Agente'
      );
    });

    // Presets de fechas
    function toISODate(d){ const z=n=>String(n).padStart(2,'0'); return d.getFullYear()+'-'+z(d.getMonth()+1)+'-'+z(d.getDate()); }
    function setRange(desde, hasta){
      const f = document.getElementById('filtros');
      f.desde.value = desde || ''; f.hasta.value = hasta || '';
      $tabla.ajax.reload();
    }
    $('.preset').on('click', function(){
      const p = $(this).data('preset'); const now = new Date();
      const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      if (p==='hoy') setRange(toISODate(today), toISODate(today));
      else if (p==='ayer') { const y=new Date(today); y.setDate(y.getDate()-1); setRange(toISODate(y), toISODate(y)); }
      else if (p==='7') { const d=new Date(today); d.setDate(d.getDate()-6); setRange(toISODate(d), toISODate(today)); }
      else if (p==='30') { const d=new Date(today); d.setDate(d.getDate()-29); setRange(toISODate(d), toISODate(today)); }
      else if (p==='mes') { const d=new Date(today.getFullYear(), today.getMonth(), 1); setRange(toISODate(d), toISODate(today)); }
      else if (p==='todo') setRange('', '');
    });
  };

  (async () => {
    if (!hasJQ) await ensureScript('../public/js/jquery-3.7.1.min.js');
    if (!hasDT) { ensureCSS('../public/css/datatables.min.css'); await ensureScript('../public/js/datatables.min.js'); }
    init();
  })();
})();
</script>
