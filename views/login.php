<?php require_once '../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión</title>
  <link href="../public/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../public/css/font-awesome/css/all.min.css">
  <style>
    body {
      background-image: url('../public/img/wallpaper3.png');
      height: 100vh;
    }
    
    .card {
      border-radius: 1rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      overflow: hidden;
    }
    
    .login-img {
      background: linear-gradient(rgba(186, 59, 10, 0.7), rgba(154, 97, 109, 0.7)), 
                  url('../public/img/imglogin.jpg');
      background-size: cover;
      background-position: center;
      border-radius: 1rem 0 0 1rem;
    }
    
    .logo-container {
      margin-bottom: 30px;
    }
    
    .logo-icon {
      font-size: 2.5rem;
      color: #BA3B0A;
      background: white;
      padding: 15px;
      border-radius: 50%;
      margin-right: 15px;
    }
    
    .logo-text {
      font-size: 2.2rem;
      font-weight: 700;
      color: white;
      letter-spacing: 1px;
    }
    
    .form-control {
      border-radius: 8px;
      padding: 12px 15px;
      font-size: 1.05rem;
    }
    
    .form-control:focus {
      border-color: #BA3B0A;
      box-shadow: 0 0 0 0.25rem rgba(186, 59, 10, 0.25);
    }
    
    .btn-login {
      background: #BA3B0A;
      border: none;
      color: white;
      padding: 12px;
      font-size: 1.1rem;
      font-weight: 600;
      border-radius: 8px;
      transition: all 0.3s;
    }
    
    .btn-login:hover {
      background: #9c3209;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(186, 59, 10, 0.3);
    }
    
    .form-label {
      font-weight: 500;
      color: #555;
      margin-bottom: 8px;
    }
    
    .link-primary {
      color: #BA3B0A !important;
      text-decoration: none;
    }
    
    .link-primary:hover {
      color: #9c3209 !important;
      text-decoration: underline;
    }
    
    .divider {
      display: flex;
      align-items: center;
      margin: 20px 0;
    }
    
    .divider::before, .divider::after {
      content: '';
      flex: 1;
      border-bottom: 1px solid #ddd;
    }
    
    .divider span {
      padding: 0 15px;
      color: #777;
      font-size: 0.9rem;
    }
    
    .footer-links {
      display: flex;
      justify-content: space-between;
      margin-top: 25px;
    }
    
    /* Nuevos estilos para el botón mostrar/ocultar contraseña */
    .password-toggle {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #777;
      z-index: 5;
    }
    
    .password-container {
      position: relative;
    }
    
    /* Estilo para el botón volver */
    .btn-volver {
      background: #6c757d;
      border: none;
      color: white;
      padding: 8px 15px;
      font-size: 0.95rem;
      border-radius: 5px;
      transition: all 0.3s;
      position: absolute;
      top: 20px;
      right: 20px;
    }
    
    .btn-volver:hover {
      background: #5a6268;
      transform: translateY(-2px);
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
    }
    
    @media (max-width: 768px) {
      .login-img {
        height: 200px;
        border-radius: 1rem 1rem 0 0;
      }
    }
  </style>
</head>
<body>
  <section class="vh-100">
    <div class="container py-5 h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col col-xl-10">
          <div class="card">
            <div class="row g-0">
              <!-- Imagen de fondo -->
              <div class="col-md-6 col-lg-5 d-none d-md-flex login-img align-items-center justify-content-center">
                <div class="text-center px-4">
                  <h2 class="text-white mb-4">Sistema de Registro</h2>
                  <h3 class="text-white mb-4">Posada Las Mandarinas</h3>
                  
                </div>
              </div>
              
              <!-- Formulario -->
              <div class="col-md-6 col-lg-7 d-flex align-items-center">
                <div class="card-body p-4 p-lg-5 position-relative">
                  <!-- Botón Volver modificado para redirigir a index.html -->
                  <button type="button" class="btn btn-volver" onclick="window.location.href='../public/index.html'">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                  </button>
                  
                  <form action="../controllers/validar_login.php" method="POST">
                    <!-- Mensaje de error -->
                    <?php if (isset($_GET['error'])): ?>
                      <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                      </div>
                    <?php endif; ?>
                    
                    <h2 class="fw-normal mb-4" style="letter-spacing: 1px; color: #444;">Inicia sesión en tu cuenta</h2>
                    
                    <!-- Campo de correo -->
                    <div class="mb-4">
                      <label for="correo" class="form-label">Correo electrónico</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="correo" class="form-control" placeholder="usuario@ejemplo.com" required>
                      </div>
                    </div>
                    
                    <!-- Campo de contraseña con botón para mostrar/ocultar -->
                    <div class="mb-4 password-container">
                      <label for="contrasena" class="form-label">Contraseña</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="contrasena" id="contrasena" class="form-control" placeholder="••••••••" required>
                        <span class="password-toggle" id="togglePassword">
                          <i class="fas fa-eye"></i>
                        </span>
                      </div>
                    </div>

                    <!-- Botón de login -->
                    <div class="pt-1 mb-4">
                      <button type="submit" class="btn btn-login btn-lg w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <script src="../public/js/jquery-3.7.1.min.js"></script>
  <script src="../public/js/bootstrap.bundle.min.js"></script>
  <script>
    // Función para mostrar/ocultar contraseña
    $(document).ready(function() {
      $('#togglePassword').click(function() {
        const passwordField = $('#contrasena');
        const passwordFieldType = passwordField.attr('type');
        const icon = $(this).find('i');
        
        if (passwordFieldType === 'password') {
          passwordField.attr('type', 'text');
          icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
          passwordField.attr('type', 'password');
          icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
      });
    });
  </script>
</body>
</html>