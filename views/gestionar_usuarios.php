<?php
   
    require_once '../config/auth.php';
    require_admin();
    require_once '../config/db.php';
?>
<?php include 'header.php'; ?>

<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="fas fa-users-cog me-2"></i>Gestionar Usuarios</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
      <i class="fas fa-user-plus me-1"></i>Nuevo usuario
    </button>
  </div>

  <?php if (!empty($_GET['ok'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-2"></i>Operación realizada con éxito.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php elseif (!empty($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_GET['error']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php
    // Cargar usuarios
    $stmt = $pdo->query('SELECT id_usuario, nombre_usuario, apellido_usuario, correo_usuario, rol_usuario FROM usuarios ORDER BY id_usuario DESC');
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <table id="tabla-usuarios" class="table table-striped table-bordered">
    <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Correo</th>
        <th>Rol</th>
        <th style="width:160px;">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($usuarios as $u): ?>
        <tr>
          <td><?= (int)$u['id_usuario'] ?></td>
          <td><?= htmlspecialchars($u['nombre_usuario']) ?></td>
          <td><?= htmlspecialchars($u['apellido_usuario']) ?></td>
          <td><?= htmlspecialchars($u['correo_usuario']) ?></td>
          <td>
            <?php if ($u['rol_usuario']==='Administrador'): ?>
              <span class="badge bg-primary">Administrador</span>
            <?php else: ?>
              <span class="badge bg-secondary">Recepcionista</span>
            <?php endif; ?>
          </td>
          <td>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditarUsuario<?= (int)$u['id_usuario'] ?>">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalPasswordUsuario<?= (int)$u['id_usuario'] ?>" title="Restablecer contraseña">
              <i class="fas fa-key"></i>
            </button>
            <form action="../controllers/eliminar_usuario.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este usuario?');">
              <input type="hidden" name="id_usuario" value="<?= (int)$u['id_usuario'] ?>">
              <button class="btn btn-sm btn-danger" type="submit"><i class="fas fa-trash"></i></button>
            </form>
          </td>
        </tr>

        <!-- Modal Editar Usuario -->
        <div class="modal fade" id="modalEditarUsuario<?= (int)$u['id_usuario'] ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <form action="../controllers/guardar_usuario.php" method="POST" class="modal-content">
              <input type="hidden" name="id_usuario" value="<?= (int)$u['id_usuario'] ?>">
              <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Editar usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row g-2">
                  <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($u['nombre_usuario']) ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Apellido</label>
                    <input type="text" class="form-control" name="apellido" value="<?= htmlspecialchars($u['apellido_usuario']) ?>" required>
                  </div>
                </div>
                <div class="mt-2">
                  <label class="form-label">Correo</label>
                  <input type="email" class="form-control" name="correo" value="<?= htmlspecialchars($u['correo_usuario']) ?>" required>
                </div>
                <div class="mt-2">
                  <label class="form-label">Rol</label>
                  <select class="form-select" name="rol" required>
                    <option value="Administrador" <?= ($u['rol_usuario']==='Administrador'?'selected':'') ?>>Administrador</option>
                    <option value="Recepcionista" <?= ($u['rol_usuario']==='Recepcionista'?'selected':'') ?>>Recepcionista</option>
                  </select>
                </div>
                <div class="form-text mt-2">
                  La contraseña no se modifica aquí. Usa el botón <strong><i class="fas fa-key"></i> Restablecer contraseña</strong>.
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Modal Restablecer Contraseña -->
        <div class="modal fade" id="modalPasswordUsuario<?= (int)$u['id_usuario'] ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <form action="../controllers/reset_password_usuario.php" method="POST" class="modal-content">
              <input type="hidden" name="id_usuario" value="<?= (int)$u['id_usuario'] ?>">
              <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-key me-2"></i>Restablecer contraseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
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

      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Modal Nuevo Usuario -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="../controllers/guardar_usuario.php" method="POST" class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Nuevo usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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


<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (window.$ && $.fn.DataTable) {
      $('#tabla-usuarios').DataTable({
        language: { url: '../public/js/es-ES.json' }
      });
    }
  });
</script>