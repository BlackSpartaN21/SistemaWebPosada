<?php
// views/footer.php
// Cierra la página con un footer global y carga de JS locales.
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<footer class="mt-auto bg-light border-top">
  <br>
  <div class="container-fluid py-2 px-3 px-md-5 position-relative d-flex align-items-center justify-content-between ">

    <!-- Izquierda: SOLO el logo -->
    <div class="d-flex align-items-center gap-2">
      <img src="../public/img/LogoPosadaRecortada.png" alt="Posada Las Mandarinas" width="110" height="40" loading="lazy">
    </div>

    <!-- CENTRO: SOLO el texto, centrado absoluto -->
    <div class="position-absolute start-50 top-50 translate-middle text-center">
      <span class="text-muted small d-block">
        © <?= date('Y') ?> Posada Las Mandarinas · Ejido, Estado Mérida · Todos los derechos reservados
      </span>
    </div>

    <!-- Derecha: sesión y Reportes -->
    <div class="d-flex align-items-center gap-3">
      <span class="text-muted small">
        <i class="fa-solid fa-user-shield"></i>
        Sesión: <?= htmlspecialchars($_SESSION['rol'] ?? 'Invitado') ?>
      </span>
      <a class="text-decoration-none small" href="reportes.php">
        <i class="fa-solid fa-chart-line"></i> Reportes
      </a>
    </div>

  </div>
  <br>
</footer>


    <!-- JS centrales (locales) -->

  </body>
</html>
