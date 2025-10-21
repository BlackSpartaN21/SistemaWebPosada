<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);

    if (empty($correo) || empty($contrasena)) {
        $_SESSION['error_message'] = "Por favor, completa todos los campos.";
        header("Location: ../views/login.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT id_usuario, nombre_usuario, apellido_usuario, correo_usuario, contrasena_usuario, rol_usuario FROM usuarios WHERE correo_usuario = :correo LIMIT 1");
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && $contrasena === $usuario['contrasena_usuario']) {

            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
            $_SESSION['apellido_usuario'] = $usuario['apellido_usuario'];
            $_SESSION['rol_usuario'] = $usuario['rol_usuario'];

            header("Location: ../views/recepcion.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Correo o contraseña incorrectos.";
            header("Location: ../views/login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error en la base de datos.";
        header("Location: ../views/login.php");
        exit();
    }
} else {
    header("Location: ../views/login.php");
    exit();
}