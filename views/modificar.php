<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
include '../views/header.php';
require_once '../config/db.php'; // Archivo de conexión a la base de datos
require_once '../controllers/cliente.php'; // Controlador de clientes

$clienteController = new ClienteController();
$clientes = $clienteController->obtenerClientes(); // Obtener todos los clientes de la base de datos
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Clientes</title>
    <link rel="stylesheet" href="../public/css/datatables.min.css">
    <link rel="stylesheet" href="../public/css/styletabla.css"> <!-- Asegúrate de que este archivo CSS está bien vinculado -->
    <link rel="stylesheet" href="../public/css/all.css"> <!-- Ruta a tu archivo CSS local -->
</head>
<body>
    <div class="container-fluid mt-4">
        <h2 class="text-center mb-4">Lista de Clientes</h2>
        <!-- Añadir un ID a la tabla para que DataTables la reconozca -->
        <table id="clientesTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Tipo Documento</th>
                    <th>Nro. Documento</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Descripción</th>
                    <th>Fecha de Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?php echo $cliente['tipo_documento_cliente']; ?></td>
                        <td><?php echo $cliente['documento_cliente']; ?></td>
                        <td><?php echo $cliente['nombres_cliente']; ?></td>
                        <td><?php echo $cliente['apellidos_cliente']; ?></td>
                        <td><?php echo $cliente['correo_cliente']; ?></td>
                        <td><?php echo $cliente['telefono_cliente']; ?></td>
                        <td><?php echo $cliente['descripcion_cliente']; ?></td>
                        <td><?php echo date('d-m-Y h:i A', strtotime($cliente['fecha_creacion_cliente'])) ; ?></td>
                        <td>
                            
    <a href="editar.php?id=<?php echo $cliente['documento_cliente']; ?>" class="btn btn-warning btn-sm">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    <a href="eliminar.php?id=<?php echo $cliente['documento_cliente']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este cliente?');">
        <i class="fas fa-trash-alt"></i> <!-- Icono de eliminar -->
    </a>
</td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

<!-- jQuery y DataTables -->
<script src="../public/js/jquery-3.7.1.min.js"></script>
<script src="../public/js/datatables.min.js"></script>

<script>
    $(document).ready(function () {
        $('#clientesTable').DataTable({
            language: {
                url: '../public/js/es-ES.json'
            },
            responsive: true
        });
    });
</script>

</html>
