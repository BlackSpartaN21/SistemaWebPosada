<?php
// views/footer.php
// Cierra la página con un footer global y carga de JS locales.
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
    <footer class="mt-auto py-3 bg-light border-top">
      <div class="container-fluid py-1 px-3 px-md-5 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">

        <div class="d-flex align-items-center gap-2">
          <img src="../public/img/LogoPosadaRecortada.png" alt="Posada Las Mandarinas" width="110" height="40" loading="lazy">
          <span class="text-muted small">
            © <?= date('Y') ?> Posada Las Mandarinas · Todos los derechos reservados
          </span>
        </div>

        <div class="d-flex align-items-center gap-3">
          <span class="text-muted small">
            <i class="fa-solid fa-user-shield"></i>
            Sesión: <?= htmlspecialchars($_SESSION['rol_usuario'] ?? 'Invitado') ?>
          </span>
          <!-- Enlaces opcionales (ajústalos o elimínalos si no aplican) -->
          <a class="text-decoration-none small" href="reportes.php">
            <i class="fa-solid fa-chart-line"></i> Reportes
          </a>
        </div>
      </div>
    </footer>

    <!-- JS centrales (locales) -->

  </body>
</html>
