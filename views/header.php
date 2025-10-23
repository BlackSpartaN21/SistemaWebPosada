<?php
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    $rol = $_SESSION['rol'] ?? null; // 'Administrador' | 'Recepcionista' | null
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Posada Las Mandarinas</title>
    <link href="../public/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/css/sweetalert2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../public/css/all.css">

    <style>

        .form-label i {
    margin-right: 6px;
    color:rgb(0, 0, 0);
}

        body {
            
            background-image: url('../public/img/wallpaper3.png'); /* Ajusta la ruta según tu estructura */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        .navbar {
            background-color: white !important;
            border-bottom: 3px solid #BA3B0A;
            padding-left: 20px; /* Añadido padding izquierdo */
            padding-right: 20px; /* Añadido padding derecho */
        }
        .navbar-brand {
            color: #BA3B0A !important;
            font-weight: bold;
            font-size: 24px; /* Aumentado tamaño de letra */
        }
        .nav-link {
            color: rgb(0, 0, 0) !important;
            font-weight: 500;
            font-size: 18px; /* Aumentado tamaño de letra */
            margin-right: 15px; /* Separación entre los enlaces */
        }
        .nav-link:hover {
            color: #922E08 !important; /* Tono más oscuro al pasar el cursor */
        }
        .dropdown-menu {
            background-color: white;
            border: 1px solid #BA3B0A;
        }
        .dropdown-item {
            color: rgb(0, 0, 0) !important;
            font-size: 16px; /* Tamaño de letra más grande para los ítems del dropdown */
        }
        .dropdown-item:hover {
            background-color: #FFE6E0 !important;
        }
        .navbar-toggler {
            border-color: #BA3B0A !important;
        }
        .navbar-toggler-icon {
            background-color: #BA3B0A !important;
            width: 24px;
            height: 24px;
        }
        /* Estilo personalizado para la hamburguesa */
        @media (max-width: 991px) {
            .navbar-toggler {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
        }
            .navbar-toggler-icon {
                background-color: #BA3B0A !important;
            }
        }
        /* Espaciado adicional entre los ítems del menú */
        .navbar-nav .nav-item {
            padding-right: 20px; /* Espaciado a la derecha de cada ítem */
        }

        /* Aumentar la separación entre los módulos y el logo */
        .navbar-nav {
            margin-left: 55px; /* Separación mayor entre los módulos y el logo */
        }

        /* Borde alrededor de la imagen del usuario */
        .navbar-nav .nav-item img {
            border: 3px solid #BA3B0A; /* Borde con el color de la marca */
            padding: 2px; /* Espaciado interno para que el borde no toque la imagen */
        }
        
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="recepcion.php">
                <img src="../public/img/logoPosadaRecortada.png" alt="Logo" width="200" height="75" class="d-inline-block align-text-top">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto"> <!-- Alineación a la izquierda de todos los módulos -->
                    <li class="nav-item">
                        <a class="nav-link" href="recepcion.php">Recepción</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="clientesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Clientes
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="clientesDropdown">
                            <li><a class="dropdown-item" href="./registrar.php" data-bs-toggle="modal" data-bs-target="#clienteModal">Registrar</a></li>
                            <li><a class="dropdown-item" href="modificar.php">Modificar</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reportes.php">Reportes</a>
                    </li>
                    <?php if ($rol === 'Administrador'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Herramientas Administrativas
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="gestionar_habitaciones.php">Gestionar Habitación</a></li>
                            <li><a class="dropdown-item" href="gestionar_usuarios.php">Gestionar Usuarios</a></li>
                            <!-- Agrega más ítems admin aquí -->
                            </ul>
                        </li>
<?php endif; ?>
                </ul>
                <!-- Avatar y Dropdown para usuario alineado a la derecha -->
                <ul class="navbar-nav ms-auto"> <!-- Esto alinea el módulo del usuario a la derecha -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="../public/img/imglogin.jpg" alt="User Avatar" class="rounded-circle" width="60" height="60">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="perfil.php">Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="perfil.php">Manual de Usuario</a></li>
                            <li><a class="dropdown-item" href="perfil.php">Copia de Seguridad</a></li>
                            <li><a class="dropdown-item" href="../controllers/logout.php">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Modal de Registro/Edición de Cliente -->
<div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="clienteModalLabel">Datos del Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <!-- Acción por defecto: CREAR (modificar.php cambia esto a EDITAR con JS) -->
        <form id="clienteForm" method="POST" action="../controllers/registrar.php" novalidate>
          <div class="mb-3">
            <label for="tipo_documento_cliente" class="form-label">
              <i class="fas fa-id-card"></i> Tipo de Documento
            </label>
            <select class="form-select" id="tipo_documento_cliente" name="tipo_documento_cliente" required>
              <option value="V">V</option>
              <option value="E">E</option>
              <option value="P">P</option>
              <option value="J">J</option>
            </select>
            <div class="valid-feedback">Se ve bien.</div>
            <div class="invalid-feedback">Selecciona un tipo válido.</div>
          </div>

          <div class="mb-3">
            <label for="documento_cliente" class="form-label">
              <i class="fas fa-id-badge"></i> Documento
            </label>
            <input type="text" class="form-control" id="documento_cliente" name="documento_cliente" maxlength="10" required>
            <div class="valid-feedback">Perfecto.</div>
          </div>

          <div class="mb-3">
            <label for="nombres_cliente" class="form-label">
              <i class="fas fa-user"></i> Nombres
            </label>
            <input type="text" class="form-control" id="nombres_cliente" name="nombres_cliente" maxlength="50" required>
            <div class="valid-feedback">Correcto.</div>
            <div class="invalid-feedback">Solo letras y espacios (máx. 50).</div>
          </div>

          <div class="mb-3">
            <label for="apellidos_cliente" class="form-label">
              <i class="fas fa-user-tag"></i> Apellidos
            </label>
            <input type="text" class="form-control" id="apellidos_cliente" name="apellidos_cliente" maxlength="50" required>
            <div class="valid-feedback">Correcto.</div>
            <div class="invalid-feedback">Solo letras y espacios (máx. 50).</div>
          </div>

          <div class="mb-3">
            <label for="telefono_cliente" class="form-label">
              <i class="fas fa-phone"></i> Teléfono
            </label>
            <input type="text" class="form-control" id="telefono_cliente" name="telefono_cliente" maxlength="11" required>
            <div class="valid-feedback">Se ve bien.</div>
            <div class="invalid-feedback">Debe contener exactamente 11 caracteres numéricos.</div>
          </div>

          <div class="mb-3">
            <label for="correo_cliente" class="form-label">
              <i class="fas fa-envelope"></i> Correo Electrónico
            </label>
            <input type="email" class="form-control" id="correo_cliente" name="correo_cliente" required>
            <div class="valid-feedback">Correo válido.</div>
            <div class="invalid-feedback">Ingrese un correo válido.</div>
          </div>

          <div class="mb-3">
            <label for="descripcion_cliente" class="form-label">
              <i class="fas fa-align-left"></i> Descripción (Opcional)
            </label>
            <textarea class="form-control" id="descripcion_cliente" name="descripcion_cliente" rows="3" maxlength="100"></textarea>
            <div class="valid-feedback">Ok.</div>
            <div class="invalid-feedback">Máximo 100 caracteres.</div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save"></i> Guardar
            </button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
              <i class="fas fa-times"></i> Cancelar
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
<?php
// Incluir automáticamente el footer al final del ciclo de vida de la petición
// (así no tienes que hacer include en cada vista).
if (!defined('FOOTER_AUTO')) {
  define('FOOTER_AUTO', true);
  register_shutdown_function(function () {
    // __DIR__ apunta a /views  → /views/footer.php
    include __DIR__ . '/footer.php';
  });
}
?>

    <script src="../public/js/validacion.js"></script>
    <script src="../public/js/sweetalert2.min.js"></script>
    <script src="../public/js/bootstrap.bundle.min.js"></script>

<?php
// En header.php: asegurar sesión para leer "flash"
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$__flashType = $_SESSION['flash_alert_type'] ?? null;
$__flashMsg  = $_SESSION['flash_alert_msg']  ?? null;
unset($_SESSION['flash_alert_type'], $_SESSION['flash_alert_msg']);
?>
<!-- SweetAlert2 local -->
<script src="../public/js/sweetalert2.min.js"></script>

<script>
// ===== Validación modal cliente (checks en vivo) =====
function soloNumeros(v){ return /^[0-9]+$/.test(v); }
function soloLetrasEspacios(v){ return /^[A-Za-zÁÉÍÓÚÑáéíóúñ ]+$/.test(v); }
function validarEmail(v){ return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); }

(function initClienteModalValidation(){
  const f    = document.getElementById('clienteForm');
  if (!f) return;

  const tipo = document.getElementById('tipo_documento_cliente');
  const doc  = document.getElementById('documento_cliente');
  const tel  = document.getElementById('telefono_cliente');
  const nom  = document.getElementById('nombres_cliente');
  const ape  = document.getElementById('apellidos_cliente');
  const mail = document.getElementById('correo_cliente');
  const des  = document.getElementById('descripcion_cliente');

  tipo?.addEventListener('change', ()=>{
    const ok = ['V','E','P','J'].includes(tipo.value);
    tipo.classList.toggle('is-invalid', !ok);
    tipo.classList.toggle('is-valid', ok);
  });
  doc?.addEventListener('input', ()=>{
    const ok = soloNumeros(doc.value) && doc.value.length <= 10;
    doc.classList.toggle('is-invalid', !ok);
    doc.classList.toggle('is-valid', ok);
  });
  tel?.addEventListener('input', ()=>{
    const ok = soloNumeros(tel.value) && tel.value.length === 11;
    tel.classList.toggle('is-invalid', !ok);
    tel.classList.toggle('is-valid', ok);
  });
  nom?.addEventListener('input', ()=>{
    const ok = nom.value.length <= 50 && soloLetrasEspacios(nom.value);
    nom.classList.toggle('is-invalid', !ok);
    nom.classList.toggle('is-valid', ok);
  });
  ape?.addEventListener('input', ()=>{
    const ok = ape.value.length <= 50 && soloLetrasEspacios(ape.value);
    ape.classList.toggle('is-invalid', !ok);
    ape.classList.toggle('is-valid', ok);
  });
  mail?.addEventListener('input', ()=>{
    const ok = validarEmail(mail.value);
    mail.classList.toggle('is-invalid', !ok);
    mail.classList.toggle('is-valid', ok);
  });
  des?.addEventListener('input', ()=>{
    const ok = des.value.length <= 100;
    des.classList.toggle('is-invalid', !ok);
    des.classList.toggle('is-valid', ok);
  });

  f.addEventListener('submit', (ev)=>{
    let ok = true;
    const vTipo = ['V','E','P','J'].includes(tipo.value); if(!vTipo){ok=false; tipo.classList.add('is-invalid');}
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

// ===== SweetAlert (flash desde el servidor) =====
(function(){
  const type = <?= json_encode($__flashType) ?>;
  const msg  = <?= json_encode($__flashMsg) ?>;
  if (!type || !msg) return;
  const icon = (type === 'success' ? 'success' :
               (type === 'error'   ? 'error'   :
               (type === 'warning' ? 'warning' : 'info')));
  Swal.fire({ icon, title: msg, timer: 2200, showConfirmButton: false });
})();
</script>


</body>
</html>
