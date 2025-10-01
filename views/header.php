<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Posada Las Mandarinas</title>
    <link href="../public/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/css/sweetalert2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../public/css/all.css">

    <style>

        .form-label i {
    margin-right: 6px;
    color:rgb(0, 0, 0);
}

        body {
            background-image: url('../public/img/wallpaper3.png'); /* Ajusta la ruta según tu estructura */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        .navbar {
            background-color: white !important;
            border-bottom: 3px solid #BA3B0A;
            padding-left: 20px; /* Añadido padding izquierdo */
            padding-right: 20px; /* Añadido padding derecho */
        }
        .navbar-brand {
            color: #BA3B0A !important;
            font-weight: bold;
            font-size: 24px; /* Aumentado tamaño de letra */
        }
        .nav-link {
            color: rgb(0, 0, 0) !important;
            font-weight: 500;
            font-size: 18px; /* Aumentado tamaño de letra */
            margin-right: 15px; /* Separación entre los enlaces */
        }
        .nav-link:hover {
            color: #922E08 !important; /* Tono más oscuro al pasar el cursor */
        }
        .dropdown-menu {
            background-color: white;
            border: 1px solid #BA3B0A;
        }
        .dropdown-item {
            color: rgb(0, 0, 0) !important;
            font-size: 16px; /* Tamaño de letra más grande para los ítems del dropdown */
        }
        .dropdown-item:hover {
            background-color: #FFE6E0 !important;
        }
        .navbar-toggler {
            border-color: #BA3B0A !important;
        }
        .navbar-toggler-icon {
            background-color: #BA3B0A !important;
            width: 24px;
            height: 24px;
        }
        /* Estilo personalizado para la hamburguesa */
        @media (max-width: 991px) {
            .navbar-toggler {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
        }
            .navbar-toggler-icon {
                background-color: #BA3B0A !important;
            }
        }
        /* Espaciado adicional entre los ítems del menú */
        .navbar-nav .nav-item {
            padding-right: 20px; /* Espaciado a la derecha de cada ítem */
        }

        /* Aumentar la separación entre los módulos y el logo */
        .navbar-nav {
            margin-left: 55px; /* Separación mayor entre los módulos y el logo */
        }

        /* Borde alrededor de la imagen del usuario */
        .navbar-nav .nav-item img {
            border: 3px solid #BA3B0A; /* Borde con el color de la marca */
            padding: 2px; /* Espaciado interno para que el borde no toque la imagen */
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="../public/img/logoPosadaRecortada.png" alt="Logo" width="200" height="75" class="d-inline-block align-text-top">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto"> <!-- Alineación a la izquierda de todos los módulos -->
                    <li class="nav-item">
                        <a class="nav-link" href="recepcion.php">Recepción</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="clientesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Clientes
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="clientesDropdown">
                            <li><a class="dropdown-item" href="./registrar.php" data-bs-toggle="modal" data-bs-target="#clienteModal">Registrar</a></li>
                            <li><a class="dropdown-item" href="modificar.php">Modificar</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reportes.php">Reportes</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Herramientas Administrativas
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="gestionar_habitaciones.php">Gestionar Habitación</a></li>
                            <li><a class="dropdown-item" href="gestionar_usuarios.php">Gestionar Usuarios</a></li>
                        </ul>
                    </li>
                </ul>
                <!-- Avatar y Dropdown para usuario alineado a la derecha -->
                <ul class="navbar-nav ms-auto"> <!-- Esto alinea el módulo del usuario a la derecha -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="../public/img/imglogin.jpg" alt="User Avatar" class="rounded-circle" width="60" height="60">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="perfil.php">Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="perfil.php">Manual de Usuario</a></li>
                            <li><a class="dropdown-item" href="perfil.php">Copia de Seguridad</a></li>
                            <li><a class="dropdown-item" href="../controllers/logout.php">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Modal de Registro de Cliente -->
    <div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clienteModalLabel">Datos del Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="clienteForm" method="POST" action="../controllers/registrar.php">
<div class="mb-3">
    <label for="tipo_documento_cliente" class="form-label">
        <i class="fas fa-id-card"></i> Tipo de Documento
    </label>
    <select class="form-select" id="tipo_documento_cliente" name="tipo_documento_cliente" required>
        <option value="V">V</option>
        <option value="E">E</option>
        <option value="P">P</option>
        <option value="J">J</option>
    </select>
</div>

<div class="mb-3">
    <label for="documento_cliente" class="form-label">
        <i class="fas fa-id-badge"></i> Documento
    </label>
    <input type="text" class="form-control" id="documento_cliente" name="documento_cliente" maxlength="10" required>
    <div class="invalid-feedback">Debe contener solo números (máx. 10).</div>
</div>

<div class="mb-3">
    <label for="nombres_cliente" class="form-label">
        <i class="fas fa-user"></i> Nombres
    </label>
    <input type="text" class="form-control" id="nombres_cliente" name="nombres_cliente" maxlength="50" required>
    <div class="invalid-feedback">Solo letras y espacios (máx. 50).</div>
</div>

<div class="mb-3">
    <label for="apellidos_cliente" class="form-label">
        <i class="fas fa-user-tag"></i> Apellidos
    </label>
    <input type="text" class="form-control" id="apellidos_cliente" name="apellidos_cliente" maxlength="50" required>
    <div class="invalid-feedback">Solo letras y espacios (máx. 50).</div>
</div>

<div class="mb-3">
    <label for="telefono_cliente" class="form-label">
        <i class="fas fa-phone"></i> Teléfono
    </label>
    <input type="text" class="form-control" id="telefono_cliente" name="telefono_cliente" maxlength="11" required>
    <div class="invalid-feedback">Debe contener exactamente 11 caracteres.</div>
</div>

<div class="mb-3">
    <label for="correo_cliente" class="form-label">
        <i class="fas fa-envelope"></i> Correo Electrónico
    </label>
    <input type="email" class="form-control" id="correo_cliente" name="correo_cliente" required>
    <div class="invalid-feedback">Ingrese un correo válido.</div>
</div>

<div class="mb-3">
    <label for="descripcion_cliente" class="form-label">
        <i class="fas fa-align-left"></i> Descripción (Opcional)
    </label>
    <textarea class="form-control" id="descripcion_cliente" name="descripcion_cliente" rows="3" maxlength="100"></textarea>
    <div class="invalid-feedback">Máximo 100 caracteres.</div>
</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-success">
        <i class="fas fa-save"></i> Guardar
    </button>
    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
        <i class="fas fa-times"></i> Cancelar
    </button>
</div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../public/js/validacion.js"></script>
    <script src="../public/js/bootstrap.bundle.min.js"></script>
    <script src="../public/js/sweetalert2.min.js"></script>



</body>
</html>
