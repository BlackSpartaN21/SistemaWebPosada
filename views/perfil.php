<?php
// views/perfil.php
require_once '../config/auth.php';
require_login();
require_once '../config/db.php';

$userId = (int)($_SESSION['id_usuario'] ?? 0);

$stmt = $pdo->prepare("
  SELECT id_usuario, nombre_usuario, apellido_usuario, correo_usuario, rol_usuario, fecha_creacion
  FROM usuarios
  WHERE id_usuario = :id
  LIMIT 1
");
$stmt->execute([':id' => $userId]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$u) {
  redirect_safe('login.php?error=Sesión%20inválida');
}
?>
<?php include 'header.php'; ?>

<!-- jQuery (si no está ya en header.php) -->
<script src="../public/js/jquery-3.7.1.min.js"></script>
<!-- SweetAlert -->
<script src="../public/js/sweetalert2.min.js"></script>

<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="fas fa-id-badge me-2"></i>Mi perfil</h4>
    <div class="d-flex gap-2">
      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil">
        <i class="fas fa-user-edit me-1"></i> Editar datos
      </button>
      <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalCambiarPassword">
        <i class="fas fa-key me-1"></i> Cambiar contraseña
      </button>
    </div>
  </div>

  <?php
    $okFlag   = !empty($_GET['ok']);
    $errorMsg = $_GET['error'] ?? '';
  ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const ok = <?php echo $okFlag ? 'true' : 'false'; ?>;
      const errorMsg = <?php echo json_encode($errorMsg); ?>;
      const clearParams = () => {
        const url = new URL(window.location);
        url.searchParams.delete('ok'); url.searchParams.delete('error');
        window.history.replaceState({}, '', url);
      };
      if (ok) Swal.fire({icon:'success', title:'¡Listo!', text:'Operación realizada con éxito.'}).then(clearParams);
      else if (errorMsg) Swal.fire({icon:'error', title:'Ups…', text:errorMsg}).then(clearParams);
    });
  </script>

  <div class="row g-3">
    <div class="col-md-4">
      <div class="card h-100 text-center">
        <div class="card-body d-flex flex-column justify-content-center">
          <div class="mx-auto rounded-circle bg-light border d-flex align-items-center justify-content-center"
               style="width:96px;height:96px;font-size:32px;">
            <?php
              $ini = strtoupper(substr($u['nombre_usuario'] ?? '', 0, 1) . substr($u['apellido_usuario'] ?? '', 0, 1));
              echo htmlspecialchars($ini ?: 'U');
            ?>
          </div>
          <h5 class="mt-3 mb-0">
            <?php echo htmlspecialchars(($u['nombre_usuario'] ?? '') . ' ' . ($u['apellido_usuario'] ?? '')); ?>
          </h5>
          <span class="badge mt-2 <?php echo ($u['rol_usuario'] === 'Administrador') ? 'bg-primary' : 'bg-secondary'; ?>">
            <?php echo htmlspecialchars($u['rol_usuario'] ?? ''); ?>
          </span>
        </div>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card h-100">
        <div class="card-header bg-light">
          <strong>Información de cuenta</strong>
        </div>
        <div class="card-body">
          <div class="row mb-2">
            <div class="col-sm-4 text-muted">Nombre</div>
            <div class="col-sm-8"><?php echo htmlspecialchars($u['nombre_usuario'] ?? ''); ?></div>
          </div>
          <div class="row mb-2">
            <div class="col-sm-4 text-muted">Apellido</div>
            <div class="col-sm-8"><?php echo htmlspecialchars($u['apellido_usuario'] ?? ''); ?></div>
          </div>
          <div class="row mb-2">
            <div class="col-sm-4 text-muted">Correo</div>
            <div class="col-sm-8"><?php echo htmlspecialchars($u['correo_usuario'] ?? ''); ?></div>
          </div>
          <div class="row mb-2">
            <div class="col-sm-4 text-muted">Rol</div>
            <div class="col-sm-8"><?php echo htmlspecialchars($u['rol_usuario'] ?? ''); ?></div>
          </div>
          <div class="row">
            <div class="col-sm-4 text-muted">Miembro desde</div>
            <div class="col-sm-8">
              <?php
                $fc = $u['fecha_creacion'] ?? null;
                echo $fc ? date('d/m/Y', strtotime($fc)) : '—';
              ?>
            </div>
          </div>
        </div>
        <div class="card-footer text-end">
          <a href="recepcion.php" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- =========================
     MODAL: Editar datos
     ========================= -->
