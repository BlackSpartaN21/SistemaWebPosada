<?php
// views/login.php
declare(strict_types=1);
session_start();

// --- CSRF para el login
if (empty($_SESSION['csrf_login'])) {
  $_SESSION['csrf_login'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_login'];

// --- Resolver mensaje (GET y/o flash en sesión) SOLO para modal
$qsType  = isset($_GET['status']) ? strtolower(trim((string)$_GET['status'])) : null;
$qsMsg   = isset($_GET['msg']) ? trim((string)$_GET['msg']) : null;

$flash   = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']); // consumir flash si existe

$alertType = null;
$alertMsg  = null;

if ($flash && is_array($flash) && !empty($flash['msg'])) {
  $alertType = strtolower((string)$flash['type'] ?? 'info');
  $alertMsg  = (string)$flash['msg'];
} elseif (!empty($qsMsg)) {
  $alertType = in_array($qsType, ['success','error','warning','info']) ? $qsType : 'info';
  $alertMsg  = $qsMsg;
}

// Icono SweetAlert
$swalIconMap = [
  'success' => 'success',
  'error'   => 'error',
  'warning' => 'warning',
  'info'    => 'info',
];
$swalIcon   = $swalIconMap[$alertType] ?? null;
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión | Posada Las Mandarinas</title>

  <!-- CSS locales -->
  <link rel="stylesheet" href="../public/css/bootstrap.min.css">
  <link rel="stylesheet" href="../public/css/all.css"><!-- Font Awesome local -->
  <link rel="stylesheet" href="../public/css/sweetalert2.min.css">
  <link rel="stylesheet" href="../public/css/stylepaginaweb.css"><!-- opcional -->

  <style>
    body.login-bg {
      min-height: 100vh;
      background:
        linear-gradient( rgba(0,0,0,.45), rgba(0,0,0,.45) ),
        url('../public/img/wallpaper.jpg') center/cover no-repeat fixed;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      backdrop-filter: blur(3px);
      background: rgba(255,255,255,.9);
      border: 0;
      border-radius: 1rem;
      box-shadow: 0 20px 45px rgba(0,0,0,.25);
    }
    .brand-top {
      display: flex;
      align-items: center;
      gap: .75rem;
      justify-content: center;
      margin-bottom: .75rem;
    }
    .brand-top img { width: 90px; height: 90px; object-fit: contain; }
    .brand-top h1 { font-size: 1.15rem; margin: 0; font-weight: 700; letter-spacing: .3px; color: #0d6efd; }
    .form-control { padding-top: .7rem; padding-bottom: .7rem; }
    .btn-primary .spinner-border { width: 1rem; height: 1rem; border-width: .15rem; }
    .small-muted { font-size: .9rem; color: #6c757d; }
    .login-wrapper { width: min(100%, 480px); margin: 1rem; }
  </style>
</head>
<body class="login-bg">

  <main class="login-wrapper">
    <div class="card login-card">
      <div class="card-body p-4 p-md-5">

        <div class="brand-top" aria-label="Marca del sistema">
          <img src="../public/img/LogoPosada.png" alt="Logo Posada">
          <h1>Posada Las Mandarinas</h1>
        </div>

        <h2 class="h5 text-center mb-4">Iniciar sesión</h2>

        <!-- Formulario -->
        <form id="formLogin" class="needs-validation" novalidate method="post" action="../controllers/login_process.php" autocomplete="on">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

          <!-- Email -->
          <div class="mb-3">
            <label for="correo" class="form-label">Correo</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
              <input
                type="email"
                class="form-control"
                id="correo"
                name="correo_usuario"
                placeholder="usuario@correo.com"
                required
                autocomplete="username"
                aria-describedby="correoHelp">
              <div class="invalid-feedback">Ingresa un correo válido.</div>
            </div>
            <div id="correoHelp" class="form-text small-muted">Usa el correo registrado en el sistema.</div>
          </div>

          <!-- Password -->
          <div class="mb-2">
            <label for="contrasena" class="form-label">Contraseña</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
              <input
                type="password"
                class="form-control"
                id="contrasena"
                name="contrasena_usuario"
                placeholder="••••••••"
                minlength="6"
                required
                autocomplete="current-password">
              <button class="btn btn-outline-secondary" type="button" id="btnTogglePass" aria-label="Mostrar u ocultar contraseña">
                <i class="fa-solid fa-eye-slash" id="iconEye"></i>
              </button>
              <div class="invalid-feedback">La contraseña es requerida (mín. 6 caracteres).</div>
            </div>
          </div>
<br>
          <!-- <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
              <label class="form-check-label small" for="remember">Recordarme</label>
            </div>
            <a href="#" class="small">¿Olvidaste tu contraseña?</a> 
          </div>
          -->
          <!-- Compatibilidad con controladores antiguos -->
          <input type="hidden" name="correo" id="mirrorCorreo">
          <input type="hidden" name="contrasena" id="mirrorContrasena">

          <button id="btnLogin" type="submit" class="btn btn-primary w-100">
            <span class="label">Entrar</span>
            <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
          </button>

          <a href="../public/index.html" class="btn btn-outline-secondary w-100 mt-2">
            <i class="fa-solid fa-arrow-left-long me-1"></i> Volver
          </a>
        </form>

        <p class="text-center small-muted mt-4 mb-0">
          © <?php echo date('Y'); ?> Posada Las Mandarinas · Todos los derechos reservados.
        </p>
      </div>
    </div>
  </main>

  <!-- JS locales -->
  <script src="../public/js/jquery-3.7.1.min.js"></script>
  <script src="../public/js/bootstrap.bundle.min.js"></script>
  <script src="../public/js/sweetalert2.min.js"></script>

  <script>
    (function () {
      'use strict';

      const form  = document.getElementById('formLogin');
      const btn   = document.getElementById('btnLogin');
      const spin  = btn.querySelector('.spinner-border');
      const label = btn.querySelector('.label');

      // Mostrar/ocultar contraseña
      const btnToggle = document.getElementById('btnTogglePass');
      const inputPass = document.getElementById('contrasena');
      const iconEye   = document.getElementById('iconEye');

      btnToggle.addEventListener('click', function () {
        const isPass = inputPass.getAttribute('type') === 'password';
        inputPass.setAttribute('type', isPass ? 'text' : 'password');
        iconEye.classList.toggle('fa-eye');
        iconEye.classList.toggle('fa-eye-slash');
      });

      // Mirror de nombres para compatibilidad
      const correo = document.getElementById('correo');
      const contr  = document.getElementById('contrasena');
      const mCorr  = document.getElementById('mirrorCorreo');
      const mCont  = document.getElementById('mirrorContrasena');

      function syncMirrors() {
        mCorr.value = correo.value;
        mCont.value = contr.value;
      }
      correo.addEventListener('input', syncMirrors);
      contr.addEventListener('input', syncMirrors);
      syncMirrors();

      // Validación Bootstrap + evitar doble envío
      form.addEventListener('submit', function (e) {
        if (!form.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
          form.classList.add('was-validated');
          return;
        }
        btn.disabled = true;
        spin.classList.remove('d-none');
        label.textContent = 'Entrando...';
      }, false);

      // ÚNICA alerta: SweetAlert si viene mensaje del servidor
      <?php if (!empty($alertMsg) && !empty($swalIcon)): ?>
      Swal.fire({
        icon: <?php echo json_encode($swalIcon, JSON_UNESCAPED_UNICODE); ?>,
        title: {
          success: '¡Listo!',
          error:   'Ups...',
          warning: 'Atención',
          info:    'Información'
        }[<?php echo json_encode($swalIcon, JSON_UNESCAPED_UNICODE); ?>],
        text: <?php echo json_encode($alertMsg, JSON_UNESCAPED_UNICODE); ?>,
        confirmButtonText: 'Aceptar'
      });
      <?php endif; ?>
    })();
  </script>
</body>
</html>
