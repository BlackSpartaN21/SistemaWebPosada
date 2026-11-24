<?php
// controllers/validar_login.php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once '../config/db.php';
require_once '../config/auth.php';
require_once '../config/bitacora.php'; // [BITÁCORA]

$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    // Log y salida original
    try { bitacora_log($pdo, 'Autenticación', 'login_simple', ['motivo' => 'method_not_allowed', 'method' => $_SERVER['REQUEST_METHOD'] ?? null, 'ip' => $ip], 'ERROR'); } catch (Throwable $_) {}
    header('Location: ../views/login.php');
    exit;
}

$correo     = trim((string)($_POST['correo']     ?? ''));
$contrasena = trim((string)($_POST['contrasena'] ?? ''));

if ($correo === '' || $contrasena === '') {
    try { bitacora_log($pdo, 'Autenticación', 'login_simple', ['motivo' => 'inputs_invalidos', 'ip' => $ip], 'ERROR'); } catch (Throwable $_) {}
    header('Location: ../views/login.php?error=Campos%20requeridos');
    exit;
}

try {
    $stmt = $pdo->prepare('
        SELECT id_usuario, nombre_usuario, apellido_usuario, correo_usuario, contrasena_usuario, rol_usuario
        FROM usuarios
        WHERE correo_usuario = :correo
        LIMIT 1
    ');
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($contrasena, (string)$usuario['contrasena_usuario'])) {
        // Regenerar ID de sesión para evitar fijación de sesión
        session_regenerate_id(true);

        $_SESSION['id_usuario'] = (int)$usuario['id_usuario'];
        $_SESSION['nombre']     = (string)$usuario['nombre_usuario'];
        $_SESSION['apellido']   = (string)$usuario['apellido_usuario'];
        $_SESSION['correo']     = (string)$usuario['correo_usuario'];
        $_SESSION['rol']        = (string)$usuario['rol_usuario']; // 'Administrador' o 'Recepcionista'

        // Bitácora OK
        try {
            bitacora_log($pdo, 'Autenticación', 'login_simple', [
                'id_usuario' => (int)$usuario['id_usuario'],
                'email'      => $correo,
                'rol'        => (string)$usuario['rol_usuario'],
                'ip'         => $ip
            ], 'OK');
        } catch (Throwable $_) {}

        header('Location: ../views/recepcion.php');
        exit;
    }

    // Credenciales inválidas
    try {
        bitacora_log($pdo, 'Autenticación', 'login_simple', [
            'motivo' => 'invalid_credentials',
            'email'  => $correo,
            'ip'     => $ip
        ], 'ERROR');
    } catch (Throwable $_) {}

    header('Location: ../views/login.php?error=Correo%20o%20contrase%C3%B1a%20incorrectos');
    exit;

} catch (PDOException $e) {
    // Bitácora error DB (sin romper tu comportamiento original)
    try {
        bitacora_log($pdo, 'Autenticación', 'login_simple', [
            'motivo'   => 'db_error',
            'email'    => $correo,
            'ip'       => $ip,
            'pdo_code' => $e->getCode(),
            'ex'       => $e->getMessage()
        ], 'ERROR');
    } catch (Throwable $_) {}

    header('Location: ../views/login.php?error=Error%20en%20la%20base%20de%20datos');
    exit;
}
