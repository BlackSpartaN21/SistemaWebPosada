<?php
    require_once './config/db.php';

    $nombre = "Fabian";
    $apellido = "Sanchez";
    $correo = "fabian@gmail.com";
    $contrasena = password_hash("12345", PASSWORD_DEFAULT);
    $rol = "Recepcionista";

    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, apellido_usuario, correo_usuario, contrasena_usuario, rol_usuario) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $apellido, $correo, $contrasena, $rol]);

    echo "Usuario administrador creado.";
?>