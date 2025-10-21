<?php
include '../config/db.php'; // Conexión a la base de datos

// Si se envió el formulario, procesamos la inserción en la base de datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_documento = $_POST['tipo_documento_cliente'];
    $documento = $_POST['documento_cliente'];
    $nombres = $_POST['nombres_cliente'];
    $apellidos = $_POST['apellidos_cliente'];
    $telefono = $_POST['telefono_cliente'];
    $correo = $_POST['correo_cliente'];
    $descripcion = $_POST['descripcion_cliente'];

    try {
        $query = "INSERT INTO clientes (tipo_documento_cliente, documento_cliente, nombres_cliente, apellidos_cliente, telefono_cliente, correo_cliente, descripcion_cliente) 
                  VALUES (:tipo_documento, :documento, :nombres, :apellidos, :telefono, :correo, :descripcion)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':tipo_documento' => $tipo_documento,
            ':documento' => $documento,
            ':nombres' => $nombres,
            ':apellidos' => $apellidos,
            ':telefono' => $telefono,
            ':correo' => $correo,
            ':descripcion' => $descripcion
        ]);

        echo "<script>alert('Cliente registrado con éxito.'); window.location.href='../views/recepcion.php';</script>";
    } catch (PDOException $e) {
        die("Error al registrar cliente: " . $e->getMessage());
    }
}
?>