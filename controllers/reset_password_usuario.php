<?php
    require_once '../config/auth.php';
    require_admin();
    require_once '../config/db.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/gestionar_usuarios.php');
    exit;
    }

    $id_usuario = (int)($_POST['id_usuario'] ?? 0);
    $nueva      = (string)($_POST['nueva_contrasena'] ?? '');

    if ($id_usuario <= 0 || strlen($nueva) < 6) {
    header('Location: ../views/gestionar_usuarios.php?error=Datos%20inv%C3%A1lidos');
    exit;
    }

    try {
    $hash = password_hash($nueva, PASSWORD_DEFAULT);
    $upd = $pdo->prepare('UPDATE usuarios SET contrasena_usuario=:pass WHERE id_usuario=:id');
    $upd->execute([':pass' => $hash, ':id' => $id_usuario]);
    header('Location: ../views/gestionar_usuarios.php?ok=1');
    exit;
    } catch (PDOException $e) {
    header('Location: ../views/gestionar_usuarios.php?error=Error%20de%20base%20de%20datos');
    exit;
    }
?>