<div class="modal fade" id="modalEditarPerfil" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="../controllers/perfil_actualizar.php" method="POST" class="modal-content bg-white" id="formEditarPerfil">
      <input type="hidden" name="id_usuario" value="<?php echo (int)$u['id_usuario']; ?>">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Editar datos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($u['nombre_usuario']); ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Apellido</label>
            <input type="text" class="form-control" name="apellido" value="<?php echo htmlspecialchars($u['apellido_usuario']); ?>" required>
          </div>
        </div>
        <div class="mt-2">
          <label class="form-label">Correo</label>
          <input type="email" class="form-control" name="correo" value="<?php echo htmlspecialchars($u['correo_usuario']); ?>" required>
        </div>
        <div class="form-text mt-2">
          El <strong>rol</strong> no puede cambiarse desde aquí.
        </div>
      </div>
      <div class="modal-footer">
        <!-- Guardar verde IZQUIERDA -->
        <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Guardar</button>
        <!-- Cancelar rojo DERECHA -->
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- =========================
     MODAL: Cambiar contraseña
     ========================= -->
<div class="modal fade" id="modalCambiarPassword" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="../controllers/perfil_cambiar_password.php" method="POST" class="modal-content bg-white" id="formCambiarPassword">
      <input type="hidden" name="id_usuario" value="<?php echo (int)$u['id_usuario']; ?>">
      <div class="modal-header bg-warning">
        <h5 class="modal-title"><i class="fas fa-key me-2"></i>Cambiar contraseña</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <!-- Actual -->
        <div class="mb-2">
          <label class="form-label">Contraseña actual</label>
          <div class="input-group">
            <input type="password" class="form-control" name="contrasena_actual" id="cp_actual" minlength="6" required>
            <button type="button" class="btn btn-outline-secondary" id="cp_toggle_0" tabindex="-1" title="Mostrar/ocultar">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>

        <!-- Nueva -->
        <div class="mb-2">
          <label class="form-label">Nueva contraseña</label>
          <div class="input-group">
            <input type="password" class="form-control" name="nueva_contrasena" id="cp_nueva" minlength="6" required>
            <button type="button" class="btn btn-outline-secondary" id="cp_toggle_1" tabindex="-1" title="Mostrar/ocultar">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          <div class="form-text">Mínimo 6 caracteres.</div>
        </div>

        <!-- Confirmación -->
        <div class="mb-2">
          <label class="form-label">Confirmar nueva contraseña</label>
          <div class="input-group">
            <input type="password" class="form-control" id="cp_confirm" minlength="6" required>
            <button type="button" class="btn btn-outline-secondary" id="cp_toggle_2" tabindex="-1" title="Mostrar/ocultar">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          <div class="invalid-feedback">Las contraseñas no coinciden.</div>
        </div>
      </div>
      <div class="modal-footer">
        <!-- Guardar verde IZQUIERDA -->
        <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Guardar</button>
        <!-- Cancelar rojo DERECHA -->
        <button type="button" class="btn btn-danger ms-auto" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script>
  $(function(){
    // Helpers de toggle
    function togglePassword(inputSel, iconSel) {
      const $inp = $(inputSel);
      const $ico = $(iconSel);
      const isPw = $inp.attr('type') === 'password';
      $inp.attr('type', isPw ? 'text' : 'password');
      $ico.toggleClass('fa-eye fa-eye-slash');
    }
    function setInputType(sel, type){ $(sel).attr('type', type); }
    function resetEye(iconSel){ $(iconSel).removeClass('fa-eye-slash').addClass('fa-eye'); }

    // Toggles Cambiar Password
    $('#cp_toggle_0').on('click', function(){ togglePassword('#cp_actual', '#cp_toggle_0 i'); });
    $('#cp_toggle_1').on('click', function(){ togglePassword('#cp_nueva', '#cp_toggle_1 i'); });
    $('#cp_toggle_2').on('click', function(){ togglePassword('#cp_confirm', '#cp_toggle_2 i'); });

    // Validación de confirmación en tiempo real
    $('#cp_nueva, #cp_confirm').on('input', function(){
      const a = $('#cp_nueva').val(), b = $('#cp_confirm').val();
      $('#cp_confirm').toggleClass('is-invalid', b.length > 0 && a !== b);
    });

    // Al abrir modal: limpiar estado
    const modalPwd = document.getElementById('modalCambiarPassword');
    if (modalPwd) {
      modalPwd.addEventListener('show.bs.modal', function(){
        $('#cp_actual, #cp_nueva, #cp_confirm').val('').removeClass('is-invalid');
        setInputType('#cp_actual','password'); setInputType('#cp_nueva','password'); setInputType('#cp_confirm','password');
        resetEye('#cp_toggle_0 i'); resetEye('#cp_toggle_1 i'); resetEye('#cp_toggle_2 i');
      });
    }

    // Validación al enviar
    $('#formCambiarPassword').on('submit', function(e){
      const a = $('#cp_nueva').val();
      const b = $('#cp_confirm').val();
      if (a !== b) {
        e.preventDefault();
        $('#cp_confirm').addClass('is-invalid').focus();
        Swal.fire({icon:'error', title:'Contraseñas distintas', text:'La confirmación no coincide con la nueva contraseña.'});
      }
    });
  });
</script>
