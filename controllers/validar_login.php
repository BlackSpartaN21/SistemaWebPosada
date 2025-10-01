<?php
session_start();
include '../config/db.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if (empty($correo) || empty($contrasena)) {
        header('Location: ../views/login.php?error=Campos requeridos');
        exit;
    }

    // Buscar el usuario por correo
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo_usuario = :correo");
    $stmt->execute(['correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($contrasena, $usuario['contrasena_usuario'])) {
        // Guardar datos en la sesión
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre_usuario'];
        $_SESSION['apellido'] = $usuario['apellido_usuario'];
        $_SESSION['rol'] = $usuario['rol_usuario'];
        header('Location: ../views/recepcion.php');
        exit;
    } else {
        header('Location: ../views/login.php?error=Correo o contraseña incorrectos');
        exit;
    }
} else {
    header('Location: ../views/login.php');
    exit;
}
