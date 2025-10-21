<?php
    // Arranque de sesión seguro y utilidades de rol/permiso
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        }


        function is_logged_in(): bool {
        return isset($_SESSION['id_usuario']);
    }


    function user_role(): ?string {
        return $_SESSION['rol'] ?? null; // 'Administrador' | 'Recepcionista' | null
    }


    function is_admin(): bool {
        return user_role() === 'Administrador';
    }


    function require_login(): void {
        if (!is_logged_in()) {
            http_response_code(302);
            header('Location: ../views/login.php?error=Debes iniciar sesión');
            exit;
        }
    }


    function require_admin(): void {
        require_login();
        if (!is_admin()) {
            http_response_code(403);
            // Puedes llevarlo a una página de "Acceso denegado" si lo prefieres
            header('Location: ../views/recepcion.php?error=Acceso%20denegado');
            exit;
        }
    }
?>