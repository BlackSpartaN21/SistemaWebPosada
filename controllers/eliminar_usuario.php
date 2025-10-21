<?php
    require_once '../config/auth.php';
    require_admin();
    require_once '../config/db.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/gestionar_usuarios.php');
    exit;
    }

    $id_usuario = (int)($_POST['id_usuario'] ?? 0);
    if ($id_usuario <= 0) {
    header('Location: ../views/gestionar_usuarios.php?error=ID%20inv%C3%A1lido');
    exit;
    }

    try {
    // No permitir eliminarse a sí mismo
    if (isset($_SESSION['id_usuario']) && (int)$_SESSION['id_usuario'] === $id_usuario) {
        header('Location: ../views/gestionar_usuarios.php?error=No%20puedes%20eliminar%20tu%20propia%20cuenta');
        exit;
    }

    // Verificar si el objetivo es admin y si es el último admin
    $q = $pdo->prepare('SELECT rol_usuario FROM usuarios WHERE id_usuario=:id');
    $q->execute([':id' => $id_usuario]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        header('Location: ../views/gestionar_usuarios.php?error=Usuario%20no%20encontrado');
        exit;
    }

    if ($row['rol_usuario'] === 'Administrador') {
        $c = $pdo->query("SELECT COUNT(*) AS c FROM usuarios WHERE rol_usuario='Administrador'");
        $countAdmin = (int)$c->fetchColumn();
        if ($countAdmin <= 1) {
        header('Location: ../views/gestionar_usuarios.php?error=No%20puedes%20eliminar%20al%20%C3%BAltimo%20Administrador');
        exit;
        }
    }

    $del = $pdo->prepare('DELETE FROM usuarios WHERE id_usuario=:id');
    $del->execute([':id' => $id_usuario]);

    header('Location: ../views/gestionar_usuarios.php?ok=1');
    exit;

    } catch (PDOException $e) {
    header('Location: ../views/gestionar_usuarios.php?error=Error%20de%20base%20de%20datos');
    exit;
    }
?>