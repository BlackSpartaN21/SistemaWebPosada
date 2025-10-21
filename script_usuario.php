<?php
    require_once './config/db.php';

    $nombre = "Mary";
    $apellido = "Rangel";
    $correo = "maryrangel06@gmail.com";
    $contrasena = password_hash("5678", PASSWORD_DEFAULT);
    $rol = "Administrador";

    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, apellido_usuario, correo_usuario, contrasena_usuario, rol_usuario) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $apellido, $correo, $contrasena, $rol]);

    echo "Usuario administrador creado.";
?>