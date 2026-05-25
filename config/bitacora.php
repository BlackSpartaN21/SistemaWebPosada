<?php
// config/bitacora.php
declare(strict_types=1);

function bitacora_log(PDO $pdo, string $modulo, string $accion, $detalle = null, string $resultado = 'OK'): void {
    try {
        if (is_array($detalle)) {
            $detalle = json_encode($detalle, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        $idUsuario = isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

        $stmt = $pdo->prepare("
            INSERT INTO bitacora (id_usuario, modulo, accion, detalle, ip, user_agent, resultado)
            VALUES (:id, :modulo, :accion, :detalle, :ip, :ua, :resultado)
        ");
        $stmt->execute([
            ':id'        => $idUsuario,
            ':modulo'    => $modulo,
            ':accion'    => $accion,
            ':detalle'   => $detalle,
            ':ip'        => $ip,
            ':ua'        => $ua,
            ':resultado' => $resultado
        ]);
    } catch (Throwable $e) {
        // No romper el flujo de la app por la bitácora
        error_log('bitacora_log error: ' . $e->getMessage());
    }
}
