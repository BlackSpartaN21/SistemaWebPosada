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
  <h2 class="mb-4">
    <i class="fas fa-history me-2"></i>Bitácora del Sistema

    <span class="module-help"
          tabindex="0"
          role="button"
          aria-expanded="false"
          data-help="Desde este módulo puedes consultar el historial de actividades realizadas por los usuarios, identificar quién ejecutó cada acción y revisar información como la fecha, hora y descripción de los eventos registrados en el sistema.">
        <i class="fas fa-question-circle"></i>
    </span>
</h2>

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

  td.detail-cell {
    min-width: 260px;
    max-width: 420px;
    white-space: normal;
  }

  .detail-preview {
    display: flex;
    flex-direction: column;
    gap: 4px;
    color: #495057;
    font-size: 0.88rem;
    line-height: 1.4;
  }

  .detail-preview-item {
    display: block;
    overflow-wrap: anywhere;
  }

  .detail-preview-item strong {
    color: #343a40;
  }

  .btn-ver-detalle {
    border-radius: 6px;
    font-size: 0.78rem;
    padding: 3px 9px;
  }

  #detalleModalBody {
    font-family: inherit;
    font-size: 0.92rem;
    line-height: 1.65;
    color: #343a40;
  }

    .module-help {
    position: relative;
    display: inline-flex;
    align-items: center;
    color: #a0a0a0;
    font-size: 17px;
    margin-left: 5px;
    cursor: help;
    vertical-align: middle;
    transition: color 0.2s ease;
}

.module-help:hover {
    color: #0d6efd;
}

.module-help:focus:not(:hover) {
    color: #a0a0a0;
    outline: none;
}

.module-help::after {
    content: attr(data-help);
    position: absolute;
    z-index: 1080;
    top: calc(100% + 12px);
    left: 50%;
    transform: translate(-50%, -5px);
    width: 290px;
    padding: 12px 15px;
    background: #ffffff;
    color: #444;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    font-size: 13px;
    font-weight: normal;
    line-height: 1.5;
    text-align: left;
    white-space: normal;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: all 0.2s ease;
}

.module-help:hover::after,
.module-help.active::after {
    opacity: 1;
    visibility: visible;
    transform: translate(-50%, 0);
}
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

    // Convierte nombres técnicos (id_reserva, fecha_inicio) en etiquetas legibles.
    function humanizeKey(key) {
      const labels = {
        id_usuario: 'Usuario ID',
        id_cliente: 'Cliente ID',
        id_reserva: 'Reserva ID',
        id_habitacion: 'Habitación ID',
        id_tarifa: 'Tarifa ID',
        documento_cliente: 'Documento del cliente',
        nombre_cliente: 'Nombre del cliente',
        nombre_usuario: 'Usuario',
        fecha_entrada: 'Fecha de entrada',
        fecha_salida: 'Fecha de salida',
        metodo_pago: 'Método de pago',
        estado_reserva: 'Estado de la reservación'
      };

      const original = String(key ?? '');
      if (labels[original.toLowerCase()]) return labels[original.toLowerCase()];

      return original
        .replace(/([a-z])([A-Z])/g, '$1 $2')
        .replace(/[_-]+/g, ' ')
        .replace(/\s+/g, ' ')
        .trim()
        .replace(/^./, c => c.toUpperCase());
    }

    function parseDetail(raw) {
      if (raw === null || raw === undefined || raw === '') return 'Sin detalles adicionales';
      if (typeof raw !== 'string') return raw;

      const trimmed = raw.trim();
      if (!trimmed) return 'Sin detalles adicionales';

      try {
        return JSON.parse(trimmed);
      } catch (e) {
        return raw;
      }
    }

    function readableValue(value) {
      if (value === null || value === undefined || value === '') return 'No especificado';
      if (typeof value === 'boolean') return value ? 'Sí' : 'No';
      if (typeof value === 'object') return JSON.stringify(value, null, 2);
      return String(value);
    }

    function formatDetailPreview(raw) {
      const detail = parseDetail(raw);
      if (typeof detail === 'object' && detail !== null && !Array.isArray(detail)) {
        const entries = Object.entries(detail);
        if (!entries.length) return '<span class="text-muted">Sin información adicional</span>';

        const summary = entries.slice(0, 2).map(([key, value]) => {
          const valueText = (value !== null && typeof value === 'object')
            ? (Array.isArray(value) ? `(${value.length} elementos)` : 'Información adicional')
            : readableValue(value);
          return `<span class="detail-preview-item"><strong>${$('<div>').text(humanizeKey(key)).html()}:</strong> ${$('<div>').text(valueText).html()}</span>`;
        }).join('');

        return `<div class="detail-preview">${summary}</div><button type="button" class="btn btn-sm btn-outline-primary btn-ver-detalle mt-1"><i class="fas fa-eye me-1"></i>Ver detalles</button>`;
      }

      const text = readableValue(detail);
      const short = text.length > 95 ? text.slice(0, 95) + '…' : text;
      return `<div class="detail-preview">${$('<div>').text(short).html()}</div><button type="button" class="btn btn-sm btn-outline-primary btn-ver-detalle mt-1"><i class="fas fa-eye me-1"></i>Ver detalles</button>`;
    }

    function formatDetailFull(raw) {
      const detail = parseDetail(raw);

      if (typeof detail === 'object' && detail !== null && !Array.isArray(detail)) {
        const entries = Object.entries(detail);
        if (!entries.length) return 'Sin información adicional';

        return entries.map(([key, value]) => {
          return `${humanizeKey(key)}:\n${readableValue(value)}`;
        }).join('\n\n');
      }

      if (Array.isArray(detail)) return JSON.stringify(detail, null, 2);
      return readableValue(detail);
    }

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
          className: 'detail-cell',
          render: function (data, type) {
            if (type !== 'display') return data;
            if (!data || data === '—') return '<span class="text-muted">Sin detalles</span>';
            return formatDetailPreview(data);
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

    // Abre el detalle completo y lo presenta con etiquetas y saltos de línea.
    $('#tablaBitacora tbody').on('click', '.btn-ver-detalle', function () {
      const fila = $tabla.row($(this).closest('tr')).data();
      if (!fila) return;

      $('#detalleModalBody').text(formatDetailFull(fila.detalle));
      bootstrap.Modal.getOrCreateInstance(document.getElementById('detalleModal')).show();
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
