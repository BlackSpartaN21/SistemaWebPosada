<?php
   
    require_once '../config/db.php';
    require_once '../config/auth.php';


    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../views/login.php');
        exit;
    }


    $correo = trim($_POST['correo'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');


    if ($correo === '' || $contrasena === '') {
        header('Location: ../views/login.php?error=Campos%20requeridos');
        exit;
    }


    try {
        $stmt = $pdo->prepare('SELECT id_usuario, nombre_usuario, apellido_usuario, correo_usuario, contrasena_usuario, rol_usuario FROM usuarios WHERE correo_usuario = :correo LIMIT 1');
        $stmt->execute([':correo' => $correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($usuario && password_verify($contrasena, $usuario['contrasena_usuario'])) {
            // Regenerar ID de sesión para evitar fijación de sesión
            session_regenerate_id(true);


            $_SESSION['id_usuario'] = (int)$usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre_usuario'];
            $_SESSION['apellido'] = $usuario['apellido_usuario'];
            $_SESSION['correo'] = $usuario['correo_usuario'];
            $_SESSION['rol'] = $usuario['rol_usuario']; // 'Administrador' o 'Recepcionista'


            header('Location: ../views/recepcion.php');
            exit;
        }


        header('Location: ../views/login.php?error=Correo%20o%20contraseña%20incorrectos');
        exit;


    } catch (PDOException $e) {
    // En producción podrías loguear $e->getMessage()
    header('Location: ../views/login.php?error=Error%20en%20la%20base%20de%20datos');
    exit;
    }
?>
